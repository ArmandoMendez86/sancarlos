// public/js/cobro_scan.js
// Requiere: Bootstrap 5, SweetAlert2, y rutas.js (debe exponer BASE_URL_API)

document.addEventListener('DOMContentLoaded', () => {
    // ====== Referencias UI ======
    const folioInput = document.getElementById('folioInput');
    const btnBuscar = document.getElementById('btnBuscarFolio');

    const cobroModalEl = document.getElementById('cobroModal');
    const cobroModal = new bootstrap.Modal(cobroModalEl);
    const cobroForm = document.getElementById('cobroForm');

    // Datos del vehículo/detalle activo mientras está abierto el modal
    window.vehiculoData = window.vehiculoData || {};

    // ====== Helpers de formato y fechas ======
    const pad2 = (n) => String(n).padStart(2, '0');
    const toLocalInputValue = (d) => `${d.getFullYear()}-${pad2(d.getMonth() + 1)}-${pad2(d.getDate())}T${pad2(d.getHours())}:${pad2(d.getMinutes())}`;
    const parseInputDate = (val) => val ? new Date(val) : null;

    // Convierte "HH:mm[:ss]" o Date en Date anclado al día de baseDate
    const ensureDateFromTime = (baseDate, timeOrDate, fbH = 9, fbM = 0) => {
        const d = new Date(baseDate);
        if (!timeOrDate) { d.setHours(fbH, fbM, 0, 0); return d; }
        if (Object.prototype.toString.call(timeOrDate) === '[object Date]') {
            d.setHours(timeOrDate.getHours(), timeOrDate.getMinutes(), timeOrDate.getSeconds() || 0, 0);
            return d;
        }
        const p = String(timeOrDate).split(':').map(x => parseInt(x, 10) || 0);
        d.setHours(p[0] ?? fbH, p[1] ?? fbM, p[2] ?? 0, 0);
        return d;
    };

    const humanDiffMin = (a, b) => Math.max(0, Math.floor((b - a) / 60000));
    const ceilHours = (mins) => Math.ceil(Math.max(0, mins) / 60);

    // MISMA LÓGICA DE COBRO que usas en Vehículos Activos (ajusta si tu activo usa otra tarifa)
    const calcDiurno = (min) => {
        // Tolerancia 3 min, 1ra hora $20, cada hora o fracción $20
        if (!Number.isFinite(min) || min <= 3) return 0;
        let costo = 20;     // primera hora
        let rem = min - 60; // minutos restantes
        while (rem > 0) { costo += 20; rem -= 60; }
        return costo;
    };

    const parseJSONSafe = async (resp) => { try { return await resp.json(); } catch { return null; } };

    // ====== Recalcular costo (centralizado) ======
    async function recalcularCosto() {
        try {
            const entradaInput = document.getElementById('input-entrada');
            const salidaInput = document.getElementById('input-salida');

            // Fechas de UI (después pueden ser ajustadas por la lógica)
            let entrada = parseInputDate(entradaInput?.value);
            let salida = parseInputDate(salidaInput?.value) || new Date();

            const lostTicketChecked = document.getElementById('boleto_perdido_check')?.checked === true;

            // Config entregada por backend (apertura/cierre del estacionamiento)
            const cfg = (vehiculoData && vehiculoData.config) ? vehiculoData.config : {};

            // Día base para SALIDA: apertura + 30min como inicio de cobro en pensión
            const baseSalida = new Date(salida.getFullYear(), salida.getMonth(), salida.getDate());
            const aperturaSalida = ensureDateFromTime(baseSalida, cfg.apertura, 9, 0);
            const limiteTolerancia = new Date(aperturaSalida.getTime() + 30 * 60000);

            // Día base para ENTRADA: para detectar cruce de noche cuando no viene bandera
            const baseEntrada = entrada ? new Date(entrada.getFullYear(), entrada.getMonth(), entrada.getDate()) : new Date(salida);
            const cierreEntrada = ensureDateFromTime(baseEntrada, cfg.cierre, 22, 0);

            // Derivar pensión si no viene salidas.es_pension y cruzó cierre
            let cruzoNoche = false;
            if (entrada && cierreEntrada && salida > cierreEntrada) cruzoNoche = true;

            const esPensionFlag = (Number(vehiculoData?.es_pension) === 1) ? true : (cruzoNoche ? true : false);

            // === Cálculo ===
            let total = 0;
            let horas_extras = 0;
            let cobro_extra = 0;
            let modoTexto = 'Total a Pagar';

            if (esPensionFlag) {
                // ----- PENSIÓN NOCTURNA -----
                // Regla: el cobro inicia desde (apertura del día de salida + 30 min).
                // Si el cliente llega antes o dentro de esa tolerancia => $0.
                if (salida <= limiteTolerancia) {
                    horas_extras = 0;
                    cobro_extra = 0;
                    total = 0;
                    modoTexto = 'Pensión — dentro de tolerancia';
                } else {
                    const inicioCobro = limiteTolerancia;
                    const mins = humanDiffMin(inicioCobro, salida);
                    horas_extras = ceilHours(mins);   // para salidas.horas_extras
                    cobro_extra = calcDiurno(mins);  // para salidas.cobro_extra
                    total = cobro_extra;              // lo cobrado hoy es el EXTRA
                    modoTexto = 'Pensión — desde apertura + 30 min';
                }

                // Reflejar en UI el punto de partida real (apertura+30 del día de salida)
                if (entradaInput) entradaInput.value = toLocalInputValue(limiteTolerancia);
                if (salidaInput) salidaInput.value = toLocalInputValue(salida);

            } else {
                // ----- NO ES PENSIÓN -----
                // MISMA LÓGICA DE ACTIVOS: de entrada -> salida + recargo por noches cruzadas
                const minsTotales = entrada ? humanDiffMin(entrada, salida) : 0;
                total = calcDiurno(minsTotales);

                let noches = 0;
                if (cierreEntrada && salida > cierreEntrada) {
                    let cursor = new Date(cierreEntrada);
                    while (salida > cursor) { noches++; cursor.setDate(cursor.getDate() + 1); }
                }
                if (noches > 0) total += (noches * 100); // ajusta monto si tu activo usa otro valor

                horas_extras = 0;
                cobro_extra = 0;
                modoTexto = `Normal — ${noches} noche${noches > 1 ? 's' : ''}`;
            }

            // Boleto perdido (regla: mínimo $100; si ya supera 100, +$50)
            if (lostTicketChecked) {
                if (total <= 100) total = 100; else total += 50;
            }

            // UI
            document.getElementById('label_total_modo').textContent = `Total a Pagar — ${modoTexto}`;
            document.getElementById('total_costo').textContent = `$${(total || 0).toFixed(2)}`;

            // Guardar para submit
            vehiculoData.cobro_final = total || 0;
            vehiculoData.es_pension_flag = esPensionFlag ? 1 : 0;
            vehiculoData.horas_extras = horas_extras;
            vehiculoData.cobro_extra = cobro_extra;

        } catch (e) {
            console.error('recalcularCosto()', e);
        }
    }

    // ====== Buscar por Folio (escáner externo) ======
    async function buscarPorFolio(folio) {
        if (!folio) return;

        try {
            // 1) Buscar entrada por folio
            const r1 = await fetch(`${BASE_URL_API}/index.php?action=entradas/buscarPorFolio`, {
                method: 'POST', headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ folio })
            });
            const j1 = await parseJSONSafe(r1);
            if (!j1 || !j1.success) {
                Swal.fire('No encontrado', (j1 && j1.message) || 'No se localizó el folio.', 'warning');
                return;
            }

            const entradaId = j1.data.entrada_id;

            // 2) Obtener detalles necesarios para la salida (mismo endpoint que usas en Activos)
            const r2 = await fetch(`${BASE_URL_API}/index.php?action=salidas/obtenerDetalles`, {
                method: 'POST', headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: entradaId })
            });
            const j2 = await parseJSONSafe(r2);
            if (!j2 || !j2.success) {
                Swal.fire('Error', (j2 && j2.message) || 'No se pudo obtener detalles.', 'error');
                return;
            }

            // 3) Cargar datos en memoria
            window.vehiculoData = j2.data || {};
            vehiculoData.config = vehiculoData.config || {};

            // 4) Pintar encabezados del modal
            document.getElementById('modal-placa').textContent = vehiculoData.placa || '--';
            document.getElementById('modal-tipo').textContent = vehiculoData.tipo || '--';
            document.getElementById('modal-marca').textContent = vehiculoData.marca || '--';
            document.getElementById('modal-color').textContent = vehiculoData.color || '--';

            // 5) Fechas por defecto en el modal
            const now = new Date();
            const cfg = vehiculoData.config;

            const baseSalida = new Date(now.getFullYear(), now.getMonth(), now.getDate());
            const aperturaSalida = ensureDateFromTime(baseSalida, cfg.apertura, 9, 0);
            const aperturaMas30 = new Date(aperturaSalida.getTime() + 30 * 60000);

            // Si viene marcado es_pension=1 o si backend ya lo derivó, fijar entrada = apertura+30; salida = ahora
            const esPension = Number(vehiculoData.es_pension) === 1;
            if (esPension) {
                document.getElementById('input-entrada').value = toLocalInputValue(aperturaMas30);
                document.getElementById('input-salida').value = toLocalInputValue(now);
            } else {
                // caso normal: muestra la fecha_entrada real y salida = ahora
                const entradaDate = vehiculoData.fecha_entrada ? new Date(vehiculoData.fecha_entrada) : now;
                document.getElementById('input-entrada').value = toLocalInputValue(entradaDate);
                document.getElementById('input-salida').value = toLocalInputValue(now);
            }

            // 6) Calcular y abrir modal
            await recalcularCosto();
            cobroModal.show();

        } catch (e) {
            console.error(e);
            Swal.fire('Error', 'No se pudo conectar con el servidor.', 'error');
        }
    }

    // ====== Eventos de UI ======
    const bindRecalc = (sel) => document.querySelector(sel)?.addEventListener('input', recalcularCosto);
    bindRecalc('#input-entrada'); bindRecalc('#input-salida');
    bindRecalc('#boleto_perdido_check'); bindRecalc('#tolerancia_check');
    ['mode_normal', 'mode_pension_tiempo', 'mode_pension_solo', 'input_monto_pension']
        .forEach(id => document.getElementById(id)?.addEventListener('change', recalcularCosto));

    // El escáner suele enviar Enter al final
    folioInput?.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            buscarPorFolio(folioInput.value.trim());
        }
    });
    btnBuscar?.addEventListener('click', () => buscarPorFolio(folioInput.value.trim()));

    // ====== Submit: registrar salida y cobro ======
    cobroForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (!vehiculoData?.id) return;

        const payload = {
            entrada_id: vehiculoData.id,
            cobro: vehiculoData.cobro_final,
            boleto_perdido: document.getElementById('boleto_perdido_check')?.checked ? 1 : 0,
            es_pension: vehiculoData.es_pension_flag,
            placa: vehiculoData.placa || null,
            vehiculos_id: vehiculoData.vehiculos_id || vehiculoData.vehiculo_id || null,
            fecha_entrada_override: document.getElementById('input-entrada')?.value || null,
            fecha_salida_override: document.getElementById('input-salida')?.value || null,

            // >>> Para tabla salidas <<<
            horas_extras: vehiculoData.horas_extras || 0,
            cobro_extra: vehiculoData.cobro_extra || 0,
            tipo_cobro: (vehiculoData.es_pension_flag ? 'pension' : 'normal')
        };

        try {
            const r = await fetch(`${BASE_URL_API}/index.php?action=salidas/registrar`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await parseJSONSafe(r);
            if (data && data.success) {
                Swal.fire({ icon: 'success', title: '¡Salida registrada!', timer: 1200, showConfirmButton: false })
                    .then(() => { cobroModal.hide(); folioInput.value = ''; folioInput.focus(); });
            } else {
                Swal.fire('Error', (data && data.message) || 'No se pudo registrar la salida', 'error');
            }
        } catch (err) {
            console.error(err);
            Swal.fire('Error', 'No se pudo conectar con el servidor.', 'error');
        }
    });
});
