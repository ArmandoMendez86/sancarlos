<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehículos Estacionados - Estacionamiento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #2c3e50;
            color: #ecf0f1;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        .scrollable-container {
            max-height: 80vh;
            overflow-y: auto;
            padding-right: 15px;
        }

        .vehicle-card {
            background-color: #fff;
            color: #2c3e50;
            border: none;
            border-left: 5px solid #0d6efd;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: transform 0.2s;
        }

        .vehicle-card:hover {
            transform: translateY(-5px);
        }

        /* --- INICIO DE NUEVOS ESTILOS PARA EL MODAL --- */

        .modal-content {
            background-color: #f8f9fa;
            border-radius: 1rem;
            border: none;
        }

        .modal-header {
            border-bottom: none;
            padding: 1.5rem 1.5rem 0.5rem;
        }

        .modal-body {
            padding: 1rem 2rem;
        }

        .modal-footer {
            border-top: none;
            padding: 0.5rem 1.5rem 1.5rem;
        }

        .placa-display {
            font-size: 2rem;
            font-weight: bold;
            color: #0d6efd;
            letter-spacing: 2px;
            margin-bottom: 1.5rem;
        }

        .detail-list {
            list-style: none;
            padding: 0;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            font-size: 0.95rem;
            padding: 0.75rem 0;
            border-bottom: 1px solid #dee2e6;
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        .detail-item .label {
            color: #6c757d;
        }

        .detail-item .value {
            font-weight: 600;
            color: #212529;
        }

        .total-display-box {
            background-color: #e9f5ee;
            border: 1px solid #a3cfbb;
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-top: 1.5rem;
        }

        .total-display-box .price {
            font-size: 3.5rem;
            font-weight: 700;
            color: #28a745;
            line-height: 1;
        }

        .total-display-box .label {
            font-size: 1rem;
            color: #3e8a5b;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 500;
        }

        /* --- FIN DE NUEVOS ESTILOS PARA EL MODAL --- */

        .modal-title {
            color: #212529;
            /* Color oscuro para el título */
        }

        .form-check-label {
            color: #212529;
            /* Color oscuro para las etiquetas */
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center mb-4">
            <div class="col-md-10 text-center">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h3>Vehículos actualmente en el estacionamiento</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center mb-4">
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" class="form-control search-input" id="searchInput"
                        placeholder="Buscar por placa...">
                    <button class="btn btn-primary" type="button"><i class="fa-solid fa-search"></i></button>
                </div>
            </div>
        </div>

        <div class="scrollable-container" id="vehicleCardsContainer">
            <div class="row justify-content-center">
            </div>
        </div>
    </div>

    <div class="modal fade" id="cobroModal" tabindex="-1" aria-labelledby="cobroModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="cobroForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="cobroModalLabel">Resumen de Cobro</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <div class="text-center">
                            <h3 class="placa-display" id="modal-placa">------</h3>
                        </div>

                        <ul class="detail-list">
                            <li class="detail-item">
                                <span class="label">Entrada:</span>
                                <span class="value" id="modal-entrada">--</span>
                            </li>
                            <li class="detail-item">
                                <span class="label">Salida:</span>
                                <span class="value" id="modal-salida">--</span>
                            </li>
                            <li class="detail-item">
                                <span class="label">Horario del día:</span>
                                <span class="value" id="modal-horario-dia">--</span>
                            </li>
                            <li class="detail-item">
                                <span class="label">Tiempo Total:</span>
                                <span class="value" id="modal-tiempo">--</span>
                            </li>
                        </ul>

                        <hr class="my-3">

                        <div class="d-flex justify-content-around">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="boleto_perdido_check"
                                    name="boleto_perdido">
                                <label class="form-check-label" for="boleto_perdido_check">Boleto Perdido</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="es_pension_check"
                                    name="es_pension">
                                <label class="form-check-label" for="es_pension_check">Pensión Nocturna</label>
                            </div>
                        </div>

                        <div class="text-center total-display-box">
                            <div class="label">Total a Pagar</div>
                            <div class="price" id="modal-costo">--</div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success btn-lg w-100">
                            <i class="fa-solid fa-cash-register me-2"></i>Realizar Cobro
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script src="public/js/rutas.js"></script>
    <script src="public/js/activos.js"></script>
</body>

</html>