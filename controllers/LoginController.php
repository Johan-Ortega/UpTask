<?php

namespace Controllers;
use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController {
    public static function login(Router $router){
        $alertas = [];
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = new Usuario($_POST);

            $alertas = $usuario->validarLogin();

            if(empty($alertas)){
                //Verificar que el usuario exista
                $usuario = Usuario::where('email', $usuario->email);
                if(!$usuario || !$usuario->confirmado){
                    Usuario::setAlerta('error', 'Usuario No Existe o no está confirmado');
                } else{
                    //El Usuario Existe
                    if(password_verify($_POST['password'], $usuario->password)){
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        //Redireccionar
                        header('Location: /dashboard');
                    } else{
                        Usuario::setAlerta('error', 'Password Incorrecto');
                    }
                }
            }
        }

        $alertas = Usuario::getAlertas();

        //Render a la vista
        $router->render('auth/login', [
            'titulo' => 'Inicar Sesión',
            'alertas' => $alertas
        ]);
    }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public static function logout(){
        session_start();
        $_SESSION = [];
        header('Location: /');

    }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public static function crear(Router $router){
        $alertas = [];
        $usuario = new Usuario;
        
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();

            if(empty($alertas)){
                $existeUsuario = Usuario::where('email', $usuario->email);
            
                if($existeUsuario){
                    Usuario::setAlerta('error', 'El Usuario ya está registrado');
                    $alertas = Usuario::getAlertas();
                } else {
                    //Hashear el password
                    $usuario->hashPassword();

                    //Eliminar password2
                    unset($usuario->password2);

                    //Generar token
                    $usuario->generarToken();

                    //Crear un nuevo usuario
                    $resultado = $usuario->guardar();

                    //Enviar email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarConfirmacion();
                    
                    if($resultado){
                        header('Location: /mensaje');
                    }
                }
            }
        }

        //Render a la vista
        $router->render('auth/crear', [
            'titulo' => 'Crea tu cuenta',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public static function olvide(Router $router){
        $alertas = [];
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarEmail();

            if(empty($alertas)){
                //Buscar el usuario
                $usuario = Usuario::where('email', $usuario->email);

                if($usuario && $usuario->confirmado === '1'){
                    //Encontre al usuario

                    //Generar un nuevo token
                    $usuario->generarToken();
                    unset($usuario->password2);

                    //Actualizar el usuario
                    $usuario->guardar();

                    //Enviar el email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();

                    //Imprimir la alerta
                    Usuario::setAlerta('exito', 'Hemos enviado las instrucciones a tu correo');
                } else{
                    Usuario::setAlerta('error', 'El Usuario no existe o no está confirmado');
                }
            }
        }

        $alertas = Usuario::getAlertas();
        
        //Render a la vista
        $router->render('auth/olvide', [
            'titulo' => 'Olvide mi contraseña',
            'alertas' => $alertas
        ]);
    }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public static function reestablecer(Router $router){
        $token = s($_GET['token']);
        $mostrar = true;

        if(!$token) header('Location: /');

        //Identificar al usuario con este token
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)){
            Usuario::setAlerta('error', 'Token No Valido');
            $mostrar = false;
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            //Añadir el nuevo password
            $usuario->sincronizar($_POST);

            //Validar Password
            $alertas = $usuario->validarPassword();

            if(empty($alertas)){
                //Hashear el nuevo password
                $usuario->hashPassword();
                unset($usuario->password2);

                //Eliminar el token
                $usuario->token = null;

                //Guardar el usuario en la BD
                $resultado = $usuario->guardar();

                //Redireccionar
                if($resultado){
                    header('Location: /');
                }
            }
        }

        $alertas = Usuario::getAlertas();

        //Render a la vista
        $router->render('auth/reestablecer', [
            'titulo' => 'Reestablecer mi contraseña',
            'alertas' => $alertas,
            'mostrar' => $mostrar
        ]);
    }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public static function mensaje(Router $router){
        //Render a la vista
        $router->render('auth/mensaje', [
            'titulo' => 'Cuenta Creada Exitosamente'
        ]);
    }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public static function confirmar(Router $router){
        
        $token = s($_GET['token']);

        if(!$token) header('Location: /');

        //Encontrar al usuario con este token
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)){
            //No se encontro un usuario con ese token
            Usuario::setAlerta('error', 'Token No Válido');
        } else {
            //Confirmar la cuenta
            $usuario->confirmado = 1;
            $usuario->token = null;
            unset($usuario->password2);
            
            //Guardar al usuarioen la BD
            $usuario->guardar();

            Usuario::setAlerta('exito', 'Cuenta Comprobada Correctamente');

        }

        $alertas = Usuario::getAlertas();
        
        //Render a la vista
        $router->render('auth/confirmar', [
            'titulo' => 'Confirma tu cuenta',
            'alertas' => $alertas
        ]);
    }
}