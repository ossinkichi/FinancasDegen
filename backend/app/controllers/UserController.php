<?php

namespace app\controllers;

use \Exception;
use Dotenv\Dotenv;
use app\classes\Helper;
use app\classes\JwtHelper;
use app\models\UsersModel;
use PHPMailer\PHPMailer\Exception as PHPMailerException;
use PHPMailer\PHPMailer\PHPMailer;
use stdClass;

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
        $data = $this->helper->getData($data);;

        $user = [
            'email' => filter_var($data['email'], FILTER_SANITIZE_EMAIL),
            'password' => filter_var($data['password'], FILTER_SANITIZE_SPECIAL_CHARS)
        ];

        $response = $this->getUser($user['email']);
        if (empty($response['message'])) {
            $this->helper->message(['message' => 'Usuário não encontrado'], 404);
            return;
        }
        if ($response['status'] == 200) {
            $response['message'] = $this->helper->sanitizeArray($response['message']);
        }

        if (!password_verify($user['password'], $response['message']['password'])) {
            $this->helper->message(['message' => 'Senha ou usuário incorreta'], 401);
            return;
        }

        $this->helper->message(
            ['message' =>
            [
                'user' => $response['message']['userhash'] ?  $response['message']['userhash'] : [],
                'token' => $response['message']['userhash'] ? $this->jwt->generate(60 * 60 * 7) : ''
            ]],
            $response['status']
        );
    }

    public function register(): void
    {
        $this->helper->verifyMethod('POST');
        try {
            $data =  file_get_contents("php://input");
            $data = $this->helper->getData($data);

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

            $reponse = $this->setNewUser($user);
            $this->helper->message(['message' => $reponse['message']], $reponse['status']);
            if ($reponse['status'] == 200) {
                $this->sendEmail([
                    'from' => 'exampleemail@gmail.com',
                    'to' => $user['email'],
                    'fromName' => 'Example Name',
                    'toName' => $user['name'],
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

            $hash = $_GET['user'];

            if (empty($hash)) {
                $this->helper->message(['error' => 'Usuário não identificado'], 400);
                return;
            };
            $response = $this->getUser($hash);

            // if (!$response['message']['emailverify']) {
            //     http_response_code(403);
            //     echo json_encode(['error' => 'Usuario está com a conta inativa, para acessar novamente nossa aplicacao e necessario que ative a sua conta']);
            //     return;
            // };

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
                    'contato' => $response['message']['phone'],
                    'cnpj' => $response['message']['company']
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
        if (empty($data)) {
            $this->helper->message(['error' => 'campo obrigatorio não informado'], 400);
            return;
        }
        $data = $this->helper->getData($data);
        $user = [
            'userhash' => filter_var($data['hash'], FILTER_SANITIZE_SPECIAL_CHARS),
            'name' => filter_var($data['name'], FILTER_SANITIZE_SPECIAL_CHARS),
            'email' => filter_var($data['email'], FILTER_SANITIZE_EMAIL),
            'password' => filter_var($data['password'], FILTER_SANITIZE_SPECIAL_CHARS),
            'dateofbirth' => $data['dateofbirth'],
            'gender' => filter_var($data['gender'], FILTER_SANITIZE_SPECIAL_CHARS),
            'phone' => filter_var($data['phone'], FILTER_SANITIZE_SPECIAL_CHARS)
        ];

        $response = $this->updateDataUser($user['name'], $user['email'], $user['password'], $user['dateofbirth'], $user['gender'], $user['phone'], $data['hash']);
        $this->helper->message(['message' => $response['message']], $response['status']);
    }

    public function delete(): void
    {
        $this->helper->verifyMethod('DELETE');
        $this->jwt->validate();

        try {
            $hash = $_GET['user'];
            if (empty($hash) || !isset($hash)) {
                $this->helper->message(['message' => 'usuario não indentificado'], 400);
                return;
            }
            $response = $this->deleteUser($hash);

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
            $data = $this->helper->getData($data);
            if (!empty($data) && isset($data['user']) && !isset($data['password'])) {
                $response = $this->getUser($data['user']);
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
                    return;
                }
                return;
            };
            if (empty($data) || !isset($data['user']) || empty($data['password'])) {
                $this->helper->message(['message' => 'Dados não informados'], 403);
                return;
            };

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
        $invite = file_get_contents('php://input');

        if (empty($invite) || !isset($invite['invite']) && !isset($invite['company'])) {
            $this->helper->message(['message' => 'Dados não informados'], 403);
            return;
        }


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
            $data = file_get_contents('php://input');

            if (empty($data)) {
                $this->helper->message(['message' => 'Dados não informados']);
                return;
            }

            $data = $this->helper->getData($data);

            $response = $this->setCompany(intval($data['company']), $data['user']);
            $this->helper->message(['message' =>
            [

                'message' => $response['message']
            ]], $response['status']);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function active(): void
    {
        try {
            $this->helper->verifyMethod('GET');;
            $data = $_GET;

            if (empty($data) && !isset($data['user'])) {
                $this->helper->message(['message' => 'Usuário não informado'], 403);
                return;
            }

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

    private function emailConfig(): object
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

    public function inviteCompany() {}
}
