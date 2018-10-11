<?php

/**
* Validador de datos provenientes de formularios
*
* @package Validator.php
* @author Anthony Sarcos <anthonysarcos@esferatecnologica.com>
*/

class Validator
{

    /**
    * @var String
    */
    private static $triggerAlert = '';

    /**
    * @var Array
    */
    private static $warning = [];

    /**
    * @var Array
    */
    private static $fieldsValues = [];

    /**
    * @var Array
    */
    private static $fieldValue = [];


    public static function validate($validations=[])
    {
        $triggerAlert = '';
        # Reglas de validación
        $rules =  [];
        # Etiqueta o mensaje del campo a validar
        $label = [];
        # Valor del campo a validar
        $value = [];
        # Arreglo que contiene las reglas de validación
        $setRules = [];

        foreach ($validations as $fields => $fieldRow) {
            $fieldsRules = explode('|', $fieldRow);
            $totalRules = count($fieldsRules);

            $label = explode(':', $fieldsRules[0]);
            $value = explode(':', $fieldsRules[1]);
            $setRules = explode(':', $fieldsRules[2]);

            # Mensaje para la validación
            $message = ( isset($fieldsRules[3]) )? explode(':', $fieldsRules[3]) : [null,null];

            if ( isset($setRules[0]) and $setRules[0] == 'rules' ) {
                //echo $fields.'-'.$label[1].'-'.$value[1].'-'.$rules[1].' <br> ';
                $rules =  explode(',', $setRules[1]);
                $triggerAlert .= self::evalRules([$fields,$label[1],$value[1], $message[1]], $rules);
            } else {
                $triggerAlert .= 'Debes proporcionar al menos una regla de validación';
            }
        }
        if ( $triggerAlert == null ) {
            return false;
        } else {
            return true;
        }

    }

    /**
    * Metodo que asegura una sola llamada al validador y lleva a cabo las reglas de validación.
    * @param  array $value, $validations
    * @return boolean
    */
    private static function evalRules($value=[],$rules=[])
    {
        $triggerAlert = '';
        $field = $value[0];
        $label = $value[1];
        $curFieldValue = $value[2];
        $message = ( isset($value[3]) )? $value[3] : '';

        foreach ( $rules as $rule )
        {
            # Verifica las reglas de validación según sea el caso
            $ruleParam = explode('=', $rule);

            $min = ( isset($ruleParam[1]) and $ruleParam[1] !== null )? $ruleParam[1] : '';
            $max = ( isset($ruleParam[1]) and $ruleParam[1] !== null )? $ruleParam[1] : '';

            if ( is_array($ruleParam) ) {
                $rule = $ruleParam[0];
            }

            switch ( $rule )
            {
                case 'required':
                    $triggerAlert .= self::isEmpty($curFieldValue);
                    if ( $triggerAlert !== '' ) {
                        self::$warning[] = 'El campo ' . $label . ' no puede estar vacío.';
                    } else {
                        self::$fieldsValues[] = [$field => $curFieldValue];
                        self::$fieldValue[$field] = $curFieldValue;
                    }
                    break;

                case 'email':
                    $triggerAlert .= self::isEmail($curFieldValue);
                    if ( $triggerAlert !== '' ) {
                        self::$warning[] = 'El campo ' . $label . ' no tiene un formato válido.';
                    } else {
                        self::$fieldsValues[] = [$field => $curFieldValue];
                        self::$fieldValue[$field] = $curFieldValue;
                    }
                break;

                case 'email':
                    $triggerAlert .= self::isEmail($curFieldValue);
                    if ( $triggerAlert !== '' ) {
                        self::$warning[] = 'El campo ' . $label . ' no tiene un formato válido.';
                    } else {
                        self::$fieldsValues[] = [$field => $curFieldValue];
                        self::$fieldValue[$field] = $curFieldValue;
                    }
                break;

                case 'numeric':
                    $triggerAlert .= self::isNumeric($curFieldValue);
                    if ($triggerAlert==='error') {
                        self::$warning[] = 'El campo ' . $label . ' debe ser numérico.';
                    } else {
                        self::$fieldsValues[] = [$field => $curFieldValue];
                        self::$fieldValue[$field] = $curFieldValue;
                    }
                break;

                case 'idnumber':
                    $triggerAlert .= self::isIdnumber($curFieldValue);
                    if ( $triggerAlert !== '' ) {
                        self::$warning[] = 'El campo ' . $label . ' es incorrecto.';
                    } else {
                        self::$fieldsValues[] = [$field => $curFieldValue];
                        self::$fieldValue[$field] = $curFieldValue;
                    }
                break;

                case 'float':
                    $triggerAlert .= self::isFloat($curFieldValue);
                    if ( $triggerAlert !== '' ) {
                        self::$warning[] = 'El campo ' . $label . ' debe ser un flotante.';
                    } else {
                        self::$fieldsValues[] = [$field => $curFieldValue];
                        self::$fieldValue[$field] = $curFieldValue;
                    }
                break;

                case 'min':
                    $triggerAlert .= self::min($curFieldValue, $min);
                    if ( $triggerAlert !== '' ) {
                           $digits = ($min > 1)? 'caracteres' : 'caracter';
                           self::$warning[] = 'El campo ' . $label . ' debe tener al menos  ' . $min . ' ' . $digits . '.';
                    } else {
                        self::$fieldsValues[] = [$field => $curFieldValue];
                        self::$fieldValue[$field] = $curFieldValue;
                    }
                    break;

                case 'max':
                    $triggerAlert .= self::max($curFieldValue, [$min,$max]);
                    if ( $triggerAlert !== '' ) {
                           self::$warning[] = 'El campo ' . $label . ' debe tener maximo  ' . $max . ' caracteres.';
                    } else {
                        self::$fieldsValues[] = [$field => $curFieldValue];
                        self::$fieldValue[$field] = $curFieldValue;
                    }
                    break;

                case 'length':
                    $length = explode('-', $ruleParam[1]);
                    $minLength = $length[0];
                    $maxLength = $length[1];
                    $triggerAlert .= self::length($curFieldValue, $length);
                    if ( $triggerAlert !== '' ) {
                        if($minLength == $maxLength){

                           if($minLength == 1) {$digits = 'caracter';
                        }else{
                            $digits = 'caracteres'; }
                           self::$warning[] = 'El campo ' . $label . ' sólo admite ' . $minLength . ' ' . $digits . '.';
                        }else{
                            self::$warning[] = 'El campo ' . $label . ' sólo admite de ' . $minLength .' a ' . $maxLength . ' caracteres.';
                        }
                    } else {
                        self::$fieldsValues[] = [$field => $curFieldValue];
                        self::$fieldValue[$field] = $curFieldValue;
                    }
                break;

                # Permitir valor vacío
                case 'set_empty':
                    $triggerAlert .= 'error';
                        self::$fieldsValues[] = [$field => ''];
                        self::$fieldValue[$field] = $curFieldValue;
                break;

                case 'keep_value':
                        self::$fieldsValues[] = [$field => ''];
                        self::$fieldValue[$field] = $curFieldValue;
                break;

                case 'external_error':
                    $triggerAlert .= 'error';
                    self::$warning[] = $message;
                break;
            }
        }
        return $triggerAlert;
    }

    public static function messages()
    {
        return self::$warning;
    }

    public static function fieldsValues()
    {
        return self::$fieldsValues;
    }

    public static function fieldValues()
    {
        return self::$fieldValue;
    }

    public static function isEmpty($value='')
    {
        if ( empty($value) ) {
            return 'error';
        }
        return;

    }

    public static function isIdnumber($value='')
    {
        $result = explode('-', $value);
        $letter = ( isset($result[0]) )? strtoupper($result[0]) : '';
        switch ($letter) {
            case 'E':
                $pattern = "/^[e, E]{1,1}+\-+[0-9]{8,15}+$/";
                break;
            case 'V':
                $pattern = "/^[j, J, v, V, g, G, c, C]{1,1}+\-+[0-9]{8,9}+$/";
                break;
            default:
                $pattern = "/^[j, J, g, G, c, C]{1,1}+\-+[0-9]{9,9}+$/";
                break;
        }

        if ( ! isset($result[1]) ) {
            return 'error';
        }
        if ( self::isInt($result[1]) === 'error' ) {
            return 'error';
        } else {

            if ( ! preg_match($pattern, $value) )
            {
                return 'error';
            }
            return;
        }

    }

    /**
    * Verifica logintud de caracteres según lo especificado en el parametro "$maxlength"
    *
    * limit[0] es el limite menor
    * limit[1] es el limite mayor
    * se condiciona el == 0 par omitir los campos con valor nulo porque ya se validaron en otro script
    */
    public static function min($value,$limit)
    {
        if ( strlen($value) < $limit ){
            return 'error';
        }
        return ;
    }

    public static function max($value,$limit)
    {
        if ( strlen($value) > $limit ){
            return 'error';
        }
        return ;
    }

    public static function length($value,$limit=NULL)
    {
        if ((strlen($value) <= $limit[1]) && (strlen($value) >= $limit[0])  || (strlen($value) == 0)){
            return ;
        }else{
            return 'error';
        }
    }

    /**
    * Verifica si un dato es entero
    * Se hizo necesario sustituir la programación de este metodo porque daba conflicto cuando se intentó validar el phone
    * Anteriormente se usaba is_int
    */
    private static function isInt($value)
    {
        $pattern = "/^[0-9]{0,}+$/";
        if ( ! preg_match($pattern, $value) ){
            return 'error';
        }
        return;
    }

    /**
    * Verifica si un dato es numérico
    */
    private static function isNumeric($value)
    {
        if ( ! is_numeric($value) ){
            return 'error';
        }
        return ;
    }
    /**
    * Verifica si un dato es numérico
    */
    private static function isFloat($value)
    {
        if ( ! is_float($value) ){
            return 'error';
        }
        return ;
    }
    /**
    * Verifica si un dato es string
    */
    public static function isString($value)
    {
        $pattern = "/^[a-z ,A-Z ]{0,}+$/";
        if ( ! preg_match($pattern, $value) ) {
           return 'error';
        }
        return ;
    }

    /**
    * Verifica el formato de fecha
    */
    private static function isDate($date)
    {

        $pattern1 =  "/^[0-9]{2,2}+\-+[0-9]{2,2}+\-+[0-9]{4,4}+$/"; //CON - COMO SEPARADOR
        $pattern2 =  "/^[0-9]{2,2}+\/+[0-9]{2,2}+\/+[0-9]{4,4}+$/"; //CON / COMO SEPARADOR
        if ( ! preg_match($pattern1, $date) )
        {
            if (!preg_match($pattern2, $date)) {
                 return 'error';
                 exit();
            }
        }
        return;
    }

    /**
    * Verifica el formato de fecha
    */
    private static function isPhone($phone)
    {

        $pattern1 =  "/^[0-9]{2,2}+\-+[0-9]{4,4}+\-+[0-9]+$/"; //CON CÓDIGO DE PAÍS COMO SEPARADOR
        $pattern2 =  "/^[0-9]{4,4}+\-+[0-9]+$/"; //CON / COMO SEPARADOR
        if ( ! preg_match($pattern1, $phone) )
        {
            if ( ! preg_match($pattern2, $phone) ) {
                 return 'error';
                 exit();
            }
        }
        return;
    }


    /**
    * Verifica que los e-mails vengan de manera correcta
    */
    private static function isEmail($email)
    {
        $result = filter_var($email, FILTER_VALIDATE_EMAIL);
        if ( !$result ){
            return 'error';
        }
        return;
    }


    private static function isPassword($password)
    {   //allowed ( '>' , '_' , '-' , '/'. '.' )
        $pattern = "/^[a-z,A-Z,0-9,&->_\/().]{0,}+$/";
        if ( ! preg_match($pattern, $password) ) {
            return 'error';
        }
        return ;
    }


    private static function strCompare($init='', $compare='')
    {
        if ( $init !== $compare ) {
            return 'error';
        }
        return;

    }

    public function __destruct(){}

}