<div class="contenedor olvide">
    <?php include_once __DIR__ .'/../templates/nombre-sitio.php' ?>
    <div class="contenedor-sm">
        <p class="descripcion-pagina">Recupera tu acceso UpTask</p>

        <?php include_once __DIR__ .'/../templates/alertas.php' ?>

        <form class="formulario" method="POST" action="/olvide" novalidate>
            <div class="campo">
                <label for="email">Correo</label>
                <input
                    type="email"
                    id="email"
                    placeholder="Tu Correo"
                    name="email"
                >
            </div>
            <input type="submit" class="boton" value="Enviar Instrucciones">
        </form>

        <div class="acciones">
            <a href="/">¿Ya tienes una cuenta? Inicia Sesión</a>
            <a href="/crear">¿No tienes una cuenta? Registrate</a>
        </div>
    </div>
</div>