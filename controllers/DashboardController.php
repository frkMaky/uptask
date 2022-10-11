<?php

namespace Controllers;

use MVC\Router;
use Model\Proyecto;
use Model\Usuario;


class DashboardController {

    public static function index(Router $router) {
        session_start();
        isAuth();

        $id = $_SESSION['id'];
        $proyectos = Proyecto::belongsTo('propietarioId',$id);

        $router->render('dashboard/index',[
            'titulo' => "Proyectos",
            'proyectos' => $proyectos
        ]);
    }

    public static function crear_proyecto(Router $router) {
        session_start();
        isAuth();
        $alertas= [];

        if ($_SERVER['REQUEST_METHOD']==='POST'){
            $proyecto = new Proyecto($_POST);

            // Validacion 
            $alertas = $proyecto->validarProyecto();

            if (empty($alertas) ) {
                // Generar URL unica
                $proyecto->url = md5 ( uniqid() );
                // Guardar creaador del proyecto
                $proyecto->propietarioId = $_SESSION['id'];
                // Guardar el proyecto 
                $proyecto->guardar();            
                // Redireccionar al proyecto
                header('Location: /proyecto?id=' . $proyecto->url);
            }
        }

        $router->render('dashboard/crear-proyecto',[
            'titulo' => "Crear Proyecto",
            'alertas' => $alertas
        ]);
    }

    public static function proyecto(Router $router) {
        session_start();
        isAuth();

        $token = $_GET['id'];
        if (!$token) header('Location: /dashboard');
        
        // Revisar que la persona que visita el proyecto es quien lo creo
        $proyecto = Proyecto::where('url',$token);
        if($proyecto->propietarioId !== $_SESSION['id'] ) {
            header('Location: /dashboard');
        }

        $router->render('dashboard/proyecto',[
            'titulo' => $proyecto->proyecto
        ]);
    }
    public static function perfil(Router $router) {
        session_start();
        isAuth();
        $alertas = [];
        $usuario = Usuario::find($_SESSION['id']);

        if($_SERVER['REQUEST_METHOD']==='POST') {

            $usuario->sincronizar($_POST);
            $alertas = $usuario->validar_perfil();

            if (empty($alertas)) {

                // Verificar no existe usuario duplicado
                $existeUsuario = Usuario::where('email',$usuario->email);

                if($existeUsuario && $existeUsuario->id !== $usuario->id) {
                    
                    Usuario::setAlerta('error','El email indicado ya se encuentra registrado');
                    
                } else {
                    // Guardar el usuario
                    $usuario->guardar();
                    
                    Usuario::setAlerta('exito','Guardado correctamente');
                 
                    // Asignar nombre nuevo a la barra
                    $_SESSION['nombre'] = $usuario->nombre;    
                }
                $alertas = $usuario->getAlertas();

            }
        }

        $router->render('dashboard/perfil',[
            'titulo' => "Perfil",
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function cambiar_password(Router $router){

        session_start();
        isAuth();

        $alertas = [];

        if ($_SERVER['REQUEST_METHOD']==="POST") {
            $usuario = Usuario::finf($_SESSION['id']);

            // Sincronizar con los datos del usuario
            $usuario->sincronizar($_POST);
            $alertas = $usuario->nuevo_password();

            if (empty($alertas) ) {
                $resultado = $usuario->comprobar_password();
                if ($resultado) {
                    // Asignar el nuevo password
                    $usuario->password = $usuario->password_nuevo;
                    // Hashear nuevo password
                    $usuario->hashPassword();
                    // Actualizar
                    $resultado = $usuario->guardar();
                    // Eliminar propiedades no necesarias
                    unset($usuario->password_actual);
                    unset($usuario->password_nuevo);

                    if ($resultado) {
                        Usuario::setAlerta('exito','Password Nuevo Actualizado');
                    }
                    
                
                } else {
                    Usuario::setAlerta('error','Password Incorrecto');
                }
                $alertas = $usuario->getAlertas();
            }
        }

        $router->render('dashboard/cambiar-password',[
            'titulo' => "Cambiar Password",
            'alertas' => $alertas
        ]);

    }
}