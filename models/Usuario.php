<?php

namespace Model;

class Usuario extends ActiveRecord {
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id','nombre','email','password','token','confirmado'];

    public function __construct($args = []){
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->password2 = $args['password2'] ?? '';
        $this->password_actual = $args['password_actual'] ?? '';
        $this->password_nuevo = $args['password_nuevo'] ?? '';
        $this->token = $args['token'] ?? '';
        $this->confirmado = $args['confirmado'] ?? 0;
    }

    //Validar le Login de Usuarios
    public function validarLogin(){
        if(!$this->email){
            self::$alertas['error'][] = 'El Correo de Usuario es obligatorio';
        }

        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)){
            self::$alertas['error'][] = 'Correo no válido';
        }

        if(!$this->password){
            self::$alertas['error'][] = 'La Contraseña de Usuario es obligatorio';
        }

        return self::$alertas;
    }

    //Validacion para cuentas nuevas
    public function validarNuevaCuenta(){
        if(!$this->nombre){
            self::$alertas['error'][] = 'El Nombre de Usuario es obligatorio';
        }
        if(!$this->email){
            self::$alertas['error'][] = 'El Correo de Usuario es obligatorio';
        }

        if(!$this->password){
            self::$alertas['error'][] = 'La Contraseña de Usuario es obligatorio';
        }
        if(strlen($this->password) < 6){
            self::$alertas['error'][] = 'La contraseña debe contener al menos 6 caracteres';
        }
        if($this->password !== $this->password2){
            self::$alertas['error'][] = 'Las contraseñas son diferentes';
        }
        return self::$alertas;
    }

    //Valida un Email
    public function validarEmail(){
        if(!$this->email){
            self::$alertas['error'][] = 'El Correo es Obligatorio';
        }

        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)){
            self::$alertas['error'][] = 'Correo no válido';
        }

        return self::$alertas;
    }
    
    public function validarPassword(){
        if(!$this->password){
            self::$alertas['error'][] = 'La Contraseña de Usuario es obligatorio';
        }
        if(strlen($this->password) < 6){
            self::$alertas['error'][] = 'La contraseña debe contener al menos 6 caracteres';
        }
        return self::$alertas;
    }

    public function validar_perfil(){
        if(!$this->nombre){
            self::$alertas['error'][] ='El Nombre es Obligatorio';
        }
        if(!$this->email){
            self::$alertas['error'][] ='El Correo es Obligatorio';
        }
        return self::$alertas;
    }

    public function nuevo_password() {//: array{                    buena practica pero se ve como si tuviera errores
        if(!$this->password_actual){
            self::$alertas['error'][] = 'La contraseña actual no puede ir vacía';
        }
        if(!$this->password_nuevo){
            self::$alertas['error'][] = 'La nueva contraseña no puede ir vacía';
        }
        if(strlen($this->password_nuevo) < 6){
            self::$alertas['error'][] = 'La contraseña debe contener al menos 6 caracteres';
        }
        return self::$alertas;
    }

    //Comprobar el password
    public function comprobar_password() {//: bool{
        return password_verify($this->password_actual, $this->password);
    }

    //Hashea el password
    public function hashPassword() {//: void{
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    //Generar un token
    public function generarToken() {//: void{
        $this->token = uniqid();
    }
}