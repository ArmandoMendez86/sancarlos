document.addEventListener('DOMContentLoaded', () => {
    const cardsContainer = document.querySelector('#vehicleCardsContainer .row');
    const searchInput = document.getElementById('searchInput');
    const cobroModal = new bootstrap.Modal(document.getElementById('cobroModal'));
    const cobroForm = document.getElementById('cobroForm');

    let vehiculoData = {};

    const formatDate = (dateString) => {
        const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
        const date = new Date(dateString);
        return date.toLocaleDateString('es-MX', options);
    };

    const renderVehicleCard = (vehiculo) => {
        const formattedDate = formatDate(vehiculo.fecha_entrada);
        const cardHtml = `
            <div class="col-md-6 col-lg-4 mb-4 vehicle-card-item" data-plate="${vehiculo.placa}">
                <div class="card vehicle-card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title">${vehiculo.placa}</h5>
                    </div>
                    <div class="card-body">
                        <p class="info-item"><i class="fa-solid fa-car icon"></i> Tipo: <span class="fw-bold">${vehiculo.tipo}</span></p>
                        <p class="info-item"><i class="fa-solid fa-tag icon"></i> Marca: <span class="fw-bold">${vehiculo.marca}</span></p>
                        <p class="info-item"><i class="fa-solid fa-palette icon"></i> Color: <span class="fw-bold">${vehiculo.color}</span></p>
                        <p class="info-item"><i class="fa-solid fa-clock icon"></i> Entrada: <span class="fw-bold">${formattedDate}</span></p>
                        <hr>
                        <button class="btn btn-success btn-sm w-100 btn-salida" data-bs-toggle="modal" data-bs-target="#cobroModal" data-plate="${vehiculo.placa}">
                            <i class="fa-solid fa-arrow-right-from-bracket me-2"></i>Dar Salida
                        </button>
                    </div>
                </div>
            </div>
        `;
        cardsContainer.insertAdjacentHTML('beforeend', cardHtml);
    };

    const cargarVehiculosActivos = async () => {
        try {
            const response = await fetch(`${BASE_URL_API}/index.php?action=entradas/obtenerActivas`);
            const result = await response.json();

            cardsContainer.innerHTML = '';

            if (result.success && result.data.length > 0) {
                result.data.forEach(vehiculo => renderVehicleCard(vehiculo));
                attachButtonEvents();
            } else {
                cardsContainer.innerHTML = '<div class="text-center w-100 p-5">No hay vehículos en el estacionamiento.</div>';
            }
        } catch (error) {
            console.error('Error al cargar los vehículos:', error);
            cardsContainer.innerHTML = '<div class="text-center w-100 p-5 text-danger">Error al conectar con la API.</div>';
        }
    };

    const recalcularCosto = () => {
        if (!vehiculoData.id) return;

        const costoBase = parseFloat(vehiculoData.costo_base);
        let costoFinal = costoBase;
        let boletoPerdido = 0;
        let esPension = 0;

        const lostTicketChecked = document.getElementById('boleto_perdido_check').checked;
        const nightPensionChecked = document.getElementById('es_pension_check').checked;

        // --- LÓGICA ORIGINAL DE BOLETO PERDIDO (RESTAURADA) ---
        if (lostTicketChecked) {
            if (costoBase <= 100) {
                costoFinal = 100;
                boletoPerdido = costoBase - costoFinal;
            } else {
                costoFinal += 50;
                boletoPerdido = 50;
            }
        }

        // --- LÓGICA DE PENSIÓN CON HORA DE CIERRE DINÁMICA ---
        if (nightPensionChecked) {
            const horaCierre = vehiculoData.hora_cierre; // Dato dinámico del backend

            // Solo proceder si hay una hora de cierre válida
            if (horaCierre && horaCierre !== 'Cerrado') {
                const fechaEntrada = new Date(vehiculoData.fecha_entrada);
                const fechaCierre = new Date(fechaEntrada.toISOString().split('T')[0] + 'T' + horaCierre + ':00');

                if (fechaCierre > fechaEntrada) {
                    const diffMs = fechaCierre - fechaEntrada;
                    const diffMins = Math.floor(diffMs / 60000);
                    let costoPension = 0;

                    // Se mantiene tu lógica de cálculo original
                    if (diffMins > 3) {
                        let minutosCobrados = diffMins - 3;
                        costoPension += 20; // Asumo que 20 es tu tarifa base por hora
                        minutosCobrados -= 60;
                        while (minutosCobrados > 0) {
                            if (minutosCobrados > 10) {
                                costoPension += 20;
                            }
                            minutosCobrados -= 60;
                        }
                    }
                    // Importante: La pensión sobreescribe el costo final, como en tu código original
                    costoFinal = costoPension + 100; // Costo hasta cierre + tarifa de pensión
                    esPension = 1;
                }
            }
        }

        document.getElementById('modal-costo').textContent = `$${costoFinal.toFixed(2)}`;

        vehiculoData.cobro_final = costoFinal;
        vehiculoData.boleto_perdido_costo = boletoPerdido;
        vehiculoData.es_pension_flag = esPension;
    };

    const attachButtonEvents = () => {
        const btnSalidas = document.querySelectorAll('.btn-salida');
        btnSalidas.forEach(btn => {
            btn.addEventListener('click', async (e) => {
                const plate = e.currentTarget.getAttribute('data-plate');

                try {
                    const response = await fetch(`${BASE_URL_API}/index.php?action=salidas/obtenerDetalles&placa=${plate}`);
                    const data = await response.json();

                    if (data.success) {
                        vehiculoData = data.data;

                        document.getElementById('modal-placa').textContent = vehiculoData.placa;
                        document.getElementById('modal-entrada').textContent = formatDate(vehiculoData.fecha_entrada);
                        document.getElementById('modal-salida').textContent = formatDate(vehiculoData.fecha_salida);
                        document.getElementById('modal-tiempo').textContent = vehiculoData.tiempo_total;

                        // --- LÍNEA AGREGADA PARA MOSTRAR HORARIO ---
                        document.getElementById('modal-horario-dia').textContent = `${vehiculoData.hora_apertura} - ${vehiculoData.hora_cierre}`;

                        document.getElementById('boleto_perdido_check').checked = false;
                        document.getElementById('es_pension_check').checked = false;

                        recalcularCosto();
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                } catch (error) {
                    Swal.fire('Error', 'No se pudo conectar con el servidor.', 'error');
                }
            });
        });
    };

    document.getElementById('boleto_perdido_check').addEventListener('change', recalcularCosto);
    document.getElementById('es_pension_check').addEventListener('change', recalcularCosto);

    cobroForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const salidaData = {
            entrada_id: vehiculoData.id,
            cobro: vehiculoData.cobro_final,
            boleto_perdido: vehiculoData.boleto_perdido_costo,
            es_pension: vehiculoData.es_pension_flag
        };

        const response = await fetch(`${BASE_URL_API}/index.php?action=salidas/registrar`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(salidaData),
        });
        const data = await response.json();

        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Salida registrada!',
                text: data.message,
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                cobroModal.hide();
                cargarVehiculosActivos();
            });
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    });

    cargarVehiculosActivos();
});