<?php include_once __DIR__ . '/header-dashboard.php'; ?>

<div class="contenedor-sm">
    <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

    <a href="/perfil" class="enlace">Volver a Perfil</a>

    <form action="/cambiar-password" class="formulario" method="POST">
        <div class="campo">
            <label for="nombre">Contraseña Actual</label>
            <input type="password" name="password_actual" placeholder="Tu Contraseña Actual">
        </div>
        <div class="campo">
            <label for="nombre">Nueva Contraseña</label>
            <input type="password" name="password_nuevo" placeholder="Tu Nueva Contraseña">
        </div>

        <input type="submit" value="Guardar Cambios">
    </form>
</div>

<?php include_once __DIR__ . '/footer-dashboard.php'; ?>