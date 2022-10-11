<?php

namespace Model;

class Usuario extends ActiveRecord {

    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id','nombre','email','password','token','confirmado'];

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->password2 = $args['password2'] ?? null;
        $this->password_actual = $args['password_actual'] ?? null;
        $this->password_nuevo = $args['password_nuevo'] ?? null;
        $this->token = $args['token'] ?? '';
        $this->confirmado = $args['confirmado'] ?? 0;
    }

    // Validar Login 
    public function validarLogin() :array{
        if (!$this->email) {
            self::$alertas['error'][] ='El Email del cliente es obligatorio'; 
        }
        if (!filter_var($this->email,FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = "El Email no es válido";
        }
        if (!$this->password) {
            self::$alertas['error'][] ='El Password no puede ir vacío'; 
        }
        if (strlen($this->password)<6) {
            self::$alertas['error'][] ='El Password debe contener al menos 6 caracteres'; 
        }
        return self::$alertas;

    }

    // Validación para cuentas nuevas
    public function validarNuevaCuenta():array {
        if (!$this->nombre) {
            self::$alertas['error'][] ='El Nombre del cliente es obligatorio'; 
        }
        if (!$this->email) {
            self::$alertas['error'][] ='El Email del cliente es obligatorio'; 
        }
        if (!$this->password) {
            self::$alertas['error'][] ='El Password no puede ir vacío'; 
        }
        if (strlen($this->password)<6) {
            self::$alertas['error'][] ='El Password debe contener al menos 6 caracteres'; 
        }
        if ($this->password != $this->password2) {
            self::$alertas['error'][] ='Los passwords son diferentes'; 
        }
        return self::$alertas;
    }

    // Valida un email 
    public function validarEmail():array {
        
        if (!$this->email) {
            self::$alertas['error'][] = "El Email es  obligatorio";
        }
        if (!filter_var($this->email,FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = "El Email no es válido";
        }

        return self::$alertas;
    }

    // Valida el password
    public function validarPassword() :array{
        if (!$this->password) {
            self::$alertas['error'][] ='El Password no puede ir vacío'; 
        }
        if (strlen($this->password)<6) {
            self::$alertas['error'][] ='El Password debe contener al menos 6 caracteres'; 
        }
        return self::$alertas;

    }

    // Valida el perfil 
    public function validar_perfil() :array{
        if (!$this->nombre) {
            self::$alertas['error'][] ='El Nombre es obligatorio'; 
        }
        if (!$this->email) {
            self::$alertas['error'][] = "El Email es  obligatorio";
        }
        if (!filter_var($this->email,FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = "El Email no es válido";
        }
        return self::$alertas;
    }

    // Valida nuevo password
    public function nuevo_password() : array {
        if (!$this->password_actual) {
            self::$alertas['error'][] = "El Password Actual no puede ir vacio";
        }
        if (!$this->password_nuevo) {
            self::$alertas['error'][] = "El Password Nuevo no puede ir vacio";
        }
        if (strlen($this->password_nuevo) < 6) {
            self::$alertas['error'][] = "El password debe contener al menos 6 caracteres";
        }
        
        return self::$alertas;
    }

    // Comprobar password
    public function comprobar_password() : bool{
        return password_verify($this->password_actual,$this->password);
    }

    // Hashea el password
    public function hashPassword() : void{
        $this->password = password_hash($this->password,PASSWORD_BCRYPT); 
    }
    // Generar token unico 
    public function crearToken() : void{
        $this->token = uniqid();
    }
}