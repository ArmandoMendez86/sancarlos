
document.addEventListener('DOMContentLoaded', (event) => {
    const selectVehiculo = document.getElementById('vehicleType');
    const entradaForm = document.getElementById('entradaForm');

    const cargarTiposVehiculos = async () => {
        try {
            const response = await fetch(`${BASE_URL_API}/index.php?action=vehiculos/tipos`);
            const data = await response.json();
            if (data.success) {
                selectVehiculo.innerHTML = '<option selected disabled>Seleccione una opción</option>';
                data.data.forEach(vehiculo => {
                    const option = document.createElement('option');
                    option.value = vehiculo.id;
                    option.textContent = vehiculo.tipo;
                    selectVehiculo.appendChild(option);
                });
            } else {
                console.error('Error del servidor:', data.message);
            }
        } catch (error) {
            console.error('Error al conectar con la API:', error);
        }
    };
    cargarTiposVehiculos();

    entradaForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(entradaForm);
        const response = await fetch(`${BASE_URL_API}/index.php?action=entradas/registrar`, {
            method: 'POST',
            body: formData,
        });
        const data = await response.json();

        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: data.message,
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                entradaForm.reset();
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
