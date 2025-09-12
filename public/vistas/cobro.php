<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cobro - Estacionamiento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
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
        .form-control, .form-select {
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
            font-size: 3rem; /* Tamaño de fuente mucho más grande */
            font-weight: bold;
            color: #28a745; /* Color verde para el costo */
            margin-top: 15px;
            margin-bottom: 15px;
            display: block; /* Asegura que ocupe su propio bloque */
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
                        <form action="#" method="post">
                            <div class="mb-4">
                                <label for="searchPlate" class="form-label">Buscar vehículo por placa</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="searchPlate" placeholder="Ej. ABC-123">
                                    <button class="btn btn-primary" type="button"><i class="fa-solid fa-search"></i></button>
                                </div>
                            </div>
                            
                            <hr class="mb-4">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="entryDateTime" class="form-label">Fecha y hora de entrada</label>
                                    <input type="datetime-local" class="form-control" id="entryDateTime">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="exitDateTime" class="form-label">Fecha y hora de salida</label>
                                    <input type="datetime-local" class="form-control" id="exitDateTime">
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="lostTicket">
                                    <label class="form-check-label" for="lostTicket">
                                        Boleto perdido
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="nightPension">
                                    <label class="form-check-label" for="nightPension">
                                        Pasar a pensión nocturna
                                    </label>
                                </div>
                            </div>
                            
                            <div class="text-center mb-4">
                                <h4 class="mb-2">Tiempo total: <span class="text-primary fw-bold">2 horas y 30 minutos</span></h4>
                                <span class="total-cost-display">
                                    $50.00
                                </span>
                                <label class="form-label fw-bold">Costo total</label>
                            </div>
                            
                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-success btn-lg">Realizar cobro</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>