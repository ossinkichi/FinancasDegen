<?php

namespace App\Classes;

use Dotenv\Dotenv;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Classes\Helper;
use Exception;

class JwtHelper
{

    private string $key;
    private object $helper;

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();
        $this->key = $_ENV['TOKEN'];
        $this->helper = new Helper;
    }

    public function generate(array $data, int $time)
    {
        try {
            $payload = [
                "iss" => "example.com",     // Emissor do token
                "aud" => "example.com",     // Destinatário do token
                "iat" => time(),            // Data de criação do token
                "exp" => time() + $time,     // Data de expiração
                "data" => $data
            ];

            return JWT::encode($payload, $this->key, 'HS256');
        } catch (Exception $e) {
            throw new Exception('Error ao gerar JWT' . $e->getMessage());
        }
    }

    public function validate(string $jwt)
    {
        try {
            if (isset($jwt)) {
                return JWT::decode($jwt, new Key($this->key, 'HS256'));
            }

            $this->helper->message(['message' => 'acesso negado'], 403);
        } catch (Exception $e) {
            throw new Exception('Erro no token' . $e->getMessage());
        }
    }
}
