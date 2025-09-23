<!-- MODAL COBRO (reutilizado) -->
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

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label mb-1">Entrada</label>
                            <input type="datetime-local" class="form-control" id="input-entrada">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label mb-1">Salida</label>
                            <input type="datetime-local" class="form-control" id="input-salida">
                        </div>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="boleto_perdido_check">
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
                                    <label class="form-check-label" for="mode_normal">Normal (diurno + $100 por cada noche)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="cobro_mode" id="mode_pension_tiempo" value="pension_tiempo">
                                    <label class="form-check-label" for="mode_pension_tiempo">Pensión (tiempo hasta cierre + $100)</label>
                                </div>
                                <div class="form-check d-flex align-items-center gap-2">
                                    <div>
                                        <input class="form-check-input" type="radio" name="cobro_mode" id="mode_pension_solo" value="pension_solo">
                                        <label class="form-check-label" for="mode_pension_solo">Pensión (solo $100 por noche)</label>
                                    </div>
                                    <div class="input-group input-group-sm" style="width: 180px;">
                                        <span class="input-group-text">$</span>
                                        <input type="number" min="0" step="1" id="input_monto_pension" class="form-control" value="100">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-3">
                    <div class="text-center">
                        <div class="h5 mb-1" id="label_total_modo">Total a Pagar</div>
                        <div class="display-6 fw-bold" id="total_costo">$0.00</div>
                    </div>
                </div>

                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-success">
                        <i class="fa-solid fa-arrow-right-from-bracket me-2"></i>Realizar Cobro
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>