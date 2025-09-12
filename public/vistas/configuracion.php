<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración - Estacionamiento</title>
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
        .config-card {
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
        .day-config {
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        .day-config:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card config-card">
                    <div class="card-header text-center bg-primary text-white">
                        <h3>Configuración del sistema</h3>
                    </div>
                    <div class="card-body">
                        <form action="#" method="post">
                            <div class="mb-3">
                                <label for="businessName" class="form-label">Nombre del negocio</label>
                                <input type="text" class="form-control" id="businessName" placeholder="Ej. El Buen Estacionamiento" required>
                            </div>
                            <div class="mb-4">
                                <label for="address" class="form-label">Dirección</label>
                                <input type="text" class="form-control" id="address" placeholder="Ej. Av. Siempre Viva 742" required>
                            </div>
                            
                            <h5 class="mb-3">Horarios de atención</h5>

                            <div class="day-config">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="mondaySwitch" checked>
                                    <label class="form-check-label" for="mondaySwitch">Lunes</label>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <input type="time" class="form-control" id="mondayOpen">
                                    </div>
                                    <div class="col-6">
                                        <input type="time" class="form-control" id="mondayClose">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="day-config">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="tuesdaySwitch" checked>
                                    <label class="form-check-label" for="tuesdaySwitch">Martes</label>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <input type="time" class="form-control" id="tuesdayOpen">
                                    </div>
                                    <div class="col-6">
                                        <input type="time" class="form-control" id="tuesdayClose">
                                    </div>
                                </div>
                            </div>

                            <div class="day-config">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="wednesdaySwitch" checked>
                                    <label class="form-check-label" for="wednesdaySwitch">Miércoles</label>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <input type="time" class="form-control" id="wednesdayOpen">
                                    </div>
                                    <div class="col-6">
                                        <input type="time" class="form-control" id="wednesdayClose">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="day-config">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="thursdaySwitch" checked>
                                    <label class="form-check-label" for="thursdaySwitch">Jueves</label>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <input type="time" class="form-control" id="thursdayOpen">
                                    </div>
                                    <div class="col-6">
                                        <input type="time" class="form-control" id="thursdayClose">
                                    </div>
                                </div>
                            </div>

                            <div class="day-config">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="fridaySwitch" checked>
                                    <label class="form-check-label" for="fridaySwitch">Viernes</label>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <input type="time" class="form-control" id="fridayOpen">
                                    </div>
                                    <div class="col-6">
                                        <input type="time" class="form-control" id="fridayClose">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        // Opcional: Deshabilita los campos de hora si el switch de un día está apagado.
        document.querySelectorAll('.day-config').forEach(day => {
            const daySwitch = day.querySelector('input[type="checkbox"]');
            const openInput = day.querySelector('input[type="time"]:nth-child(1)');
            const closeInput = day.querySelector('input[type="time"]:nth-child(2)');

            daySwitch.addEventListener('change', () => {
                const isChecked = daySwitch.checked;
                openInput.disabled = !isChecked;
                closeInput.disabled = !isChecked;
            });
        });
    </script>
</body>
</html>