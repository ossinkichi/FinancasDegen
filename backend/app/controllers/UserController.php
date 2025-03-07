<?php

namespace app\controllers;

use \Exception;
use Dotenv\Dotenv;
use app\classes\Helper;
use app\classes\JwtHelper;
use app\models\UsersModel;
use PHPMailer\PHPMailer\Exception as PHPMailerException;
use PHPMailer\PHPMailer\PHPMailer;

class UserController extends UsersModel
{
    private Helper $helper;
    private JwtHelper $jwt;

    public function __construct()
    {
        $this->helper =  new Helper;
        $this->jwt =  new JwtHelper;
    }

    public function index(): void
    {
        $this->helper->verifyMethod('GET');
        $response = $this->getAllUser();
        if (empty($response)) {
            $this->helper->message(['message' => 'nenhum usuario encontrado'], 400);
            return;
        }
        $this->helper->message(['message' => $response['message'] ? $response['message'] : []], $response['status']);
    }

    public function login(): void
    {
        $this->helper->verifyMethod('POST');
        $data = file_get_contents("php://input");

        $this->helper->arrayValidate($data, [
            'email',
            'password'
        ]);

        $data = $this->helper->getData($data);;
        $user = [
            'email' => filter_var($data['email'], FILTER_SANITIZE_EMAIL),
            'password' => filter_var($data['password'], FILTER_SANITIZE_SPECIAL_CHARS)
        ];

        $response = $this->validateLogin($user);
        $this->helper->message(['message' => $response['message']], $response['status']);
    }

    public function register(): void
    {
        try {
            $this->helper->verifyMethod('POST');
            $data =  file_get_contents("php://input");

            $this->helper->arrayValidate($data, [
                'name',
                'email',
                'password',
                'cpf',
                'dateofbirth',
                'gender',
                'phone'
            ]);

            $data = $this->helper->getData($data);

            $this->userExist(['email' => $data['email']]);

            $user = $this->helper->sanitizeArray($data);
            $user['email'] = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
            $user['userhash'] = $this->createHash($user['cpf']);

            $reponse = $this->setNewUser($user);
            $this->helper->message(['message' => $reponse['message']], $reponse['status']);
            if ($reponse['status'] == 200) {
                $this->sendEmail([
                    'from' => 'exampleemail@gmail.com',
                    'to' => $user['email'],
                    'fromName' => 'Example Name',
                    'toName' => $user['name'],
                    'subject' => 'Resgistro de novo usuário',
                    'message' => 'Olá ' . $user['name'] . ', Seja bem vindo.'
                ]);
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function get(): void
    {
        try {
            $this->helper->verifyMethod('GET');
            $this->jwt->validate();

            $hash = $_GET;
            $this->helper->arrayValidate($hash, ['user']);
            $response = $this->getUser($hash['user']);

            if (!$response['message']['emailverify']) {
                http_response_code(403);
                echo json_encode(['error' => 'Usuario está com a conta inativa, para acessar novamente nossa aplicacao e necessario que ative a sua conta']);
                return;
            };

            $this->helper->message([
                'message' =>
                [
                    'name' => $response['message']['name'],
                    'email' => $response['message']['email'],
                    'verify' => $response['message']['emailverify'],
                    'cargo' => $response['message']['type'],
                    'cpf' => substr($response['message']['cpf'], 0, 3) . str_repeat('*', 7),
                    'nascimento' => str_replace('/', '-', $response['message']['dateofbirth']),
                    'genero' => $response['message']['gender'],
                    'contato' => $response['message']['phone']
                ]
            ], $response['status']);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function update(): void
    {
        $this->helper->verifyMethod('PUT');
        $this->jwt->validate();

        $data = file_get_contents('php://input');
        $this->helper->arrayValidate($data, [
            'hash',
            'name',
            'email',
            'password',
            'dateofbirth',
            'gender',
            'phone'
        ]);
        $data = $this->helper->getData($data);
        $user = $this->helper->sanitizeArray($data);
        $user['email'] = filter_var($data['email'], FILTER_SANITIZE_EMAIL);

        $this->userExist(['email' => $user['email'], 'hash' => $user['hash']]);

        $response = $this->updateDataUser($user['name'], $user['email'], $user['password'], $user['dateofbirth'], $user['gender'], $user['phone'], $user['hash']);
        $this->helper->message(['message' => $response['message']], $response['status']);
    }

    public function delete(): void
    {
        $this->helper->verifyMethod('DELETE');
        $this->jwt->validate();

        try {
            $hash = $_GET;
            $this->helper->arrayValidate($hash, ['user']);
            $response = $this->deleteUser($hash['user']);

            $this->helper->message(['message' => $response['message']], $response['status']);
        } catch (Exception $e) {
            $this->helper->message(['error' => $e->getMessage()], 400);
        }
    }

    public function forgotPassword(): void
    {
        try {
            $this->helper->verifyMethod('PUT');
            $data = file_get_contents('php://input');
            $this->helper->arrayValidate($data, ['user']);
            $data = $this->helper->getData($data);

            $this->sendMessageForForgotPassword($data);
            $this->helper->arrayValidate($data, ['user', 'password']);
            dd($data);
            $response = $this->getUser($data['user']);

            if (password_verify($data['password'], $response['message']['password'])) {
                $this->helper->message(['message' => 'A nova senha não pode ser igual a anterior'], 401);
                return;
            }

            $response = $this->setNewPassword($data['user'], $data['password']);
            $this->helper->message(['message' => $response['message']], $response['status']);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function inviteFromCompany(): void
    {
        $this->helper->verifyMethod('POST');
        $this->jwt->validate();
        $invite = file_get_contents('php://input');
        $this->helper->arrayValidate($invite, ['invite', 'company']);
        $this->helper->getData($invite);

        $response = $this->getUser($invite);
        $this->sendEmail([
            'from' => 'exampleemail@gmail.com',
            'to' => $response['message']['email'],
            'fromName' => 'Example Name',
            'toName' => $response['message']['name'],
            'subject' => 'Alterar senha do usuario',
            'message' => 'Olá ' . ['name'] . ', você foi convidado a entrar na enpresa **, para ingressar click no link a seguir!'
        ]);
    }

    public function join(): void
    {
        try {
            $this->helper->verifyMethod('PUT');
            $this->jwt->validate();
            $data = file_get_contents('php://input');
            $this->helper->arrayValidate($data, ['user', 'company']);
            $data = $this->helper->getData($data);

            $response = $this->setCompany(intval($data['company']), $data['user']);
            $this->helper->message(['message' => ['message' => $response['message']]], $response['status']);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function active(): void
    {
        try {
            $this->helper->verifyMethod('GET');;
            $data = $_GET;
            $this->helper->arrayValidate($data, ['user']);

            $response = $this->activateAccount($data['user']);
            $this->helper->message(['message' => $response['message']], $response['status']);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    private function createHash(string $hash): string
    {
        return hash('sha256', $hash);
    }

    private function emailConfig()
    {
        try {
            $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
            $dotenv->load();

            // Looking to send emails in production? Check out our Email API/SMTP product!
            $phpmailer = new PHPMailer();
            $phpmailer->isSMTP();
            $phpmailer->Host = $_ENV['EMAILHOST'];
            $phpmailer->SMTPAuth = true;
            $phpmailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Tipo de criptografia
            $phpmailer->Port = $_ENV['EMAILPORT'];
            $phpmailer->Username = $_ENV['EMAILUSERNAME'];
            $phpmailer->Password = $_ENV['EMAILPASSWORD'];

            return $phpmailer;
        } catch (PHPMailerException $pme) {
            $this->helper->message(['message' => 'Erro ao configurar o email'], 403);
            throw new PHPMailerException($pme->errorMessage());
        } catch (Exception $e) {
            $this->helper->message(['message' => 'Erro ao configurar o email'], 403);
        }
    }

    private function sendEmail(array $emailData): void
    {
        try {
            $mail = $this->emailConfig();

            //Recipients
            $mail->setFrom($emailData['from'], $emailData['fromName']);
            $mail->addAddress($emailData['to'], $emailData['toName']);

            //Content
            $mail->isHTML(true);
            $mail->Subject = $emailData['subject'];
            $mail->Body    = $emailData['message'];

            $mail->send();
        } catch (PHPMailerException $pme) {
            throw new PHPMailerException($pme->errorMessage());
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
            // $this->helper->message(['message' => 'Não foi possivel enviar o email'], 403);
        }
    }

    private function validateLogin(array $user): array
    {
        $response = $this->getUser($user['email']);
        if (empty($response['message'])) {
            return ['message' => 'Usuário não encontrado', 'status' => 404];
        }

        if (!password_verify($user['password'], $response['message']['password'])) {
            return ['message' => 'Senha ou usuário incorreta', 'status' => 401];
        }

        if ($response['status'] == 200 && is_array($response['message'])) {
            $response['message'] = $this->helper->sanitizeArray($response['message']);
        }

        return [
            'message' => [
                'user' => $response['message']['userhash'] ?  $response['message']['userhash'] : [],
                'token' => $response['message']['userhash'] ? $this->jwt->generate(60 * 60 * 7) : ''
            ],
            'status' => 200
        ];
    }

    private function userExist(array $user): void
    {
        try {
            $response = $this->getUser($user['email']);

            if (is_array($response['message']) && !empty($response['message'])) {
                if (!isset($user['hash'])) {
                    $this->helper->message(['message' => 'Não foi possivel executar a ação'], 401);
                    die();
                }

                if ($response['message']['userhash'] !== $user['hash']) {
                    $this->helper->message(['message' => 'Não foi possivel atualizar os dados'], 400);
                    die();
                }
            }
        } catch (Exception $e) {
            $this->helper->message(['message' => 'Não foi possivel atualizar os dados', 'status' => 400]);
        }
    }

    private function sendMessageForForgotPassword($user)
    {
        if (!isset($user['password'])) {
            $response = $this->getUser($user['user']);
            if ($response['status'] == 200) {
                $this->sendEmail([
                    'from' => 'exampleemail@gmail.com',
                    'to' => $response['message']['email'],
                    'fromName' => 'Example Name',
                    'toName' => $response['message']['name'],
                    'subject' => 'Alterar senha do usuario',
                    'message' => 'Olá ' . $response['name'] . ', você solicitou uma troca de senha? Caso tenha sido você, clique no link a seguir <b>youtube.com</b>. caso não, ignore!.'
                ]);
            } else {
                $this->helper->message(['message' => $response['message']], $response['status']);
                die();
            }
        }
    }
}
