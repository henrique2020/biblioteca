<?php
namespace App\Auth;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtService {
    public static function create(array $payload): string {
        $payload['iat'] = time();
        $payload['exp'] = time() + (60 * 60); // 1 hora
        return JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');
    }

    public static function validate(string $token): ?object {
        try {
            return JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
        } catch (\Exception $e) {
            return null;
        }
    }
    
    public static function getBearerToken(): ?string {
        $headers = getallheaders();
        if (isset($headers['Authorization'])) {
            if (preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
                return $matches[1];
            }
        }
        return null;
    }
}
