<style>
    /* Estilos para el botón FAB */
    .fab-button {
        position: fixed;
        bottom: 25px;
        left: 25px;
        z-index: 1000;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background-color: #dc3545;
        /* Rojo para gastos */
        color: #fff;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        font-size: 1.5rem;
        transition: background-color 0.3s;
        cursor: pointer;
    }

    .fab-button:hover {
        background-color: #c82333;
    }

    /* Estilos del modal */
    .modal-content {
        background-color: #f8f9fa;
        color: #2c3e50;
        border-radius: 12px;
        animation: fade-in 0.3s ease-out;
    }

    @keyframes fade-in {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .form-control {
        background-color: #f0f2f5;
        color: #2c3e50;
    }

    .input-group-text {
        background-color: #e9ecef;
    }
</style>



<button class="fab-button" data-bs-toggle="modal" data-bs-target="#expenseModal">
    <i class="fa-solid fa-sack-dollar"></i>
</button>

<div class="modal fade" id="expenseModal" tabindex="-1" aria-labelledby="expenseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="expenseModalLabel">Registrar Gasto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="expenseForm">
                    <div class="mb-3">
                        <label for="expenseConcept" class="form-label">Concepto</label>
                        <input type="text" class="form-control" id="expenseConcept" name="concepto"
                            placeholder="Ej. Material de limpieza" required>
                    </div>
                    <div class="mb-3">
                        <label for="expenseAmount" class="form-label">Monto</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" id="expenseAmount" name="monto"
                                placeholder="Ej. 150.00" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="expenseForm" class="btn btn-primary">Guardar Gasto</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const expenseForm = document.getElementById('expenseForm');
        const expenseModal = new bootstrap.Modal(document.getElementById('expenseModal'));

        expenseForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(expenseForm);
            const response = await fetch(`${BASE_URL_API}/index.php?action=gastos/registrar`, {
                method: 'POST',
                body: formData,
            });
            const data = await response.json();

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Gasto registrado!',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    expenseModal.hide();
                    expenseForm.reset();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message,
                });
            }
        });
    });
</script>