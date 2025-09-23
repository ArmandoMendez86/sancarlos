document.addEventListener('DOMContentLoaded', () => {

    let tipoCobroActual = null; // 'normal', 'diurno', 'pension'
    let salidaActual = null;
    let horarioHoy = null; // Guardar horario del día para el botón pensión

    async function obtenerHorarioDia(dia) {
        const response = await fetch(`${BASE_URL_API}/index.php?action=configuracion/obtenerHorarioDiaAPI&dia=${encodeURIComponent(dia)}`);
        const data = await response.json();
        if (data.success) {
            return data.data;
        } else {
            throw new Error('No se pudo obtener el horario');
        }
    }

    function calcularCobroEstacionamiento({
        tarifa = 20,
        tolerancia_primera = 3, // minutos
        tolerancia_final = 10,   // minutos
        entrada,
        salida,
        pago_diurno = false,
        pago_pension = false,
        boleto_perdido = false
    }) {
        const fechaEntrada = entrada instanceof Date ? entrada : new Date(entrada);
        const fechaSalida = salida instanceof Date ? salida : new Date(salida);

        let minutos = Math.ceil((fechaSalida - fechaEntrada) / 60000);
        if (minutos <= 0) return 0;

        // Primera hora con tolerancia de 3 minutos
        if (minutos <= 60 + tolerancia_primera) {
            let subtotal = minutos > tolerancia_primera ? tarifa : 0;

            if (boleto_perdido) {
                if (subtotal > 100) {
                    subtotal += 50;
                } else if (subtotal > 0) {
                    subtotal = 100;
                }
            }
            if (pago_diurno || pago_pension) subtotal += 100;
            return subtotal;
        }

        // Más de una hora: cobra la primera hora, luego bloques de 1h con tolerancia final
        minutos -= 60 + tolerancia_primera;
        let horas = 1;
        while (minutos > 0) {
            if (minutos <= 60 + tolerancia_final) {
                horas++;
                break;
            } else {
                horas++;
                minutos -= 60 + tolerancia_final;
            }
        }
        let subtotal = horas * tarifa;

        if (boleto_perdido) {
            if (subtotal > 100) {
                subtotal += 50;
            } else {
                subtotal = 100;
            }
        }
        if (pago_diurno || pago_pension) subtotal += 100;
        return subtotal;
    }

    function mostrarTiempoConsumido(entrada, salida) {
        const fechaEntrada = entrada instanceof Date ? entrada : new Date(entrada);
        const fechaSalida = salida instanceof Date ? salida : new Date(salida);

        let diffMs = fechaSalida - fechaEntrada;
        if (diffMs < 0) diffMs = 0;

        const totalMin = Math.floor(diffMs / 60000);
        const horas = Math.floor(totalMin / 60);
        const minutos = totalMin % 60;

        // Calcula el total solo por tiempo (sin recargos)
        let minutosParaCobro = Math.ceil((fechaSalida - fechaEntrada) / 60000);
        let horasCobro = Math.floor(minutosParaCobro / 60);
        let minutosRestantes = minutosParaCobro % 60;
        if (minutosRestantes > 10) { // tolerancia final
            horasCobro += 1;
        }
        // Calcula el total solo por tiempo (sin recargos)
        const totalSoloTiempo = calcularCobroEstacionamiento({
            tarifa: 20,
            tolerancia_primera: 3,
            tolerancia_final: 10,
            entrada: fechaEntrada,
            salida: fechaSalida,
            pago_diurno: false,
            pago_pension: false,
            boleto_perdido: false
        });


        const tiempoStr = `<span class="text-primary">${horas} hora${horas !== 1 ? 's' : ''} ${minutos} minuto${minutos !== 1 ? 's' : ''}</span>`;
        const totalStr = `<span class="text-success fw-bold">$${totalSoloTiempo}</span>`;

        document.getElementById('tiempoConsumido').innerHTML = `
            <div class="alert alert-warning text-center fw-bold fs-5 mt-2" role="alert">
                <i class="fa-solid fa-clock"></i> Tiempo consumido: ${tiempoStr}
                <br>
                <span class="fs-6 text-dark">Total por tiempo: ${totalStr}</span>
            </div>
        `;
    }

    function mostrarInfoExtraCobro() {
        const infoDiv = document.getElementById('infoExtraCobro');
        if (tipoCobroActual === 'diurno' || tipoCobroActual === 'pension') {
            infoDiv.innerHTML = `
                <div class="alert alert-info text-center fw-bold fs-5 mt-2" role="alert">
                    <i class="fa-solid fa-circle-plus"></i> Recargo por pensión: <span class="text-success">$100</span>
                </div>
            `;
        } else {
            infoDiv.innerHTML = '';
        }
    }

    function mostrarBadgeTipoCobro() {
        const badgeDiv = document.getElementById('badgeTipoCobro');
        let badgeHtml = '';
        if (tipoCobroActual === 'normal') {
            badgeHtml = `<span class="badge bg-success fs-6"><i class="fa-solid fa-clock"></i> Cobro normal</span>`;
        } else if (tipoCobroActual === 'diurno') {
            badgeHtml = `<span class="badge bg-warning text-dark fs-6"><i class="fa-solid fa-sun"></i> Cobro diurno</span>`;
        } else if (tipoCobroActual === 'pension') {
            badgeHtml = `<span class="badge bg-info text-dark fs-6"><i class="fa-solid fa-bed"></i> Cobro pensión</span>`;
        } else {
            badgeHtml = '';
        }
        badgeDiv.innerHTML = badgeHtml;
    }

    function actualizarTotalCobro() {
        const boletoPerdido = document.getElementById('lostTicketCheckbox').checked;
        const entrada = document.getElementById('entradaFecha').textContent;
        const fechaEntrada = new Date(entrada.replace(' ', 'T'));
        const fechaSalida = salidaActual ? salidaActual : new Date();

        let total = calcularCobroEstacionamiento({
            tarifa: 20,
            tolerancia_primera: 3,
            tolerancia_final: 10,
            entrada: fechaEntrada,
            salida: fechaSalida,
            pago_diurno: (tipoCobroActual === 'diurno'),
            pago_pension: (tipoCobroActual === 'pension'),
            boleto_perdido: boletoPerdido
        });


        document.getElementById('totalCobroMonto').textContent = `$${total}`;
        mostrarTiempoConsumido(fechaEntrada, fechaSalida);
        mostrarBadgeTipoCobro();
        mostrarInfoExtraCobro();
    }

    document.getElementById('licensePlateSearch').addEventListener('input', async function (e) {
        const folio = e.target.value;

        if (folio.length > 0) {
            const formData = new FormData();
            formData.append('folio', folio);

            const response = await fetch(`${BASE_URL_API}/index.php?action=entradas/buscarPorFolio`, {
                method: 'POST',
                body: formData,
            });
            const data = await response.json();

            const detailsDiv = document.getElementById('vehicle-details');
            const notFoundDiv = document.getElementById('not-found-message');

            if (data && data.success && data.data) {
                // Obtener horario de hoy para el botón pensión
                const dias = ['domingo', 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
                const fechaEntrada = new Date(data.data.fecha_entrada.replace(' ', 'T'));
                const nombreDia = dias[fechaEntrada.getDay()];
                horarioHoy = await obtenerHorarioDia(nombreDia);

                // Calcular rango para mostrar botón pensión
                const hoy = new Date(fechaEntrada);
                const cierreStr = horarioHoy.cierre;
                const [hCierre, mCierre, sCierre] = cierreStr.split(':');
                const fechaCierre = new Date(hoy);
                fechaCierre.setHours(parseInt(hCierre), parseInt(mCierre), parseInt(sCierre));
                const fechaMediaHoraAntes = new Date(fechaCierre.getTime() - 30 * 60000);

                let btnPension = '';
                if (fechaEntrada >= fechaMediaHoraAntes && fechaEntrada <= fechaCierre) {
                    btnPension = `
                      <button type="button" class="btn btn-info btn-lg text-white py-3" id="btnPagarPension">
                        <i class="fa-solid fa-bed"></i> Pagar solo pensión
                      </button>
                    `;
                }

                const botonesHtml = `
                  <div class="d-grid gap-3 mt-4">
                    <button type="button" id="btnPagarSalida" class="btn btn-success btn-lg py-3">
                      <i class="fa-solid fa-cash-register"></i> Pagar y dar salida
                    </button>
                    <button type="button" class="btn btn-warning btn-lg py-3">
                      <i class="fa-solid fa-sun"></i> Pago diurno
                    </button>
                    ${btnPension}
                    <button type="button" id="btnConfirmarCobro" class="btn btn-primary btn-lg py-3 mt-2">
                      <i class="fa-solid fa-check"></i> Confirmar cobro
                    </button>
                  </div>
                `;

                detailsDiv.innerHTML = `
                    <div class="container-fluid">
                    <div class="row g-3 align-items-stretch">
                        <!-- Columna izquierda: Datos del vehículo -->
                        <div class="col-md-6">
                        <div class="card h-100 shadow-sm">
                            <div class="card-header bg-primary text-white">
                            <h4 class="mb-0"><i class="fa-solid fa-car-side"></i> Detalles del vehículo</h4>
                            </div>
                            <div class="card-body">
                            <div id="badgeTipoCobro" class="mb-3"></div>
                            <input type="hidden" id="entradaId" value="${data.data.id}">
                            <div class="mb-2"><strong>Placa:</strong> <span class="fw-bold text-uppercase float-end">${data.data.placa}</span></div>
                            <div class="mb-2"><strong>Marca:</strong> <span class="fw-bold float-end">${data.data.marca}</span></div>
                            <div class="mb-2"><strong>Color:</strong> <span class="fw-bold float-end">${data.data.color}</span></div>
                            <div class="mb-2"><strong>Folio:</strong> <span class="fw-bold float-end">${data.data.folio}</span></div>
                            <div class="mb-2"><strong>Entrada:</strong> <span class="fw-bold float-end" id="entradaFecha">${data.data.fecha_entrada}</span></div>
                            <div class="mb-2"><strong>Salida:</strong> <span class="fw-bold float-end" id="salidaFecha">-</span></div>
                            <div id="tiempoConsumido"></div>
                            <div id="infoExtraCobro"></div>
                            </div>
                        </div>
                        </div>
                        <!-- Columna derecha: Cobro y acciones -->
                        <div class="col-md-6">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body d-flex flex-column justify-content-between">
                            <div>
                                <div class="form-check my-3">
                                <input class="form-check-input" type="checkbox" id="lostTicketCheckbox">
                                <label class="form-check-label fw-bold text-danger" for="lostTicketCheckbox">
                                    Boleto perdido
                                </label>
                                </div>
                                <div id="totalCobroDiv" class="my-3">
                                <div class="card border-3 border-primary shadow-lg bg-gradient">
                                    <div class="card-body text-center">
                                    <span class="fs-4 fw-bold text-secondary">Total a cobrar</span>
                                    <div class="display-3 fw-bold text-success mt-2" id="totalCobroMonto">$0</div>
                                    </div>
                                </div>
                                </div>
                            </div>
                            ${botonesHtml}
                            </div>
                        </div>
                        </div>
                    </div>
                    </div>
                `;
                detailsDiv.style.display = '';
                if (notFoundDiv) notFoundDiv.style.display = 'none';

                // Scroll suave al mostrar detalles
                detailsDiv.scrollIntoView({ behavior: "smooth", block: "end" });

                tipoCobroActual = 'normal';
                salidaActual = null;

                document.getElementById('salidaFecha').textContent = '-';
                actualizarTotalCobro();

                document.getElementById('lostTicketCheckbox').addEventListener('change', function () {
                    actualizarTotalCobro();
                });

                document.getElementById('btnPagarSalida').addEventListener('click', function () {
                    tipoCobroActual = 'normal';
                    salidaActual = new Date();
                    document.getElementById('salidaFecha').textContent = salidaActual.toLocaleString();
                    actualizarTotalCobro();
                    activarDesactivarCheckbox(false, false);
                });

                document.querySelector('.btn-warning').addEventListener('click', async function () {
                    const dias = ['domingo', 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
                    const hoy = new Date();
                    const nombreDia = dias[hoy.getDay()];
                    const siguienteDia = new Date(hoy);
                    siguienteDia.setDate(hoy.getDate() + 1);
                    const nombreDiaSiguiente = dias[siguienteDia.getDay()];

                    try {
                        const horarioHoy = await obtenerHorarioDia(nombreDia);
                        if (!horarioHoy.abierto) {
                            document.getElementById('salidaFecha').textContent = '-';
                            document.getElementById('totalCobroMonto').textContent = '$0';
                            document.getElementById('tiempoConsumido').innerHTML = '';
                            document.getElementById('infoExtraCobro').innerHTML = '';
                            return;
                        }
                        const [hCierre, mCierre, sCierre] = horarioHoy.cierre.split(':');
                        const salidaCalculo = new Date(hoy);
                        salidaCalculo.setHours(parseInt(hCierre), parseInt(mCierre), parseInt(sCierre));

                        const horarioSiguiente = await obtenerHorarioDia(nombreDiaSiguiente);
                        if (!horarioSiguiente.abierto) {
                            document.getElementById('salidaFecha').textContent = '-';
                            salidaActual = null;
                        } else {
                            const [hApertura, mApertura, sApertura] = horarioSiguiente.apertura.split(':');
                            siguienteDia.setHours(parseInt(hApertura), parseInt(mApertura) + 30, parseInt(sApertura));
                            document.getElementById('salidaFecha').textContent = siguienteDia.toLocaleString();
                            salidaActual = salidaCalculo;
                        }

                        tipoCobroActual = 'diurno';

                        const entradaTexto = document.getElementById('entradaFecha').textContent;
                        const fechaEntrada = new Date(entradaTexto.replace(' ', 'T'));

                        if (fechaEntrada >= salidaCalculo) {
                            document.getElementById('totalCobroMonto').textContent = '$0';
                            document.getElementById('tiempoConsumido').innerHTML = '';
                            document.getElementById('infoExtraCobro').innerHTML = '';
                            mostrarBadgeTipoCobro();
                            return;
                        }

                        actualizarTotalCobro();
                        activarDesactivarCheckbox(false, false);

                    } catch (err) {
                        // Manejo de error
                    }
                });

                const btnPagarPension = document.getElementById('btnPagarPension');
                if (btnPagarPension) {
                    btnPagarPension.addEventListener('click', function () {
                        // Marcar tipo y salida (si te sirve para tu flujo)
                        tipoCobroActual = 'pension';
                        salidaActual = new Date();
                        document.getElementById('salidaFecha').textContent = salidaActual.toLocaleString();

                        // === SOLO FECHA VISUAL: apertura del día siguiente + 30 min ===
                        const dias = ['domingo', 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
                        const hoy = new Date();
                        const siguienteDia = new Date(hoy);
                        siguienteDia.setDate(hoy.getDate() + 1);
                        const nombreDiaSiguiente = dias[siguienteDia.getDay()];

                        // obtenerHorarioDia devuelve el horario del día que se pasa
                        obtenerHorarioDia(nombreDiaSiguiente)
                            .then(horarioSiguiente => {
                                if (horarioSiguiente && horarioSiguiente.abierto) {
                                    const [hApertura, mApertura, sApertura] = horarioSiguiente.apertura.split(':');
                                    const salidaVisual = new Date(siguienteDia);
                                    salidaVisual.setHours(parseInt(hApertura), parseInt(mApertura) + 30, parseInt(sApertura));
                                    document.getElementById('salidaFecha').textContent = salidaVisual.toLocaleString();
                                } else {
                                    document.getElementById('salidaFecha').textContent = '-';
                                }
                            })
                            .catch(() => {
                                // Si falla, dejamos la fecha que ya pusiste arriba (salidaActual) o un '-'
                            });
                        // === FIN SOLO FECHA VISUAL ===

                        // Forzar monto fijo a $100
                        document.getElementById('totalCobroMonto').textContent = '$100';

                        // Ocultar/limpiar el tiempo transcurrido
                        const tiempoDiv = document.getElementById('tiempoConsumido');
                        if (tiempoDiv) tiempoDiv.innerHTML = '';

                        activarDesactivarCheckbox(false, true);

                        // (Opcional) actualizar solo el badge
                        mostrarBadgeTipoCobro();
                        mostrarInfoExtraCobro();

                        // Importante: NO llamamos a actualizarTotalCobro() para evitar recálculos.
                    });
                }


                document.getElementById('btnConfirmarCobro').addEventListener('click', async function () {
                    // Datos que ya están en la UI
                    const montoTxt = document.getElementById('totalCobroMonto').textContent || '$0';
                    const monto = Number(montoTxt.replace(/[^0-9.]/g, '')) || 0;

                    const fechaSalidaTxt = document.getElementById('salidaFecha').textContent || '';

                    // Tomamos el id/folio de la entrada desde el detalle mostrado
                    const entradaId = document.querySelector('#entradaId')?.value
                    const pension = (tipoCobroActual != 'normal') ? 1 : 0;

                    // Preparar payload
                    const formData = new FormData();
                    formData.append('entrada_id', entradaId);         // folio o id de la entrada
                    formData.append('monto', monto);                  // numérico
                    formData.append('fecha_salida_txt', fechaSalidaTxt); // texto tal como se ve; el server lo normaliza
                    formData.append('pension', pension);              // 0/1

                    try {
                        const resp = await fetch(`${BASE_URL_API}/index.php?action=salidas/registrar`, {
                            method: 'POST',
                            body: formData
                        });
                        const json = await resp.json();

                        if (json && json.success) {
                            Swal.fire({ icon: 'success', title: 'Cobro realizado', text: 'Salida registrada correctamente.' });
                        } else {
                            Swal.fire({ icon: 'error', title: 'Error', text: json?.message || 'No se pudo registrar la salida.' });
                        }
                    } catch (e) {
                        Swal.fire({ icon: 'error', title: 'Error de red', text: 'No se pudo comunicar con el servidor.' });
                    }
                });


            } else {
                detailsDiv.style.display = 'none';
                if (notFoundDiv) notFoundDiv.style.display = '';
            }
        }
    });

    // === REGISTRO DE ENTRADA DE VEHÍCULO ===

    const entradaForm = document.getElementById('entradaForm');
    if (entradaForm) {
        entradaForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            // Obtén los datos del formulario
            const formData = new FormData(entradaForm);

            // Envía los datos al backend
            const response = await fetch(`${BASE_URL_API}/index.php?action=entradas/registrar`, {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            // Muestra el resultado
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Entrada registrada!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                });
                entradaForm.reset();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'No se pudo registrar la entrada.'
                });
            }
        });
    }

    function activarDesactivarCheckbox(valor1, valor2) {
        const lost = document.getElementById('lostTicketCheckbox');
        if (lost) { lost.checked = valor1; lost.disabled = valor2; }
    }

});