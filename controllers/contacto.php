<?php
require_once '../backendVistaMontana/utils/response.php';
require_once '../backendVistaMontana/utils/validator.php';

// controllers/contacto.php

function enviarMensajeContacto() {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (empty($data['nombre']) || empty($data['email']) || empty($data['mensaje'])) {
        Response::error('Todos los campos son obligatorios', 400);
    }

    if (!Validator::validateEmail($data['email'])) {
        Response::error('Email inválido', 400);
    }

    // Aquí implementarías el envío de email
    $to = "contacto@vistamontana.com"; // Tu email
    $subject = "Nuevo mensaje de contacto - Hospedaje Vista Montaña";
    $message = "Nombre: " . $data['nombre'] . "\n";
    $message .= "Email: " . $data['email'] . "\n";
    $message .= "Teléfono: " . ($data['telefono'] ?? 'No proporcionado') . "\n\n";
    $message .= "Mensaje:\n" . $data['mensaje'];
    
    $headers = "From: " . $data['email'];

    if (mail($to, $subject, $message, $headers)) {
        Response::success(null, 'Mensaje enviado exitosamente');
    } else {
        Response::error('Error al enviar el mensaje', 500);
    }
}
?>