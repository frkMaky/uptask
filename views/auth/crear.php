<div class="contenedor crear">

    <?php include_once __DIR__ . '/../templates/nombre-sitio.php';?>
    <?php include_once __DIR__ . '/../templates/alertas.php';?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Crea tu cuenta en UpTask</p>
        <form action="/crear" class="formulario" method="post">
            <div class="campo">
                <label for="nombre">Nombre</label>
                <input 
                    type="text0"
                    id="nombre"
                    name="nombre"
                    placeholder="Tu nombre"
                    value = "<?php echo $usuario->nombre; ?>"
                />
            </div>
            <div class="campo">
                <label for="email">Email</label>
                <input 
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Tu email"
                    value="<?php echo $usuario->email; ?>"
                />
            </div>
            <div class="campo">
                <label for="password">Password</label>
                <input 
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Tu password"
                />
            </div>
            <div class="campo">
                <label for="password2">Repetir Password</label>
                <input 
                    type="password"
                    id="password2"
                    name="password2"
                    placeholder="Repite tu password"
                />
            </div>
            <input type="submit" class="boton" value="Crear Cuenta">
        </form>

        <div class="acciones">
            <a href="/">¿Ya tienes una cuenta? Iniciar Sesión</a>
            <a href="/olvide">¿Olvidaste tu password?</a>
        </div>

    </div> <!-- contenedor-sm -->
</div>