<?php

namespace app\controllers;

use \Exception;
use Dotenv\Dotenv;
use Klein\Request;
use Klein\Response;
use app\classes\Helper;
use app\classes\JwtHelper;
use app\models\UsersModel;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

class UserController extends UsersModel
{
    private Helper $helper;
    private JwtHelper $jwt;

    public function __construct()
    {
        $this->helper =  new Helper;
        $this->jwt =  new JwtHelper;
    }

    public function index(Request $request, Response $response): Response
    {
        try {
            $res = $this->getAllUser(); // Faz o pedido ao banco de dados

            // Verifica se houve retorno
            if (empty($res)) {
                return $response->code(404)->header('Content-Type', 'application/json')->body(\json_encode(['message' => 'nenhum usuario encontrado']));
            }
            // Sanitiza os dados
            \is_array($res['message']) ?
                $res['message'] = \array_map(function ($user) {
                    $user = $this->helper->sanitizeArray($user);
                    foreach (['id', 'password'] as $chave) {
                        unset($user[$chave]);
                    }
                    return $user;
                },  $res['message'])
                : null;

            // Envia uma resposta ao front
            return $response->code($res['status'])->header('Content-Type', 'application/json')->body(\json_encode(['message' => $res['message'], 'error' => $res['error'] ?? []]));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function login(Request $request, Response $response): Response
    {
        $data = \json_decode($request->body());

        $this->helper->arrayValidate($data, [
            'email',
            'password'
        ]);

        $user = [
            'email' => filter_var($data['email'], FILTER_SANITIZE_EMAIL),
            'password' => filter_var($data['password'], FILTER_SANITIZE_SPECIAL_CHARS)
        ];

        $res = $this->validateLogin($user);
        return $response->code($res['status'])
            ->header('Content-Type', 'application/json')
            ->body(\json_encode($res['message']));
    }

    // Registra um novo usuário
    public function create(Request $request, Response $response): Response
    {
        try {
            $data = \json_decode($request->body(), true); // Recebe os dados do front

            // Verifica se todos os dados necessários foram enviados
            $this->helper->arrayValidate($data, [
                'name',
                'email',
                'password',
                'cpf',
                'dateofbirth',
                'gender',
                'phone'
            ]);

            // Verifica se o usúario já está cadastrado
            $this->userExist(['email' => $data['email']]);

            // Sanitiza os dados
            $user = $this->helper->sanitizeArray($data);
            $user['email'] = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
            $user['hash'] = $this->createHash($user['cpf']);

            // Converte os tipos dos dados
            $user = $this->helper->convertType($user, ['string', 'string', 'string', 'string', 'string', 'string', 'string', 'string', 'string']);

            // Faz o pedido ao banco e recebe sua resposta
            $res = $this->setNewUser($user['hash'], $user['name'], $user['email'], $user['cpf'], $user['password'], $user['dateofbirth'], $user['gender'], $user['phone'], $user['position']);

            // Dá um retorno ao front
            return $response->code($res['status'])
                ->header('Content-Type', 'application/json')
                ->body(\json_encode(['message' => $res['message'], 'error' => $res['error'] ?? []]));

            // Envia um email ao usuário cadastrado
            /*if ($res['status'] == 201) {
                $this->sendEmail([
                    'from' => 'exampleemail@gmail.com',
                    'to' => $user['email'],
                    'fromName' => 'Example Name',
                    'toName' => $user['name'],
                    'subject' => 'Resgistro de novo usuário',
                    'message' => 'Olá ' . $user['name'] . ', Seja bem vindo.'
                ]);
            }*/
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function get(Request $request, Response $response): Response
    {
        try {
            $this->jwt->validate();

            $hash = $request->param('user');
            $this->helper->arrayValidate([$hash], [0]);
            $res = $this->getUser($hash);

            if (!$response['message']['emailverify']) {
                return $response->code(403)->header('Content-Type', 'aplication/json')->body(['message' => 'Usuario está com a conta inativa, para acessar novamente nossa aplicacao e necessario que ative a sua conta']);
            };

            return $response->code($res['status'])->header('Content-Type', 'aplication/json')->body(\json_encode([
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
            ]));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function update(Request $request, Response $response): Response
    {
        $this->jwt->validate();

        $data = \json_decode($request->body());
        $this->helper->arrayValidate($data, [
            'hash',
            'name',
            'email',
            'password',
            'dateofbirth',
            'gender',
            'phone'
        ]);

        $user = $this->helper->sanitizeArray($data);
        $user['email'] = filter_var($data['email'], FILTER_SANITIZE_EMAIL);

        $this->userExist(['email' => $user['email'], 'hash' => $user['hash']]);

        $res = $this->updateDataUser($user['name'], $user['email'], $user['password'], $user['dateofbirth'], $user['gender'], $user['phone'], $user['hash']);
        return $response->code($res['status'])->header('Content-Type', 'application/json')->body(\json_encode($res['message']));
    }

    public function delete(Request $request, Response $response): Response
    {
        $this->helper->verifyMethod('DELETE');
        $this->jwt->validate();

        try {
            $hash = $request->param('user');
            $this->helper->arrayValidate($hash, ['user']);
            $res = $this->deleteUser($hash['user']);

            return $response->code($res['status'])->header('Content-Type', 'application/json')->body(\json_encode($res['message']));
        } catch (Exception $e) {
            throw new Exception('Erro ao deletar: ' . $e->getMessage());
        }
    }

    public function forgotPassword(Request $request, Response $response): Response
    {
        try {
            $data = \json_decode($request->body());
            $this->helper->arrayValidate($data, ['user']);
            $data = $this->helper->getData($data);

            $this->sendMessageForForgotPassword($data);
            $this->helper->arrayValidate($data, ['user', 'password']);

            $res = $this->getUser($data['user']);

            if (password_verify($data['password'], $response['message']['password'])) {
                return $response->code(401)->header('Content-Type', 'aplication/json')->body(['message' => 'A nova senha não pode ser igual a anterior']);
            }

            $res = $this->setNewPassword($data['user'], $data['password']);
            return $response->code($res['status'])->header('Content-Type', 'application/json')->body(\json_encode($res['message']));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function inviteFromCompany(Request $request, Response $response): Response
    {

        $this->jwt->validate();
        $invite = \json_decode($request->body());
        $this->helper->arrayValidate($invite, ['invite', 'company']);
        $this->helper->getData($invite);

        $res = $this->getUser($invite);
        $this->sendEmail([
            'from' => 'exampleemail@gmail.com',
            'to' => $res['message']['email'],
            'fromName' => 'Example Name',
            'toName' => $res['message']['name'],
            'subject' => 'Alterar senha do usuario',
            'message' => 'Olá ' . ['name'] . ', você foi convidado a entrar na enpresa **, para ingressar click no link a seguir!'
        ]);
        return $response->code($res['status'])->header('Content-Type', 'aplication/json')->body([]);
    }

    public function join(Request $request, Response $response): Response
    {
        try {

            $this->jwt->validate();
            $data = \json_decode($request->body());
            $this->helper->arrayValidate($data, ['user', 'company']);

            $res = $this->setCompany(intval($data['company']), $data['user']);
            return $response->code($res['status'])->header('Content-type', 'aplication/json')->body($res['message']);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function active(Request $request, Response $response): Response
    {
        try {

            $data = $request->param('user');
            $this->helper->arrayValidate($data, [0]);

            $res = $this->activateAccount($data['user']);
            return $response->code($res['status'])->header('Content-Type', 'application/json')->body(\json_encode($res['message']));
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
                'user' => $response['message']['userhash'] ?? [],
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
