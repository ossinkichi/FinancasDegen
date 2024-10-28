<?php

namespace app\controllers;

use \app\models\UsersModel;
use app\classes\Helper;
use app\classes\JwtHelper;
use Dotenv\Util\Str;
use Exception;

class UserController extends UsersModel
{
    private object $helper;
    private $jwt;

    public function __construct()
    {
        $this->helper =  new Helper;
        $this->jwt =  new JwtHelper;
    }

    public function get()
    {
        $this->helper->verifyMethod('GET');
        dd($this->getAllUser());
        $data = $this->getAllUser();
        $this->helper->message(['data' => $data ?? '']);
    }

    public function login(string $email, string $password): void
    {
        // $this->helper->verifyMethod('POST');

        $data = [
            'user' => filter_var($email, FILTER_SANITIZE_EMAIL),
            'password' => filter_var($password, FILTER_SANITIZE_SPECIAL_CHARS)
        ];

        if (!empty($field)) {
            $this->helper->message(['message' => 'O campo obrigatorio não preenchido'], 400);
            return;
        }

        $userData = $this->getUser($data);

        // if (!isset($userData['active']) || empty($userData)) {
        //     $this->helper->message(['error' => 'Usuário está com a conta inativa ou inexistente'], 403);
        //     return;
        // };

        if (!password_verify($data['password'], $userData['password'])) {
            $this->helper->message(['message' => 'Senha ou usuário incorreta'], 401);
            return;
        }

        $this->helper->message(['token' => $this->jwt->generate(['user' => $userData['userhash'], 'message' => 'Login efetuado com sucesso'], (60 * 60 * 7))], 200);
    }

    public function register(
        string $name,
        string $email,
        string $password,
        string $cpf,
        $dateofbirth,
        string $gender,
        string $phone
    ): void {
        $this->helper->verifyMethod('POST');

        try {
            $user = [
                'name' => filter_var($name, FILTER_SANITIZE_SPECIAL_CHARS),
                'email' => filter_var($email, FILTER_SANITIZE_EMAIL),
                'password' => filter_var($password, FILTER_SANITIZE_SPECIAL_CHARS),
                'cpf' => filter_var($cpf, FILTER_SANITIZE_SPECIAL_CHARS),
                'dateofbirth' => $dateofbirth,
                'gender' => filter_var($gender, FILTER_SANITIZE_SPECIAL_CHARS),
                'phone' => filter_var($name, FILTER_SANITIZE_SPECIAL_CHARS)
            ];

            if (empty($user)) {
                $this->helper->message(['error' => 'Campo obrigatorio não informado'], 400);
                return;
            }

            $user['userhash'] = $this->createHash($user['cpf']);

            $this->setNewUser($user);
            $this->helper->message(['message' => 'success']);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function getDataUser($data): void
    {
        try {
            $this->helper->verifyMethod('GET');
            $this->jwt->validate($data->outher);

            if (empty($hash)) {
                http_response_code(400);
                echo json_encode(['error' => 'É necessario passar o hash para que a busca seja feita']);
                return;
            }

            $userData = $this->getUser(['user' => $hash->paramether]);

            if (!$userData['active']) {
                http_response_code(403);
                echo json_encode(['error' => 'Usuario está com a conta inativa, para acessar novamente nossa aplicacao e necessario que ative a sua conta']);
                return;
            };

            echo json_encode(['user' => $userData]);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function update(
        string $token,
        string $hash,
        string $name,
        string $email,
        string $password,
        string $cpf,
        $dateofbirth,
        string $gender,
        int $phone
    ) {
        $this->helper->verifyMethod('POST');
        $this->jwt->validate($token);

        $user = [
            'userhash' => filter_var($hash, FILTER_SANITIZE_SPECIAL_CHARS),
            'name' => filter_var($name, FILTER_SANITIZE_SPECIAL_CHARS),
            'email' => filter_var($email, FILTER_SANITIZE_EMAIL),
            'password' => filter_var($password, FILTER_SANITIZE_SPECIAL_CHARS),
            'cpf' => filter_var($cpf, FILTER_SANITIZE_SPECIAL_CHARS),
            'dateofbirth' => $dateofbirth,
            'gender' => filter_var($gender, FILTER_SANITIZE_SPECIAL_CHARS),
            'phone' => filter_var($name, FILTER_SANITIZE_SPECIAL_CHARS)
        ];

        if (empty($user)) {
            $this->helper->message(['error' => 'campo obrigatorio não informado'], 400);
            return;
        }

        $this->updateDataUser($user);
        $this->helper->message(['message' => 'success']);
    }

    public function delete(object $hash) {}

    public function desactivateAccount(object|int $hash)
    {
        $this->helper->verifyMethod('GET');
    }

    private function createHash(string $hash): string
    {
        return hash('sha256', $hash);
    }
}
