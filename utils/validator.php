<?php
// utils/validator.php
class Validator {
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function validateDate($date) {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    public static function validateReserva($data) {
        $errors = [];

        if (empty($data['nombre_cliente'])) {
            $errors[] = 'El nombre del cliente es obligatorio';
        }

        if (empty($data['email_cliente']) || !self::validateEmail($data['email_cliente'])) {
            $errors[] = 'Email inválido';
        }

        if (empty($data['fecha_entrada']) || !self::validateDate($data['fecha_entrada'])) {
            $errors[] = 'Fecha de entrada inválida';
        }

        if (empty($data['fecha_salida']) || !self::validateDate($data['fecha_salida'])) {
            $errors[] = 'Fecha de salida inválida';
        }

        if (!empty($data['fecha_entrada']) && !empty($data['fecha_salida'])) {
            $entrada = new DateTime($data['fecha_entrada']);
            $salida = new DateTime($data['fecha_salida']);
            
            if ($salida <= $entrada) {
                $errors[] = 'La fecha de salida debe ser posterior a la fecha de entrada';
            }
        }

        if (empty($data['cantidad_personas']) || $data['cantidad_personas'] < 1) {
            $errors[] = 'La cantidad de personas debe ser al menos 1';
        }

        return $errors;
    }
}

?>