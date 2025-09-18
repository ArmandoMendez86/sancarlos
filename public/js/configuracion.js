document.addEventListener('DOMContentLoaded', async () => {
    const configForm = document.getElementById('configForm');
    const dayNames = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];

    // Cargar configuración actual
    const cargarConfiguracion = async () => {
        const response = await fetch(`${BASE_URL_API}/index.php?action=configuracion/obtener`);
        const data = await response.json();
        if (data.success && data.data) {
            const config = data.data;
            document.getElementById('businessName').value = config.nombre_negocio || '';
            document.getElementById('address').value = config.direccion || '';

            dayNames.forEach(day => {
                const abierto = document.getElementById(`${day}_abierto`);
                const apertura = document.getElementById(`${day}_apertura`);
                const cierre = document.getElementById(`${day}_cierre`);

                // Comprobamos si el elemento existe antes de intentar acceder a él
                if (abierto && apertura && cierre) {
                    abierto.checked = config[`${day}_abierto`] == 1;
                    apertura.value = config[`${day}_apertura`] || '';
                    cierre.value = config[`${day}_cierre`] || '';
                    apertura.disabled = !abierto.checked;
                    cierre.disabled = !abierto.checked;
                }
            });
        }
    };
    cargarConfiguracion();

    // Deshabilitar/habilitar campos de horario según el switch
    dayNames.forEach(day => {
        const daySwitch = document.getElementById(`${day}_abierto`);
        if (daySwitch) {
            daySwitch.addEventListener('change', () => {
                const abierto = daySwitch.checked;
                document.getElementById(`${day}_apertura`).disabled = !abierto;
                document.getElementById(`${day}_cierre`).disabled = !abierto;
            });
        }
    });

    // Guardar configuración
    configForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(configForm);
        dayNames.forEach(day => {
            const abierto = document.getElementById(`${day}_abierto`);
            if (abierto) {
                formData.set(`${day}_abierto`, abierto.checked ? 1 : 0);
            }
        });

        const response = await fetch('api/index.php?action=configuracion/guardar', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        if (data.success) {
            Swal.fire('¡Éxito!', data.message, 'success');
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    });
});