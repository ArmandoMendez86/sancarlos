<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Corte de Caja - Estacionamiento</title>
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

        .summary-card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 12px;
            text-align: center;
            padding: 20px;
            margin-bottom: 20px;
        }

        .summary-card .icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .summary-card .total-value {
            font-size: 2rem;
            font-weight: bold;
            margin-top: 10px;
        }

        .summary-card .label {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.8);
        }

        .total-box {
            background-color: #28a745;
            color: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .total-box h4 {
            font-size: 1.5rem;
            margin-bottom: 0;
        }

        .total-box .total-amount {
            font-size: 3rem;
            font-weight: bold;
            margin: 10px 0 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center mb-4">
            <div class="col-md-10 text-center">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h3>Corte de Caja Diario</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 col-lg-3">
                <div class="summary-card bg-info text-white">
                    <div class="icon"><i class="fa-solid fa-car"></i></div>
                    <div class="label">Estacionamiento</div>
                    <div class="total-value">$500.00</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="summary-card bg-warning text-dark">
                    <div class="icon"><i class="fa-solid fa-calendar-alt"></i></div>
                    <div class="label">Pensiones</div>
                    <div class="total-value">$730.00</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="summary-card bg-success text-white">
                    <div class="icon"><i class="fa-solid fa-restroom"></i></div>
                    <div class="label">Baños</div>
                    <div class="total-value">$150.00</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="summary-card bg-danger text-white">
                    <div class="icon"><i class="fa-solid fa-sack-dollar"></i></div>
                    <div class="label">Gastos</div>
                    <div class="total-value">-$200.00</div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center mt-4">
            <div class="col-md-6">
                <div class="total-box text-center">
                    <h4>Total en Caja</h4>
                    <span class="total-amount">$1,180.00</span>
                </div>
            </div>
        </div>

        <div class="row justify-content-center mt-4">
            <div class="col-md-6 d-grid">
                <button class="btn btn-secondary btn-lg" onclick="window.print()">
                    <i class="fa-solid fa-print"></i> Imprimir Reporte
                </button>
            </div>
        </div>

    </div>

   

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>