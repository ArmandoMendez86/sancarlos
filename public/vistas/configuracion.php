<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración - Estacionamiento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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

        .config-card {
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

        .day-config {
            padding-bottom: 15px;
            margin-bottom: 15px;
        }

        .day-config .row {
            align-items: center;
        }

        .day-config .row label {
            padding: 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card config-card">
                    <div class="card-header text-center bg-primary text-white">
                        <h3>Configuración del sistema</h3>
                    </div>
                    <div class="card-body">
                        <form id="configForm" method="post">
                            <div class="mb-3">
                                <label for="businessName" class="form-label">Nombre del negocio</label>
                                <input type="text" class="form-control" id="businessName" name="nombre_negocio"
                                    placeholder="Ej. El Buen Estacionamiento" required>
                            </div>
                            <div class="mb-4">
                                <label for="address" class="form-label">Dirección</label>
                                <input type="text" class="form-control" id="address" name="direccion"
                                    placeholder="Ej. Av. Siempre Viva 742" required>
                            </div>

                            <hr class="my-4">
                            <h5 class="mb-3">Horarios de atención</h5>

                            <div class="row">
                                <div class="col-md-6 col-lg-4 day-config">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="lunes_abierto"
                                            name="lunes_abierto">
                                        <label class="form-check-label" for="lunes_abierto">Lunes</label>
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <input type="time" class="form-control" id="lunes_apertura"
                                                name="lunes_apertura">
                                        </div>
                                        <div class="col-6">
                                            <input type="time" class="form-control" id="lunes_cierre"
                                                name="lunes_cierre">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 col-lg-4 day-config">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="martes_abierto"
                                            name="martes_abierto">
                                        <label class="form-check-label" for="martes_abierto">Martes</label>
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <input type="time" class="form-control" id="martes_apertura"
                                                name="martes_apertura">
                                        </div>
                                        <div class="col-6">
                                            <input type="time" class="form-control" id="martes_cierre"
                                                name="martes_cierre">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 col-lg-4 day-config">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="miercoles_abierto"
                                            name="miercoles_abierto">
                                        <label class="form-check-label" for="miercoles_abierto">Miércoles</label>
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <input type="time" class="form-control" id="miercoles_apertura"
                                                name="miercoles_apertura">
                                        </div>
                                        <div class="col-6">
                                            <input type="time" class="form-control" id="miercoles_cierre"
                                                name="miercoles_cierre">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 col-lg-4 day-config">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="jueves_abierto"
                                            name="jueves_abierto">
                                        <label class="form-check-label" for="jueves_abierto">Jueves</label>
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <input type="time" class="form-control" id="jueves_apertura"
                                                name="jueves_apertura">
                                        </div>
                                        <div class="col-6">
                                            <input type="time" class="form-control" id="jueves_cierre"
                                                name="jueves_cierre">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 col-lg-4 day-config">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="viernes_abierto"
                                            name="viernes_abierto">
                                        <label class="form-check-label" for="viernes_abierto">Viernes</label>
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <input type="time" class="form-control" id="viernes_apertura"
                                                name="viernes_apertura">
                                        </div>
                                        <div class="col-6">
                                            <input type="time" class="form-control" id="viernes_cierre"
                                                name="viernes_cierre">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 col-lg-4 day-config">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="sabado_abierto"
                                            name="sabado_abierto">
                                        <label class="form-check-label" for="sabado_abierto">Sábado</label>
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <input type="time" class="form-control" id="sabado_apertura"
                                                name="sabado_apertura">
                                        </div>
                                        <div class="col-6">
                                            <input type="time" class="form-control" id="sabado_cierre"
                                                name="sabado_cierre">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 col-lg-4 day-config">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="domingo_abierto"
                                            name="domingo_abierto">
                                        <label class="form-check-label" for="domingo_abierto">Domingo</label>
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <input type="time" class="form-control" id="domingo_apertura"
                                                name="domingo_apertura">
                                        </div>
                                        <div class="col-6">
                                            <input type="time" class="form-control" id="domingo_cierre"
                                                name="domingo_cierre">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary">Guardar configuración</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

     <?php
    include __DIR__ . '/../../app/parciales/menu.php';
    include __DIR__ . '/../../app/parciales/gasto.php';
    include __DIR__ . '/../../app/parciales/wc.php';
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="public/js/rutas.js"></script>
    <script src="public/js/configuracion.js"></script>
  
</body>

</html>