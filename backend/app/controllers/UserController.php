<?php

namespace app\controllers;

use \app\models\UsersModel;
use app\classes\Helper;
use app\classes\JwtHelper;
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

    public function index()
    {
        $this->helper->verifyMethod('GET');
        $data = $this->getAllUser();
        if (empty($data)) {
            $this->helper->message(['message' => 'nenhum usuario encontrado'], 400);
            return;
        }
        $this->helper->message(['data' => $data ?? '']);
    }

    public function login(): void
    {

        $this->helper->verifyMethod('POST');
        $data = get_object_vars(json_decode(file_get_contents("php://input")));

        $user = [
            'user' => filter_var($data['email'], FILTER_SANITIZE_EMAIL),
            'password' => filter_var($data['password'], FILTER_SANITIZE_SPECIAL_CHARS)
        ];
        foreach ($user as $key) {
            if (empty($key)) {
                $this->helper->message(['message' => 'O campo obrigatorio não preenchido'], 400);
                return;
            }
        }

        $userData = $this->getUser($user['user']);

        if (empty($userData)) {
            $this->helper->message(['message' => 'Usuário não encontrado'], 404);
            return;
        }

        if (!isset($userData['active']) || empty($userData) && !empty($userData['company'])) {
            $this->helper->message(['error' => 'Usuário está com a conta inativa ou inexistente'], 403);
            return;
        };

        if (!password_verify($user['password'], $userData['password'])) {
            $this->helper->message(['message' => 'Senha ou usuário incorreta'], 401);
            return;
        }

        $this->helper->message(['user' => $userData['userhash'], 'message' => 'success']);
    }

    public function register(): void
    {
        $this->helper->verifyMethod('POST');
        try {
            $data =  get_object_vars(json_decode(file_get_contents("php://input")));

            $user = [
                'name' => filter_var($data['name'], FILTER_SANITIZE_SPECIAL_CHARS),
                'email' => filter_var($data['email'], FILTER_SANITIZE_EMAIL),
                'password' => filter_var($data['password'], FILTER_SANITIZE_SPECIAL_CHARS),
                'cpf' => filter_var($data['cpf'], FILTER_SANITIZE_SPECIAL_CHARS),
                'dateofbirth' => $data['dateofbirth'],
                'gender' => filter_var($data['gender'], FILTER_SANITIZE_SPECIAL_CHARS),
                'phone' => filter_var($data['phone'], FILTER_SANITIZE_SPECIAL_CHARS)
            ];

            $user['userhash'] = $this->createHash($user['cpf']);

            $this->setNewUser($user);
            $this->helper->message(['message' => 'success']);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function get(): void
    {
        try {
            $this->helper->verifyMethod('GET');

            $hash = $_GET['user'];

            if (empty($hash)) {
                $this->helper->message(['error' => 'Usuário não identificado'], 400);
                return;
            };
            $userData = $this->getUser($hash);

            // if (!$userData['active']) {
            //     http_response_code(403);
            //     echo json_encode(['error' => 'Usuario está com a conta inativa, para acessar novamente nossa aplicacao e necessario que ative a sua conta']);
            //     return;
            // };

            $this->helper->message(['user' => $userData]);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function update()
    {
        $this->helper->verifyMethod('PUT');

        $data = $_POST;
        $user = [
            'userhash' => filter_var(json_decode($data['hash']), FILTER_SANITIZE_SPECIAL_CHARS),
            'name' => filter_var(json_decode($data['name']), FILTER_SANITIZE_SPECIAL_CHARS),
            'email' => filter_var(json_decode($data['email']), FILTER_SANITIZE_EMAIL),
            'password' => filter_var(json_decode($data['password']), FILTER_SANITIZE_SPECIAL_CHARS),
            'cpf' => filter_var(json_decode($data['cpf']), FILTER_SANITIZE_SPECIAL_CHARS),
            'dateofbirth' => json_decode($data['dateofbirth']),
            'gender' => filter_var(json_decode($data['gender']), FILTER_SANITIZE_SPECIAL_CHARS),
            'phone' => filter_var(json_decode($data['phone']), FILTER_SANITIZE_SPECIAL_CHARS)
        ];

        if (empty($user)) {
            $this->helper->message(['error' => 'campo obrigatorio não informado'], 400);
            return;
        }

        $this->updateDataUser($user);
        $this->helper->message(['message' => 'success']);
    }

    public function delete()
    {
        $this->helper->verifyMethod('DELETE');
        try {
            $hash = $_GET['user'];
            if (empty($hash) || !isset($hash)) {
                $this->helper->message(['erro' => 'usuario não indentificado', 'hash' => $hash], 400);
                return;
            }
            $this->deleteUser($hash);
        } catch (Exception $e) {
            $this->helper->message(['error' => $e->getMessage()], 400);
        }
    }

    public function activated()
    {
        $this->helper->verifyMethod('GET');
        try {
        } catch (Exception $e) {
            $this->helper->message(['error' => $e->getMessage()]);
        }
    }

    public function desactivate()
    {
        $this->helper->verifyMethod('GET');
    }

    public function forgotPassword(){}

    public function joinTheCompany(){}
    
    private function createHash(string $hash): string
    {
        return hash('sha256', $hash);
    }
}
