<?php

namespace App\Shared;

use Dotenv\Dotenv;
use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT as FirebaseJWT;
use Firebase\JWT\Key;

class JWT
{
    private static string $key;

    private static Helper $helper;

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__.'/../../');
        $dotenv->load();
        self::$key = $_ENV['TOKEN'];
        self::$helper = new Helper;
    }

    public function generate(int $time, array|string $data = []): string
    {
        try {
            $payload = [
                'iss' => 'example.com',     // Emissor do token
                'aud' => 'example.com',     // Destinatário do token
                'iat' => time(),            // Data de criação do token
                'exp' => time() + $time,     // Data de expiração
                'data' => $data,
            ];

            return FirebaseJWT::encode($payload, self::$key, 'HS256');
        } catch (Exception $e) {
            throw new Exception('Error ao gerar JWT'.$e->getMessage());
        }
    }

    public function validate()
    {
        try {

            $jwt = getallheaders();

            // dd($jwt);
            if (! isset($jwt['authorization'])) {
                self::$helper->message(['message' => 'acesso negado'], 401);
                exit();
            }

            $jwtDecoded = get_object_vars(FirebaseJWT::decode($jwt['authorization'], new Key(self::$key, 'HS256')));

            if (! $jwtDecoded) {
                self::$helper->message(['message' => 'acesso negado'], 401);
                exit();
            }
        } catch (ExpiredException  $e) {
            self::$helper->message(['message' => 'Acesso negado'], 401);
            exit();
        } catch (Exception $e) {
            self::$helper->message(['message' => 'token inválido'], 401);
            exit();

        }
    }
}
