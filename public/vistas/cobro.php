<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cobro - Estacionamiento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
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

        .cobro-card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            background-color: #fff;
            color: #2c3e50;
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

        .input-group-text {
            background-color: #f0f2f5;
            color: #2c3e50;
            border-color: #ced4da;
        }

        .total-cost-display {
            font-size: 3rem;
            /* Tamaño de fuente mucho más grande */
            font-weight: bold;
            color: #28a745;
            /* Color verde para el costo */
            margin-top: 15px;
            margin-bottom: 15px;
            display: block;
            /* Asegura que ocupe su propio bloque */
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card cobro-card">
                    <div class="card-header text-center bg-primary text-white">
                        <h3>Cobro de Estacionamiento</h3>
                    </div>
                    <div class="card-body">
                        <!-- Entrada por FOLIO (lectura con escáner) -->
                        <div class="mb-4">
                            <label for="folioInput" class="form-label">Buscar por folio (escáner)</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="folioInput" placeholder="Ej. SEP-0001" autocomplete="off">
                                <button class="btn btn-primary" id="btnBuscarFolio" type="button">
                                    <i class="fa-solid fa-search"></i>
                                </button>
                            </div>
                            <div class="form-text">Enfoca el input y escanea el código de barras del boleto.</div>
                        </div>

                        <hr class="mb-0">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    include __DIR__ . '/../../app/parciales/menu.php';
    include __DIR__ . '/../../app/parciales/gasto.php';
    include __DIR__ . '/../../app/parciales/wc.php';
    // >>> Modal reutilizado <<<
    include __DIR__ . '/../../app/parciales/modal_cobro.php';
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="public/js/rutas.js"></script>
    <script src="public/js/cobro_scan.js"></script>
</body>


</html>