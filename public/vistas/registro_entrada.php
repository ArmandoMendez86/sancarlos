<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Estacionamiento</title>
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

        .kpi-card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 12px;
            text-align: center;
            padding: 20px;
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .kpi-card .kpi-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
            color: rgba(255, 255, 255, 0.8);
        }

        .kpi-card .kpi-value {
            font-size: 1.8rem;
            font-weight: bold;
            color: #fff;
        }

        .kpi-card .kpi-label {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.7);
            margin-top: 5px;
        }

        .card-container {
            width: 100%;
            max-width: 900px;
        }

        .form-card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .nav-tabs .nav-link {
            color: #bdc3c7;
            /* Texto claro para pestañas inactivas */
            border: none;
            border-radius: 8px 8px 0 0;
            background-color: #34495e;
            /* Fondo oscuro para pestañas inactivas */
            margin-right: 2px;
        }

        .nav-tabs .nav-link.active {
            color: #fff;
            background-color: #0d6efd;
            /* Color vibrante para la pestaña activa */
        }

        .tab-content {
            background-color: #fff;
            /* Contenido con fondo blanco */
            color: #2c3e50;
            padding: 20px;
            border-radius: 0 0 8px 8px;
        }

        .search-container {
            display: flex;
            gap: 10px;
        }

        .form-control,
        .form-select {
            background-color: #f0f2f5;
            color: #2c3e50;
            border-color: #ced4da;
        }

        .form-control::placeholder {
            color: #888;
        }

        @media (min-width: 768px) {
            .card.h-100 {
                min-height: 50vh;
            }

            .display-3 {
                font-size: 3rem;
            }
        }
    </style>
</head>

<body>
    <div class="card-container">
        <div class="row">
            <div class="col-6 col-md-3">
                <div class="kpi-card bg-success text-white">
                    <div class="kpi-icon"><i class="fa-solid fa-car"></i></div>
                    <div class="kpi-value">$500</div>
                    <div class="kpi-label">Estacionamiento</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="kpi-card bg-info text-white">
                    <div class="kpi-icon"><i class="fa-solid fa-calendar"></i></div>
                    <div class="kpi-value">$730</div>
                    <div class="kpi-label">Pensiones</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="kpi-card bg-danger text-white">
                    <div class="kpi-icon"><i class="fa-solid fa-sack-dollar"></i></div>
                    <div class="kpi-value">$200</div>
                    <div class="kpi-label">Gastos</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="kpi-card bg-warning text-dark">
                    <div class="kpi-icon"><i class="fa-solid fa-restroom"></i></div>
                    <div class="kpi-value">$150</div>
                    <div class="kpi-label">Baños</div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card form-card">
                    <div class="card-header p-0" style="border-bottom: none;">
                        <ul class="nav nav-tabs nav-fill" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="entrada-tab" data-bs-toggle="tab"
                                    data-bs-target="#entrada-pane" type="button" role="tab" aria-controls="entrada-pane"
                                    aria-selected="true">Entrada de Vehículo</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="salida-tab" data-bs-toggle="tab"
                                    data-bs-target="#salida-pane" type="button" role="tab" aria-controls="salida-pane"
                                    aria-selected="false">Salida y Cobro</button>
                            </li>
                        </ul>
                    </div>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="entrada-pane" role="tabpanel"
                            aria-labelledby="entrada-tab" tabindex="0">
                            <form action="#" class="p-4" id="entradaForm">
                                <div class="mb-3">
                                    <label for="licensePlate" class="form-label">Placa del vehículo</label>
                                    <input type="text" class="form-control" id="licensePlate" placeholder="Ej. ABC-123"
                                        name="placa">
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="vehicleType" class="form-label">Tipo de vehículo</label>
                                        <select class="form-select" id="vehicleType" required name="vehiculos_id">
                                            <option selected disabled>Seleccione una opción</option>
                                            <option value="Automovil">Automóvil</option>
                                            <option value="Motocicleta">Motocicleta</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="brand" class="form-label">Marca</label>
                                        <input type="text" class="form-control" id="brand" placeholder="Ej. Toyota"
                                            required name="marca">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="color" class="form-label">Color</label>
                                    <input type="text" class="form-control" id="color" placeholder="Ej. Rojo" required
                                        name="color">
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-success">Registrar entrada</button>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane fade" id="salida-pane" role="tabpanel" aria-labelledby="salida-tab"
                            tabindex="0">
                            <form action="#" method="post" class="p-4">
                                <div class="mb-4">
                                    <label for="licensePlateSearch" class="form-label">Buscar vehículo por placa</label>
                                    <div class="search-container">
                                        <input type="text" class="form-control" id="licensePlateSearch"
                                            placeholder="Ej. ABC-123" required>
                                        <button class="btn btn-primary" type="button">
                                            <i class="fa-solid fa-search"></i>
                                        </button>
                                    </div>
                                </div>

                                <div id="vehicle-details"></div>
                                <div id="not-found-message" style="display: none;">
                                    <div class="alert alert-danger text-center" role="alert">
                                        Vehículo no encontrado. Por favor, verifique el folio.
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <?php
    /*
    include __DIR__ . '/../../app/parciales/menu.php';
    include __DIR__ . '/../../app/parciales/gasto.php';
    include __DIR__ . '/../../app/parciales/wc.php';
    */
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

    <script src="public/js/rutas.js"></script>
    <script src="public/js/registro_entrada.js"></script>
</body>

</html>