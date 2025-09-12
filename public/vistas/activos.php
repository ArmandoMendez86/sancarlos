<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehículos Estacionados - Estacionamiento</title>
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
        .card-header {
            background-color: #0d6efd;
            color: #fff;
            font-weight: bold;
            border-radius: 0;
        }
        .card-title {
            font-size: 1.5rem;
            margin-bottom: 0;
        }
        .card-body .row {
            align-items: center;
        }
        .info-item {
            font-size: 1rem;
            margin-bottom: 8px;
        }
        .info-item .icon {
            color: #6c757d;
            margin-right: 5px;
        }
        .search-input {
            background-color: #f0f2f5;
            color: #2c3e50;
            border-color: #ced4da;
        }
        .search-input::placeholder {
            color: #888;
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
                    <input type="text" class="form-control search-input" id="searchInput" placeholder="Buscar por placa...">
                    <button class="btn btn-primary" type="button"><i class="fa-solid fa-search"></i></button>
                </div>
            </div>
        </div>

        <div class="scrollable-container" id="vehicleCardsContainer">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-4 mb-4 vehicle-card-item" data-plate="ABC-123">
                    <div class="card vehicle-card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title">ABC-123</h5>
                        </div>
                        <div class="card-body">
                            <p class="info-item"><i class="fa-solid fa-car icon"></i> Tipo: <span class="fw-bold">Automóvil</span></p>
                            <p class="info-item"><i class="fa-solid fa-tag icon"></i> Marca: <span class="fw-bold">Nissan</span></p>
                            <p class="info-item"><i class="fa-solid fa-palette icon"></i> Color: <span class="fw-bold">Rojo</span></p>
                            <p class="info-item"><i class="fa-solid fa-clock icon"></i> Entrada: <span class="fw-bold">10:00 AM</span></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-4 vehicle-card-item" data-plate="DEF-456">
                    <div class="card vehicle-card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title">DEF-456</h5>
                        </div>
                        <div class="card-body">
                            <p class="info-item"><i class="fa-solid fa-motorcycle icon"></i> Tipo: <span class="fw-bold">Motocicleta</span></p>
                            <p class="info-item"><i class="fa-solid fa-tag icon"></i> Marca: <span class="fw-bold">Yamaha</span></p>
                            <p class="info-item"><i class="fa-solid fa-palette icon"></i> Color: <span class="fw-bold">Negro</span></p>
                            <p class="info-item"><i class="fa-solid fa-clock icon"></i> Entrada: <span class="fw-bold">11:15 AM</span></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-4 vehicle-card-item" data-plate="GHI-789">
                    <div class="card vehicle-card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title">GHI-789</h5>
                        </div>
                        <div class="card-body">
                            <p class="info-item"><i class="fa-solid fa-truck-pickup icon"></i> Tipo: <span class="fw-bold">Camioneta</span></p>
                            <p class="info-item"><i class="fa-solid fa-tag icon"></i> Marca: <span class="fw-bold">Ford</span></p>
                            <p class="info-item"><i class="fa-solid fa-palette icon"></i> Color: <span class="fw-bold">Azul</span></p>
                            <p class="info-item"><i class="fa-solid fa-clock icon"></i> Entrada: <span class="fw-bold">12:30 PM</span></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-4 vehicle-card-item" data-plate="JKL-012">
                    <div class="card vehicle-card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title">JKL-012</h5>
                        </div>
                        <div class="card-body">
                            <p class="info-item"><i class="fa-solid fa-car icon"></i> Tipo: <span class="fw-bold">Automóvil</span></p>
                            <p class="info-item"><i class="fa-solid fa-tag icon"></i> Marca: <span class="fw-bold">Honda</span></p>
                            <p class="info-item"><i class="fa-solid fa-palette icon"></i> Color: <span class="fw-bold">Gris</span></p>
                            <p class="info-item"><i class="fa-solid fa-clock icon"></i> Entrada: <span class="fw-bold">01:00 PM</span></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-4 vehicle-card-item" data-plate="MNO-345">
                    <div class="card vehicle-card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title">MNO-345</h5>
                        </div>
                        <div class="card-body">
                            <p class="info-item"><i class="fa-solid fa-motorcycle icon"></i> Tipo: <span class="fw-bold">Motocicleta</span></p>
                            <p class="info-item"><i class="fa-solid fa-tag icon"></i> Marca: <span class="fw-bold">Kawasaki</span></p>
                            <p class="info-item"><i class="fa-solid fa-palette icon"></i> Color: <span class="fw-bold">Verde</span></p>
                            <p class="info-item"><i class="fa-solid fa-clock icon"></i> Entrada: <span class="fw-bold">01:45 PM</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        const searchInput = document.getElementById('searchInput');
        const cards = document.querySelectorAll('.vehicle-card-item');

        searchInput.addEventListener('keyup', (e) => {
            const searchText = e.target.value.toLowerCase();
            
            cards.forEach(card => {
                const plate = card.getAttribute('data-plate').toLowerCase();
                if (plate.includes(searchText)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>