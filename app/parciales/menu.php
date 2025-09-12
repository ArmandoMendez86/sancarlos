<style>
    /* Estilos para el botón FAB */
    .fab-container {
        position: fixed;
        bottom: 25px;
        right: 25px;
        z-index: 1000;
    }

    .fab-button {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        font-size: 1.5rem;
        transition: transform 0.3s ease-in-out;
    }

    .fab-button.active {
        transform: rotate(45deg);
    }

    .fab-menu {
        list-style: none;
        padding: 0;
        margin: 0;
        position: absolute;
        bottom: 75px;
        right: 0;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s, visibility 0.3s, transform 0.3s;
        transform: translateY(20px);
    }

    .fab-menu.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .fab-menu li {
        margin-bottom: 10px;
    }

    .fab-menu .btn {
        width: 150px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
    }
</style>


<div class="fab-container">
    <ul class="fab-menu" id="fabMenu">
        <li><a href="#" class="btn btn-info btn-sm">Salida</a></li>
        <li><a href="#" class="btn btn-warning btn-sm">Reportes</a></li>
        <li><a href="configuracion" class="btn btn-success btn-sm">Configuración</a></li>
    </ul>
    <button class="btn btn-primary fab-button" id="fabButton">
        <i class="fa-solid fa-plus"></i>
    </button>
</div>


<script>
    const fabButton = document.getElementById('fabButton');
    const fabMenu = document.getElementById('fabMenu');

    fabButton.addEventListener('click', () => {
        fabMenu.classList.toggle('show');
        fabButton.classList.toggle('active');
    });
</script>