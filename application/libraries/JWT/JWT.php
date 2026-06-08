<?php
require_once APPPATH . 'libraries/JWT/JWT.php';

use Firebase\JWT\JWT;

class TokenHandler
{
    private static $secret_key = 'your_secret_key'; // Ganti dengan key rahasia Anda
    private static $algorithm = 'HS256';

    // Generate Token
    public static function generateToken($userData)
    {
        $payload = [
            'iat' => time(),
            'exp' => time() + (60 * 60), // Token berlaku 1 jam
            'data' => $userData
        ];
        return JWT::encode($payload, self::$secret_key, self::$algorithm);
    }

    // Validasi Token
    public static function validateToken($token)
    {
        try {
            return JWT::decode($token, self::$secret_key, [self::$algorithm]);
        } catch (Exception $e) {
            return false;
        }
    }
}
