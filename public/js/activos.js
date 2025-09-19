// public/js/activos.js
// Requiere: Bootstrap, SweetAlert2, rutas.js (BASE_URL_API)

document.addEventListener('DOMContentLoaded', () => {
  const cardsContainer = document.querySelector('#vehicleCardsContainer .row');
  const searchInput = document.getElementById('searchInput');
  const cobroModalEl = document.getElementById('cobroModal');
  const cobroModal = new bootstrap.Modal(cobroModalEl);
  const cobroForm = document.getElementById('cobroForm');

  window.vehiculoData = {};

  // ===== Helpers =====
  const pad2 = (n) => String(n).padStart(2, '0');
  const toLocalInputValue = (d) => `${d.getFullYear()}-${pad2(d.getMonth()+1)}-${pad2(d.getDate())}T${pad2(d.getHours())}:${pad2(d.getMinutes())}`;
  const parseInputDate = (val) => val ? new Date(val) : null;
  const formatDateHuman = (s) => s ? new Date(s).toLocaleString('es-MX', {dateStyle:'medium', timeStyle:'short'}) : '--';
  const humanDiff = (a,b)=>{const ms=Math.max(0,b-a);const m=Math.floor(ms/60000);const h=Math.floor(m/60);return `${h} h ${pad2(m%60)} min`;};
  const addMinutes=(d,m)=>new Date(d.getTime()+m*60000);
  const setTime=(d,hh,mm,ss=0)=>{const x=new Date(d);x.setHours(hh,mm,ss,0);return x;};
  const ymd=(d)=>`${d.getFullYear()}-${pad2(d.getMonth()+1)}-${pad2(d.getDate())}`;
  const formatHM=(mins)=>{const h=Math.floor((mins||0)/60),m=Math.abs((mins||0)%60);return `${h} h ${pad2(m)} min`;};

  const calcularCostoPolitica = (minutos) => {
    if (minutos <= 3) return 0;
    let costo = 20;
    let rem = minutos - 60;
    while (rem > 0) { if (rem > 10) costo += 20; rem -= 60; }
    return costo;
  };
  const construirCierreMismoDia = (fechaEntrada, horaCierreStr) => {
    if (!horaCierreStr || horaCierreStr === 'Cerrado') return null;
    const [hh,mm,ss='0'] = horaCierreStr.split(':').map(Number);
    const cierre = new Date(fechaEntrada);
    cierre.setHours(hh||0, mm||0, ss||0, 0);
    return cierre;
  };
  const parseJSONSafe = async (resp) => {
    const raw = await resp.text();
    const cleaned = raw.replace(/^\uFEFF/, '').trim();
    try { return JSON.parse(cleaned); }
    catch(e){ console.error('Respuesta no-JSON:', cleaned); Swal.fire('Error','Respuesta inválida del servidor.','error'); return null; }
  };

  // ===== Cards =====
  const renderVehicleCard = (v) => {
    const html = `
      <div class="col-md-6 col-lg-4 mb-4 vehicle-card-item" data-plate="${v.placa}">
        <div class="card vehicle-card">
          <div class="card-header bg-primary text-white">
            <h5 class="card-title m-0">${v.placa}</h5>
          </div>
          <div class="card-body">
            <p class="info-item"><i class="fa-solid fa-car-side me-1"></i> Tipo: <span class="fw-bold">${v.tipo || '--'}</span></p>
            <p class="info-item"><i class="fa-solid fa-industry me-1"></i> Marca: <span class="fw-bold">${v.marca || '--'}</span></p>
            <p class="info-item"><i class="fa-solid fa-palette me-1"></i> Color: <span class="fw-bold">${v.color || '--'}</span></p>
            <p class="info-item"><i class="fa-regular fa-clock me-1"></i> Entrada: <span class="fw-bold">${formatDateHuman(v.fecha_entrada)}</span></p>
            <hr>
            <button class="btn btn-success btn-sm w-100 btn-salida" data-entrada-id="${v.id}">
              <i class="fa-solid fa-arrow-right-from-bracket me-2"></i>Dar Salida
            </button>
          </div>
        </div>
      </div>`;
    cardsContainer.insertAdjacentHTML('beforeend', html);
  };

  const cargarVehiculosActivos = async () => {
    try {
      const resp = await fetch(`${BASE_URL_API}/index.php?action=entradas/obtenerActivas`);
      const result = await parseJSONSafe(resp);
      cardsContainer.innerHTML = '';
      if (result && result.success && result.data.length) {
        result.data.forEach(renderVehicleCard);
        attachButtonEvents();
      } else {
        cardsContainer.innerHTML = '<div class="text-center w-100 p-5">No hay vehículos en el estacionamiento.</div>';
      }
    } catch (e) {
      console.error(e);
      cardsContainer.innerHTML = '<div class="text-center w-100 p-5 text-danger">Error al conectar con la API.</div>';
    }
  };

  // ===== Multi-día (modo Normal) =====
  function calcularCargosMultiDia(fe, fs, horarioSemana, opts={toleranciaMin:30}) {
    const out = { dias: [], noches: 0, totalDiurno: 0, totalNoches: 0, total: 0 };
    if (fs <= fe) return out;

    let veniaDeNoche = false;
    let cursor = new Date(fe); cursor.setHours(0,0,0,0);
    const lastDay = new Date(fs); lastDay.setHours(0,0,0,0);

    while (cursor <= lastDay) {
      const dow = cursor.getDay();
      const h = horarioSemana?.[String(dow)] || horarioSemana?.[dow] || {abierto:false};
      const abierto = !!h.abierto;
      const apertura = abierto && h.apertura ? h.apertura.substring(0,5) : null;
      const cierre   = abierto && h.cierre   ? h.cierre.substring(0,5)   : null;

      const diaTxt = ymd(cursor);
      const aperturaDT = apertura ? setTime(cursor, +apertura.split(':')[0], +apertura.split(':')[1]) : null;
      const cierreDT   = cierre   ? setTime(cursor, +cierre.split(':')[0],   +cierre.split(':')[1])   : null;

      const tramoIni = new Date(Math.max((aperturaDT||fe).getTime(), fe.getTime(), cursor.getTime()));
      const diaEndNatural = setTime(cursor, 23, 59, 59);
      const tramoFin = new Date(Math.min((cierreDT||fs).getTime(), fs.getTime(), diaEndNatural.getTime()));

      let usadoMin = 0, costoDiurno = 0, pensionNoche = 0;

      if (abierto && aperturaDT && cierreDT && tramoFin > tramoIni) {
        let inicioCobro = new Date(tramoIni);
        if (veniaDeNoche && opts.toleranciaMin > 0) {
          const finTol = addMinutes(aperturaDT, opts.toleranciaMin);
          if (inicioCobro < finTol) inicioCobro = new Date(Math.min(finTol.getTime(), tramoFin.getTime()));
        }
        if (tramoFin > inicioCobro) {
          usadoMin = Math.floor((tramoFin - inicioCobro)/60000);
          costoDiurno = calcularCostoPolitica(usadoMin);
        }
      }

      if (abierto && cierreDT && fs > cierreDT) { pensionNoche = 100; veniaDeNoche = true; }
      else { veniaDeNoche = veniaDeNoche && !abierto; }

      const totalDia = costoDiurno + pensionNoche;

      out.dias.push({
        fecha: diaTxt,
        ventana: (abierto && apertura && cierre) ? `${apertura}→${cierre}` : 'Cerrado',
        usadoMin, costoDiurno, pension: pensionNoche, totalDia
      });

      out.noches += pensionNoche ? 1 : 0;
      out.totalDiurno += costoDiurno;
      out.totalNoches += pensionNoche;
      out.total += totalDia;

      cursor = addMinutes(setTime(cursor, 0,0,0), 24*60);
    }

    return out;
  }

  // ===== Helpers de tabla (encabezado dinámico) =====
  function setTableHeader(cols) {
    const thead = document.querySelector('#desglose-container thead');
    if (!thead) return;
    thead.innerHTML = `<tr>${cols.map(c=>`<th>${c}</th>`).join('')}</tr>`;
  }

  // ===== Desglose por modo =====
  function renderDesgloseNormal(bd) {
    setTableHeader(['Fecha','Ventana (apertura→cierre)','Usado','Costo diurno','Pensión nocturna','Total día']);
    const tbody = document.getElementById('desglose-body');
    const nochesEl = document.getElementById('desglose-noches');
    const totalEl  = document.getElementById('desglose-total');
    if (!tbody || !nochesEl || !totalEl) return;

    tbody.innerHTML = '';
    bd.dias.forEach(d => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${d.fecha}</td>
        <td>${d.ventana}</td>
        <td>${formatHM(d.usadoMin)}</td>
        <td>$${d.costoDiurno.toFixed(2)}</td>
        <td>${d.pension ? '$100.00' : '$0.00'}</td>
        <td class="fw-bold">$${d.totalDia.toFixed(2)}</td>
      `;
      tbody.appendChild(tr);
    });
    nochesEl.textContent = `${bd.noches} ($${(bd.totalNoches || 0).toFixed(2)})`;
    totalEl.textContent  = `$${(bd.total || 0).toFixed(2)}`;
  }

  function renderDesglosePensionTiempo(fe, horarioSemana) {
    // Calcula: tiempo desde ENTRADA hasta CIERRE del mismo día + $100
    const tbody = document.getElementById('desglose-body');
    const nochesEl = document.getElementById('desglose-noches');
    const totalEl  = document.getElementById('desglose-total');
    if (!tbody || !nochesEl || !totalEl) return;

    setTableHeader(['Fecha','Tramo cobrado','Usado','Costo diurno','Pensión','Total']);
    tbody.innerHTML = '';

    const horaCierre = vehiculoData.hora_cierre;
    if (!horaCierre || horaCierre === 'Cerrado') {
      tbody.innerHTML = `<tr><td colspan="6" class="text-center text-danger">No se encontró hora de cierre para ese día.</td></tr>`;
      nochesEl.textContent = `0 ($0.00)`; totalEl.textContent = `$0.00`; return;
    }
    const feDate = new Date(fe);
    const cierre = construirCierreMismoDia(feDate, horaCierre);
    if (!cierre || feDate > cierre) {
      tbody.innerHTML = `<tr><td colspan="6" class="text-center text-warning">Entrada mayor al cierre del día.</td></tr>`;
      nochesEl.textContent = `0 ($0.00)`; totalEl.textContent = `$0.00`; return;
    }

    const mins = Math.max(0, Math.floor((cierre - feDate)/60000));
    const costo = calcularCostoPolitica(mins);
    const total = costo + 100;

    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${ymd(feDate)}</td>
      <td>${feDate.toLocaleTimeString('es-MX',{hour:'2-digit',minute:'2-digit'})} → ${cierre.toLocaleTimeString('es-MX',{hour:'2-digit',minute:'2-digit'})}</td>
      <td>${formatHM(mins)}</td>
      <td>$${costo.toFixed(2)}</td>
      <td>$100.00</td>
      <td class="fw-bold">$${total.toFixed(2)}</td>
    `;
    tbody.appendChild(tr);

    nochesEl.textContent = `1 ($100.00)`;
    totalEl.textContent  = `$${total.toFixed(2)}`;
  }

  function renderDesgloseSoloPension(bd, noches) {
    // Muestra una fila por noche
    setTableHeader(['Fecha/Noche','Concepto','Pensión','Total']);
    const tbody = document.getElementById('desglose-body');
    const nochesEl = document.getElementById('desglose-noches');
    const totalEl  = document.getElementById('desglose-total');
    if (!tbody || !nochesEl || !totalEl) return;

    tbody.innerHTML = '';
    const sugeridas = Math.max(0, bd.noches || 0);
    const n = Math.max(1, noches);

    for (let i = 0; i < n; i++) {
      const label = (i < sugeridas)
        ? `Noche ${i+1} (detectada)`
        : `Noche ${i+1} (ajuste manual)`;
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${label}</td>
        <td>Pensión nocturna</td>
        <td>$100.00</td>
        <td class="fw-bold">$100.00</td>
      `;
      tbody.appendChild(tr);
    }

    const total = 100 * n;
    nochesEl.textContent = `${n} ($${(100*n).toFixed(2)})`;
    totalEl.textContent  = `$${total.toFixed(2)}`;
  }

  // ===== Abrir modal =====
  function attachButtonEvents() {
    document.querySelectorAll('.btn-salida').forEach(btn => {
      btn.addEventListener('click', async () => {
        const entradaId = btn.getAttribute('data-entrada-id');
        try {
          const r = await fetch(`${BASE_URL_API}/index.php?action=salidas/obtenerDetalles`, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ id: entradaId })
          });
          const data = await parseJSONSafe(r);
          if (!data || !data.success) {
            Swal.fire('Error', (data && data.message) || 'No se pudo obtener detalles.', 'error'); return;
          }
          window.vehiculoData = data.data;

          // Rellena UI
          document.getElementById('modal-placa').textContent = vehiculoData.placa || '--';
          document.getElementById('modal-tipo').textContent  = vehiculoData.tipo || '--';
          document.getElementById('modal-marca').textContent = vehiculoData.marca || '--';
          document.getElementById('modal-color').textContent = vehiculoData.color || '--';
          const horarioTxt = (vehiculoData.hora_apertura && vehiculoData.hora_cierre) ? `${vehiculoData.hora_apertura} - ${vehiculoData.hora_cierre}` : '--';
          document.getElementById('modal-horario-dia').textContent = horarioTxt;

          const entradaISO = new Date(vehiculoData.fecha_entrada);
          const salidaISO  = new Date(); // ahora
          document.getElementById('input-entrada').value = toLocalInputValue(entradaISO);
          document.getElementById('input-salida').value  = toLocalInputValue(salidaISO);

          // Reset controles
          document.getElementById('boleto_perdido_check').checked = false;
          document.getElementById('tolerancia_check').checked = true;
          document.getElementById('mode_normal').checked = true;
          document.getElementById('soloPensionNoches').value = 1;
          document.getElementById('soloPensionNoches').disabled = true;

          // Listeners
          const tol = document.getElementById('tolerancia_check');
          tol.oninput = ()=>{ recalcularTiempo(); recalcularCosto(); };

          ['input-entrada','input-salida','boleto_perdido_check'].forEach(id=>{
            const el=document.getElementById(id);
            el && el.addEventListener('input', ()=>{recalcularTiempo(); recalcularCosto();});
          });
          // modos
          document.querySelectorAll('input[name="cobro_mode"]').forEach(r=>{
            r.onchange = ()=>{
              const spn = document.getElementById('soloPensionNoches');
              spn.disabled = (r.value!=='solo_pension');
              recalcularTiempo(); recalcularCosto();
            };
          });
          document.getElementById('soloPensionNoches').addEventListener('input', ()=>{
            const el = document.getElementById('soloPensionNoches');
            if (parseInt(el.value||'0',10)<1) el.value=1;
            recalcularCosto();
          });

          // Primer cálculo
          recalcularTiempo();
          recalcularCosto();

          cobroModal.show();
        } catch (e) {
          console.error(e);
          Swal.fire('Error', 'No se pudo conectar con el servidor.', 'error');
        }
      });
    });
  }

  // ===== Recalcular tiempo =====
  function recalcularTiempo() {
    const fe = parseInputDate(document.getElementById('input-entrada')?.value);
    const fs = parseInputDate(document.getElementById('input-salida')?.value) || new Date();
    if (!fe) return;
    document.getElementById('modal-tiempo').textContent = humanDiff(fe, fs);
  }

  // ===== Recalcular costo según modo (y pintar tabla acorde) =====
  function recalcularCosto() {
    try{
      if(!vehiculoData || !vehiculoData.id) return;

      const elEntrada = document.getElementById('input-entrada');
      const elSalida  = document.getElementById('input-salida');
      const elCosto   = document.getElementById('modal-costo');
      const labelTot  = document.getElementById('total-mode-label');
      if(!elEntrada||!elSalida||!elCosto||!labelTot) return;

      const fe = new Date(elEntrada.value);
      let fs   = new Date(elSalida.value);
      if(!fe||!fs) return;

      const lostTicketChecked = !!document.getElementById('boleto_perdido_check')?.checked;
      const toleranciaOn      = !!document.getElementById('tolerancia_check')?.checked;
      const horarioSemana     = vehiculoData.horario_semana || {};
      const mode = document.querySelector('input[name="cobro_mode"]:checked')?.value || 'normal';

      // Siempre calculamos breakdown normal (sirve para sugerencias y “solo pensión”)
      const breakdown = calcularCargosMultiDia(fe, fs, horarioSemana, {toleranciaMin: toleranciaOn?30:0});

      let total = 0;
      let modoTexto = 'Total a Pagar';

      if (mode === 'normal') {
        renderDesgloseNormal(breakdown);
        total = breakdown.total;
        modoTexto = 'Total a Pagar — Modo Normal';

      } else if (mode === 'pension_tiempo') {
        // Sincroniza salida a cierre solo para visual (la tabla lo muestra claro)
        const horaCierre = vehiculoData.hora_cierre;
        if (horaCierre && horaCierre !== 'Cerrado') {
          const cierre = construirCierreMismoDia(fe, horaCierre);
          if (cierre && fe <= cierre) {
            document.getElementById('input-salida').value = toLocalInputValue(cierre);
            fs = cierre;
          }
        }
        renderDesglosePensionTiempo(fe, horarioSemana);

        const cierreCalc = construirCierreMismoDia(fe, vehiculoData.hora_cierre);
        if (!cierreCalc || fe > cierreCalc) { total = 0; modoTexto = 'Total a Pagar — Pensión (incompleto)'; }
        else {
          const mins = Math.max(0, Math.floor((cierreCalc - fe)/60000));
          total = calcularCostoPolitica(mins) + 100;
          modoTexto = 'Total a Pagar — Pensión (tiempo + $100)';
        }

      } else if (mode === 'solo_pension') {
        // Si el usuario no puso noches, sugerimos las detectadas (al menos 1)
        const nochesInput = document.getElementById('soloPensionNoches');
        if (nochesInput && (nochesInput.value === '' || Number(nochesInput.value) < 1)) {
          nochesInput.value = Math.max(1, breakdown.noches || 1);
        }
        const n = Math.max(1, parseInt(document.getElementById('soloPensionNoches').value, 10));
        renderDesgloseSoloPension(breakdown, n);
        total = 100 * n;
        modoTexto = `Total a Pagar — Solo pensión (${n} noche${n>1?'s':''})`;
      }

      // Boleto perdido al final (misma política)
      if (lostTicketChecked) {
        if (total <= 100) total = 100; else total = total + 50;
      }

      labelTot.textContent = modoTexto;
      elCosto.textContent = `$${(total||0).toFixed(2)}`;
      vehiculoData.cobro_final = total||0;
      vehiculoData.es_pension_flag = (mode!=='normal') ? 1 : (breakdown.noches>0?1:0);

    }catch(e){
      console.error('Error en recalcularCosto:', e);
    }
  }

  // ===== Buscar por placa =====
  searchInput?.addEventListener('input', (e) => {
    const q = e.target.value.toLowerCase().trim();
    document.querySelectorAll('.vehicle-card-item').forEach(card => {
      const plate = (card.getAttribute('data-plate') || '').toLowerCase();
      card.style.display = plate.includes(q) ? '' : 'none';
    });
  });

  // ===== Submit =====
  cobroForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    if (!vehiculoData?.id) return;

    const entradaOverride = document.getElementById('input-entrada')?.value || null;
    const salidaOverride  = document.getElementById('input-salida')?.value || null;

    const payload = {
      entrada_id: vehiculoData.id,
      cobro: vehiculoData.cobro_final,
      boleto_perdido: 0,
      es_pension: vehiculoData.es_pension_flag,
      placa: vehiculoData.placa || null,
      vehiculos_id: vehiculoData.vehiculos_id || vehiculoData.vehiculo_id || null,
      fecha_entrada_override: entradaOverride,
      fecha_salida_override:  salidaOverride
    };

    try {
      const r = await fetch(`${BASE_URL_API}/index.php?action=salidas/registrar`, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(payload)
      });
      const data = await parseJSONSafe(r);
      if (data && data.success) {
        Swal.fire({icon:'success', title:'¡Salida registrada!', timer:1200, showConfirmButton:false})
          .then(()=>{ cobroModal.hide(); cargarVehiculosActivos(); });
      } else {
        Swal.fire('Error', (data && data.message) || 'No se pudo registrar la salida', 'error');
      }
    } catch (e) {
      console.error(e);
      Swal.fire('Error','No se pudo conectar con el servidor.','error');
    }
  });

  // ===== Carga inicial =====
  cargarVehiculosActivos();
});
