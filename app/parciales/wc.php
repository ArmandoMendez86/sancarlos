<style>
    .fab-button-baños {
        background-color: #198754;
        margin-bottom: 5rem;
    }
</style>



<button id="btnBaños" class="fab-button fab-button-baños">
    <i class="fa-solid fa-restroom"></i>
</button>





<script>
    document.addEventListener('DOMContentLoaded', () => {
        const btnBaños = document.getElementById('btnBaños');


        // Lógica para el botón de Baños
        btnBaños.addEventListener('click', async () => {
            const response = await fetch(`${BASE_URL_API}/index.php?action=ventas/registrar`, {
                method: 'POST',
                body: JSON.stringify({}),
                headers: { 'Content-Type': 'application/json' }
            });
            const data = await response.json();

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Venta registrada!',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 1500
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