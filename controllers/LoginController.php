<?php

namespace Controllers;

use MVC\Router;
use Model\Usuario;
use Classes\Email;

class LoginController {

    public static function login(Router $router) {

        $alertas = [];

        if($_SERVER['REQUEST_METHOD']==='POST') {
            $usuario = new Usuario($_POST);

            $alertas = $usuario->validarLogin();

            if (empty($alertas)) {
                // Verificar que el usuario exista
                $usuario = Usuario::where('email',$usuario->email);

                if ($usuario && $usuario->confirmado) {

                    // El usuario existe
                    if (password_verify($_POST['password'],$usuario->password)) {
                        // Iniciar sesion 
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = TRUE;
                        
                        // Redireccionar
                        header('Location: /dashboard');

                    } else {
                        Usuario::setAlerta('error','Contraseña incorrecta');
                    }

                } else {
                    Usuario::setAlerta('error','No existe o no se ha confirmado el usuario');
                }
            }
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/login',[
            'titulo' => 'Iniciar Sesión',
            'alertas' => $alertas
        ]);
    }

    public static function logout() {
        session_start();
        $_SESSION=[];
        header('Location: /');
    }

    public static function crear(Router $router) {

        $usuario = new Usuario();
        $alertas = [];

        if($_SERVER['REQUEST_METHOD']==='POST') {
            $usuario->sincronizar($_POST);

            $alertas = $usuario->validarNuevaCuenta();
            
            if (empty($alertas)) {
            
                $existeUsuario = Usuario::where('email',$usuario->email);

                if($existeUsuario) {
                    Usuario::setAlerta('error','El usuario ya está registrado');
                                   
                } else {

                    // Hashear password
                    $usuario->hashPassword();
                    // Eliminar password2
                    unset($usuario->password2);

                    // Generar token unico 
                    $usuario->crearToken();

                    // Crear nuevo usuario 
                    $resultado = $usuario->guardar();

                    // Enviar email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);

                    $email->enviarConfirmacion();

                    if($resultado) {
                        header('Location: /mensaje');
                    }
                }
            }
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/crear',[
            'titulo' => 'Crea tu cuenta',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function olvide(Router $router) {

        $alertas = [];

        if($_SERVER['REQUEST_METHOD']==='POST') {
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarEmail();

            if (empty($alertas) ) {
                // Buscar el usuario 
                $usuario = Usuario::where('email',$usuario->email);

                if ($usuario && $usuario->confirmado) {
                    // Encontre al usuario y esta confirmado
                    // Generar nuevo token 
                    $usuario->crearToken();
                    // Actualizar el usuario 
                    unset($usuario->password2);

                    $usuario->guardar();
                    // Enviar el email 
                    $email = new Email($usuario->email,$usuario->nombre,$usuario->token);
                    $email->enviarInstrucciones();

                    // Imprimir la alerta
                    Usuario::setAlerta('exito','Hemos enviado las instrucciones a tu email');

                } else {
                    Usuario::setAlerta('error','El usuario no existe o no está confirmado');
                }

            }
        }
        $alertas = Usuario::getAlertas();

        $router->render('auth/olvide',[
            'titulo'=>'Olvide mi Password',
            'alertas' => $alertas
        ]);
    }
    public static function reestablecer(Router $router) {

        $token = $_GET['token'];
        $mostrar = true;

        if(!$token) header('Location: /');
        
        // Identificar usuario con ese token 
        $usuario = Usuario::where('token',$token);
        
        if(empty($usuario)) {
            Usuario::setAlerta('error','Token no válido');
            $mostrar =false;
        }

        if($_SERVER['REQUEST_METHOD']==='POST') {
            // Añadir nuevo password
            $usuario->sincronizar($_POST);

            // Validar el password
            $alertas = $usuario->validarPassword();

            if(empty($alertas)) {
                //Hashear el password
                $usuario->hashPassword();
                
                // Borrar el token 
                $usuario->token = null;

                // Guardar
                $resultado = $usuario->guardar();

                // Redireccionar
                if($resultado) {
                    header('Locarion: /');
                }

            }

        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/reestablecer',[
            'titulo'=>'Reestablecer Password',
            'alertas'=>$alertas,
            'mostrar'=>$mostrar
        ]);
    }

    public static function mensaje(Router $router) {
        $router->render('auth/mensaje',[
            'titulo'=>'Cuenta creada exitosamente'
        ]);    }

    public static function confirmar(Router $router) {

        $token = s($_GET['token']);

        if(!$token) header('Location: /');

        // Encontrar al usuario con el token 
        $usuario = Usuario::where('token',$token);

        if (empty($usuario)) {
            // No se localiza usuario por token
            Usuario::setAlerta('error','Token no válido');               
        } else {
            // Confirmar la cuenta
            $usuario->confirmado = 1;
            $usuario->token =null;
            unset($usuario->password2);
            // Guardar en la BBDD
            $usuario->guardar();

            Usuario::setAlerta('exito','Cuenta Comprobada Correctamente');               
        
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/confirmar',[
            'titulo'=>'Confirma tu cuenta UpTask',
            'alertas' => $alertas
        
        ]);    }
    

}