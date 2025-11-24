<?php
// utils/response.php
class Response {
    public static function json($data, $status_code = 200) {
        http_response_code($status_code);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit();
    }

    public static function error($message, $status_code = 400) {
        self::json([
            'success' => false,
            'error' => $message
        ], $status_code);
    }

    public static function success($data, $message = null) {
        $response = [
            'success' => true,
            'data' => $data
        ];
        
        if ($message) {
            $response['message'] = $message;
        }
        
        self::json($response);
    }
}

?>