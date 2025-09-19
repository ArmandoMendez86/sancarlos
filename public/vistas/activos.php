<!-- public/activos.php -->
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Vehículos Estacionados - Estacionamiento</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="public/css/activos.css"><!-- si ya la tenías, perfecto -->
</head>
<body>
  <div class="container">
    <div class="row justify-content-center mb-4">
      <div class="col-md-10 text-center">
        <div class="card page-title-card border-0 shadow-sm">
          <div class="card-body">
            <h3 class="m-0">Vehículos actualmente en el estacionamiento</h3>
          </div>
        </div>
      </div>
    </div>

    <div class="row justify-content-center mb-4">
      <div class="col-md-6">
        <div class="input-group">
          <span class="input-group-text bg-white"><i class="fa-solid fa-search"></i></span>
          <input type="text" class="form-control" id="searchInput" placeholder="Buscar por placa...">
        </div>
      </div>
    </div>

    <div class="scrollable-container" id="vehicleCardsContainer">
      <div class="row justify-content-center"></div>
    </div>
  </div>

  <!-- MODAL COBRO -->
  <div class="modal fade" id="cobroModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <form id="cobroForm">
          <div class="modal-header border-0">
            <h5 class="modal-title"><i class="fa-solid fa-cash-register me-2"></i>Salida y Cobro</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body pt-0">
            <div class="text-center placa-display" id="modal-placa">--</div>

            <ul class="detail-list">
              <li class="detail-item"><span>Tipo</span><span id="modal-tipo">--</span></li>
              <li class="detail-item"><span>Marca</span><span id="modal-marca">--</span></li>
              <li class="detail-item"><span>Color</span><span id="modal-color">--</span></li>
            </ul>

            <div class="row g-3 mt-2">
              <div class="col-md-6">
                <label class="form-label">Entrada</label>
                <input id="input-entrada" type="datetime-local" class="form-control" />
              </div>
              <div class="col-md-6">
                <label class="form-label">Salida</label>
                <input id="input-salida" type="datetime-local" class="form-control" />
              </div>
            </div>
            <p class="small text-muted mt-2">Horario del día: <span id="modal-horario-dia">--</span></p>

            <ul class="detail-list mt-2">
              <li class="detail-item"><span>Tiempo</span><span id="modal-tiempo">--</span></li>
            </ul>

            <hr class="my-3">

            <!-- Controles de cobro -->
            <div class="row g-3">
              <div class="col-md-6">
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="boleto_perdido_check" name="boleto_perdido">
                  <label class="form-check-label" for="boleto_perdido_check">Boleto Perdido</label>
                </div>

                <div class="form-check form-switch mt-2">
                  <input class="form-check-input" type="checkbox" id="tolerancia_check" checked>
                  <label class="form-check-label" for="tolerancia_check">Tolerancia 30 min tras apertura</label>
                </div>
              </div>

              <div class="col-md-6">
                <label class="form-label mb-1">Modo de cobro</label>
                <div class="d-flex flex-column gap-1" id="modo-cobro-group">
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="cobro_mode" id="mode_normal" value="normal" checked>
                    <label class="form-check-label" for="mode_normal">
                      Normal (diurno + $100 por cada noche)
                    </label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="cobro_mode" id="mode_pension_tiempo" value="pension_tiempo">
                    <label class="form-check-label" for="mode_pension_tiempo">
                      Pensión (tiempo hasta cierre + $100)
                    </label>
                  </div>
                  <div class="form-check d-flex align-items-center gap-2">
                    <div>
                      <input class="form-check-input" type="radio" name="cobro_mode" id="mode_solo_pension" value="solo_pension">
                      <label class="form-check-label" for="mode_solo_pension">
                        Solo pensión ($100 x noche)
                      </label>
                    </div>
                    <div class="ms-2 d-flex align-items-center gap-2">
                      <label for="soloPensionNoches" class="small mb-0">Noches:</label>
                      <input type="number" id="soloPensionNoches" class="form-control form-control-sm" style="width:90px" min="1" step="1" value="1" disabled>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Desglose -->
            <div class="mt-3" id="desglose-container">
              <div class="table-responsive">
                <table class="table table-sm table-striped align-middle">
                  <thead>
                    <tr>
                      <th>Fecha</th>
                      <th>Ventana (apertura→cierre)</th>
                      <th>Usado</th>
                      <th>Costo diurno</th>
                      <th>Pensión nocturna</th>
                      <th>Total día</th>
                    </tr>
                  </thead>
                  <tbody id="desglose-body"></tbody>
                  <tfoot>
                    <tr>
                      <th colspan="4" class="text-end">Noches (x$100)</th>
                      <th id="desglose-noches">0</th>
                      <th id="desglose-total">$0.00</th>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>

            <div class="text-center total-display-box">
              <div class="label" id="total-mode-label">Total a Pagar</div>
              <div class="price" id="modal-costo">$0.00</div>
            </div>
          </div>
          <div class="modal-footer border-0">
            <button type="submit" class="btn btn-success btn-lg w-100">
              <i class="fa-solid fa-arrow-right-from-bracket me-2"></i>Realizar Cobro
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Librerías -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="public/js/rutas.js"></script>
  <script src="public/js/activos.js"></script>
</body>
</html>
