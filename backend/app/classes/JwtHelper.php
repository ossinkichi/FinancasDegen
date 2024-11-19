<?php

namespace App\Classes;

use \Exception;
use Dotenv\Dotenv;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Classes\Helper;
use Firebase\JWT\ExpiredException;

class JwtHelper
{

    private static string $key;
    private static Helper $helper;

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();
        self::$key = $_ENV['TOKEN'];
        self::$helper = new Helper;
    }

    public function generate(int $time)
    {
        try {
            $payload = [
                "iss" => "example.com",     // Emissor do token
                "aud" => "example.com",     // Destinatário do token
                "iat" => time(),            // Data de criação do token
                "exp" => time() + $time,     // Data de expiração
            ];

            return JWT::encode($payload, self::$key, 'HS256');
        } catch (Exception $e) {
            throw new Exception('Error ao gerar JWT' . $e->getMessage());
        }
    }

    public function validate()
    {

        try {
            $jwt = getallheaders();
            if (!isset($jwt['Authorization'])) {
                self::$helper->message(['message' => 'acesso negado'], 401);
                die();
            }
            $jwtDecoded = get_object_vars(JWT::decode($jwt['Authorization'], new Key(self::$key, 'HS256')));

            if (!$jwtDecoded) {
                self::$helper->message(['message' => 'acesso negado'], 401);
                die();
            }
        } catch (ExpiredException  $e) {
            self::$helper->message(['message' => 'Acesso negado'], 401);
            die();
        } catch (Exception $e) { {
                self::$helper->message(['message' => 'token inválido'], 401);
                die();
            }
        }
    }
}
