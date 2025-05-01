<?php

namespace App\Classes;

use \Exception;
use Dotenv\Dotenv;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use app\Classes\Helper;
use Firebase\JWT\ExpiredException;
use Klein\Response;

class JwtHelper
{
    use Helper;

    private static string $key;

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();
        self::$key = $_ENV['TOKEN'];
    }

    public function generate(int $time, array|string $data = []): string
    {
        try {
            $payload = [
                "iss" => "example.com",     // Emissor do token
                "aud" => "example.com",     // Destinatário do token
                "iat" => time(),            // Data de criação do token
                "exp" => time() + $time,     // Data de expiração
                "data" => $data
            ];

            return JWT::encode($payload, self::$key, 'HS256');
        } catch (Exception $e) {
            throw new Exception('Error ao gerar JWT' . $e->getMessage());
        }
    }

    public function validate(Response $response =  Response::class)
    {
        try {

            $jwt = getallheaders();
            if (!isset($jwt['authorization'])) {
                $this->message(['message' => 'acesso negado'], 401);
                die();
            }

            $jwtDecoded = get_object_vars(JWT::decode($jwt['authorization'], new Key(self::$key, 'HS256')));

            if (!$jwtDecoded) {
                $this->message(['message' => 'acesso negado'], 401);
                die();
            }
        } catch (ExpiredException  $e) {
            $this->message(['message' => 'Acesso negado'], 401);
            die();
        } catch (Exception $e) { {
                $this->message(['message' => 'token inválido'], 401);
                die();
            }
        }
    }
}
