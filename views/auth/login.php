<div class="contenedor login">

    <?php include_once __DIR__ . '/../templates/nombre-sitio.php';?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Iniciar Sesión</p>
        
        <?php include_once __DIR__ . '/../templates/alertas.php';?>

        <form action="/" class="formulario" method="post">
            <div class="campo">
                <label for="email">Email</label>
                <input 
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Tu email"
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
            <input type="submit" class="boton" value="Iniciar Sesion">
        </form>

        <div class="acciones">
            <a href="/crear">¿Aún no tienes una cuenta? Obtener una</a>
            <a href="/olvide">¿Olvidaste tu password?</a>
        </div>

    </div> <!-- contenedor-sm -->
</div>