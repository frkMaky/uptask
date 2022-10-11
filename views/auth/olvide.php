<div class="contenedor olvide">

    <?php include_once __DIR__ . '/../templates/nombre-sitio.php';?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Recupera tu contraseña UpTask</p>

        <?php include_once __DIR__ . '/../templates/alertas.php';?>

        <form action="/olvide" class="formulario" method="post" novalidate>
            <div class="campo">
                <label for="email">Email</label>
                <input 
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Tu email"
                />
            </div>
            <input type="submit" class="boton" value="Enviar instrucciones">
        </form>

        <div class="acciones">
            <a href="/">¿Ya tienes una cuenta? Iniciar Sesión</a>
            <a href="/crear">¿Aún no tienes una cuenta? Obtener una</a>
        </div>

    </div> <!-- contenedor-sm -->
</div>