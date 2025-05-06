<?php

namespace App\Controllers;

use \Exception;
use Dotenv\Dotenv;
use Klein\Request;
use Klein\Response;
use PHPMailer\PHPMailer\PHPMailer;
use App\Controllers\BaseController;
use App\DTO\UserDto;
use App\Repositories\UserRepository;
use App\Repositories\UsersRepository;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

class UserController extends BaseController
{

    private UsersRepository $repository;

    public function __construct()
    {
        parent::__construct();
        $this->repository = new UsersRepository(); // Instancia o repositório de usuários
    }

    // Busca todos os usúarios
    public function index(Request $request, Response $response): Response
    {
        try {
            $users = $this->repository->getAllUser(); // Faz o pedido ao banco de dados

            return $this->successRequest(response: $response, payload: [
                'data' => \array_map(fn($data) => $data->jsonSerialize(), $users), // Converte os dados para JSON
                'message' => 'Usuários encontrados',
            ]); // Retorna os dados ao front
        } catch (Exception $e) {
            return $this->errorRequest($response, throwable: $e, context: [
                'message' => 'Erro ao buscar os usuários',
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ])->code(500); // Retorna o erro ao front
        }
    }

    // Verifica se o usuario tem uma conta
    public function login(Request $request, Response $response): void # Atualmente está dando erro
    {
        $Payload = \json_decode($request->body(), true); // Recebe os dados do front
        $userDto = UserDto::make($Payload);

        // Valida o usúario
        $this->validateLogin(userDto: $userDto, response: $response);
    }

    // Registra um novo usuário
    public function create(Request $request, Response $response): Response
    {
        try {
            $playload = \json_decode($request->body(), true); // Recebe os dados do front
            $userDto = UserDto::make($playload); // Cria um novo objeto UserDto com os dados recebidos

            // Verifica se todos os dados necessários foram enviados
            $user['hash'] = $this->createHash(hash: $userDto->cpf); // Cria um hash para o usuário


            // Faz o pedido ao banco e recebe sua resposta
            $this->repository->setNewUser(userDto: $userDto); // Faz o pedido ao banco de dados e recebe sua resposta

            return $this->successRequest(response: $response, payload: [], statusCode: 201); // Retorna os dados ao front

            // Envia um email ao usuário cadastrado
            /*
            if ($res['status'] == 201) {
                $this->sendEmail([
                    'from' => 'exampleemail@gmail.com',
                    'to' => $user['email'],
                    'fromName' => 'Example Name',
                    'toName' => $user['name'],
                    'subject' => 'Resgistro de novo usuário',
                    'message' => 'Olá ' . $user['name'] . ', Seja bem vindo.'
                ]);
            }
            */
        } catch (Exception $e) {
            return $this->errorRequest($response, throwable: $e, context: [
                'message' => 'Erro ao cadastrar o usuário',
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ])->code(500); // Retorna o erro ao front
        }
    }

    /*/ Busca um usuário pelo hash
    public function get(Request $request, Response $response): Response
    {
        try {
            $this->jwt->validate(); // Verifica se o token é valido

            $hash = $request->param('hash'); // Recebe o hash do usuario
            $this->helper->arrayValidate([$hash]); // Verifica se o dado foi enviado
            $hash = $this->helper->convertType([$hash], ['string'])[0]; // Converte o tipo do dado
            $hash = $this->helper->sanitizeArray([$hash])[0]; // Sanitiza o dado

            $res = $this->getUser($hash); // Faz o pedido ao banco de dados e recebe sua resposta

            // Verifica se houve retorno
            if (empty($res)) {
                return $response
                    ->code(404)
                    ->header('Content-Type', 'aplication/json')
                    ->body(['message' => 'nenhum usuario encontrado']);
            }

            // Dá um retorno ao front
            return $response
                ->code($res['status'])
                ->header('Content-Type', 'aplication/json')
                ->body(\json_encode([
                    'message' =>
                    [
                        'name' => $res['message']['name'],
                        'email' => $res['message']['email'],
                        'verify' => $res['message']['emailverify'],
                        'cargo' => $res['message']['position'],
                        'cpf' => substr(str_repeat('*', 8) . $res['message']['cpf'],  -3),
                        'nascimento' => str_replace('/', '-', $res['message']['dateofbirth']),
                        'genero' => $res['message']['gender'],
                        'contato' => $res['message']['phone']
                    ],
                    'error' => $res['error'] ?? []
                ]));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    // Atualiza os dados do usuário
    public function update(Request $request, Response $response): Response
    {
        $this->jwt->validate(); // Verifica se o token é valido

        $data = \json_decode($request->body(), true); // Recebe os dados do front
        // Verifica se todos os dados necessários foram enviados
        $this->helper->arrayValidate($data, [
            'hash',
            'name',
            'email',
            'password',
            'dateofbirth',
            'gender',
            'phone'
        ]);

        // Sanitiza os dados
        $user = $this->helper->sanitizeArray($data);
        $user['email'] = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
        // Converte os tipos dos dados
        $user =  $this->helper->convertType($user, ['string', 'string', 'string', 'string', 'string', 'string', 'string', 'string']);
        // Verifica se o usúario existe
        $userExist = $this->userExist($user['hash']);

        if ($userExist) {
            return $response->code(401)->header('Content-Type', 'application/json')->body(\json_encode(['message' => 'Usuário não encontrado']));
        }

        // Faz o pedido ao banco e recebe sua resposta
        $res = $this->updateDataUser($user['name'], $user['email'], $user['password'], $user['dateofbirth'], $user['gender'], $user['phone'], $user['hash']);

        // Dá um retorno ao front
        return $response
            ->code($res['status'])
            ->header('Content-Type', 'application/json')
            ->body(\json_encode(['message' => $res['message'], 'error' => $res['error'] ?? []]));
    }

    // Delete um usuario
    public function delete(Request $request, Response $response): Response
    {

        try {
            $this->jwt->validate(); // Verifica se o token é valido

            $hash = $request->param('hash'); // Recebe o hash do usuario
            $this->helper->arrayValidate([$hash], ['0']); // Verifica se o dado foi enviado
            $hash = $this->helper->sanitizeArray([$hash])[0]; // Sanitiza o dado
            $hash = $this->helper->convertType([$hash], ['string'])[0]; // Converte o tipo do dado

            $res = $this->deleteUser($hash); // Faz o pedido ao banco de dados e recebe sua resposta

            // Verifica se houve retorno
            if (empty($res)) {
                return $response->code(404)->header('Content-Type', 'application/json')->body(\json_encode(['message' => 'nenhum usuario encontrado']));
            }

            // Dá um retorno ao front
            return $response
                ->code($res['status'])
                ->header('Content-Type', 'application/json')
                ->body(\json_encode(['message' => $res['message'], 'error' => $res['error'] ?? []]));
        } catch (Exception $e) {
            throw new Exception('Erro ao deletar: ' . $e->getMessage());
        }
    }
    /*
    public function forgoatPassword(Request $request, Response $response): Response
    {
        try {
            $body = \json_decode($request->body(), true);  // Recebe os dados do front

            $this->helper->arrayValidate($body, ['user', 'password']); // Verifica se todos os dados necessários foram enviados
            $body = $this->helper->sanitizeArray($body); // Sanitiza os dados
            $body = $this->helper->convertType($body, ['string', 'string']); // Converte os tipos dos dados

            $userExist = $this->userExist($body['user']); // Verifica se o usúario já está cadastrado

            if (!$userExist) {
                return $response->code(401)->header('Content-Type', 'aplication/json')->body(\json_encode(['message' => 'Usuário não encontrado']));
            }


            // Verifica se A senha é igual a anterior
            if (password_verify($body['password'], $userExist['password'])) {
                return $response->code(401)->header('Content-Type', 'aplication/json')->body(\json_encode(['message' => 'A nova senha não pode ser igual a anterior']));
            }

            $res = $this->setNewPassword($body['user'], $body['password']);
            // \dd($body);
            return $response
                ->code($res['status'])
                ->header('Content-Type', 'application/json')
                ->body(\json_encode(['message' => $res['message'], 'error' => $res['error'] ?? []]));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    /*
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

    // Ingresa o usuário a uma empresa
    public function join(Request $request, Response $response): Response
    {
        try {
            $this->jwt->validate(); // Verifica se o token é valido
            $data = $request->params(['company', 'user']); // Recebe os dados do front

            $this->helper->arrayValidate($data, ['user', 'company']); // Verifica se todos os dados necessários foram enviados
            $data = $this->helper->sanitizeArray($data); // Sanitiza os dados
            $data = $this->helper->convertType($data, ['string', 'string']); // Converte os tipos dos dados

            // Faz o pedido ao banco e recebe sua resposta
            $res = $this->setCompany($data['company'], $data['user']);

            // Verifica se houve retorno
            if (empty($res) || !\is_array($res)) {
                return $response->code(404)->header('Content-Type', 'aplication/json')->body(\json_encode(['message' => 'Não foi possivel ingressar na empresa!']));
            }

            // Dá um retorno ao front
            return $response
                ->code($res['status'])
                ->header('Content-type', 'aplication/json')
                ->body(\json_encode(['message' => $res['message'], 'error' => $res['error'] ?? []]));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    // Ativa a conta do usuário
    public function active(Request $request, Response $response): Response
    {
        try {
            $data = $request->param('hash'); // Recebe o hash do usuario
            $this->helper->arrayValidate([$data], ['0']); // Verifica se o dado foi enviado
            $data = $this->helper->convertType([$data], ['string'])[0]; // Converte o tipo do dado
            $data = $this->helper->sanitizeArray([$data])[0]; // Sanitiza o dado

            $res = $this->activateAccount($data); // Faz o pedido ao banco de dados e recebe sua resposta

            // Verifica se houve retorno
            if (empty($res)) {
                // Se não houver retorno, retorna um erro 404
                return $response
                    ->code(404)
                    ->header('Content-Type', 'application/json')
                    ->body(\json_encode(['message' => 'nenhum usuario encontrado']));
            }

            // Se houver retorno, retorna o status e a mensagem
            return $response
                ->code($res['status'])
                ->header('Content-Type', 'application/json')
                ->body(\json_encode(['message' => $res['message'], 'error' => $res['error'] ?? []]));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    */
    // Cria um hash para o usuario
    private function createHash(string $hash): string
    {
        return hash('sha256', $hash);
    }

    /*
    // Configura o email
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
    */
    private function validateLogin(UserDto $userDto, Response $response): Response
    {
        try {
            $user = $this->userExist(user: $userDto->email, response: $response); // Faz o pedido ao banco de dados e recebe sua resposta

            if ($user->deleted) {
                return $this->errorRequest(response: $response, throwable: new Exception(), context: [
                    'message' => 'Email ou senha incorreto',
                ])->code(401); // Retorna o erro ao front
            }

            if (!\password_verify(password: $userDto->password, hash: $user->password)) {
                return $this->errorRequest(response: $response, throwable: new Exception(), context: [
                    'message' => 'Email ou senha incorreto',
                ])->code(401); // Retorna o erro ao front
            }

            if (!$user->emailverify) {
                return $this->errorRequest(response: $response, throwable: new Exception(), context: [
                    'message' => 'Email não verificado',
                ])->code(401); // Retorna o erro ao front
            }

            return $this->successRequest(response: $response, payload: [
                'message' => 'Usuário encontrado',
                'data' => $user->jsonSerialize(), // Converte os dados para JSON
                'token' => $this->jwtHelper->generate(time: (60 * 60 * 2)), // Cria o token
            ]); // Retorna os dados ao front
        } catch (Exception $e) {
            return $this->errorRequest($response, throwable: $e, context: [
                'message' => 'Erro ao validar o login',
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ])->code(500); // Retorna o erro ao front
        }
    }

    private function userExist(string $user, Response $response): object
    {
        try {
            // Faz o pedido ao banco de dados e recebe sua resposta
            $userData = $this->repository->getUser(user: $user);

            if (empty($userData)) {
                return $this->errorRequest(response: $response, throwable: new Exception, context: [
                    'message' => 'Usuário não encontrado',
                    'error' => 'Usuário não encontrado',
                ])->code(401); // Retorna o erro ao front
            }

            return $userData;
        } catch (Exception $e) {
            return $this->errorRequest($response, throwable: $e, context: [
                'message' => 'Erro ao buscar o usuário',
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ])->code(500); // Retorna o erro ao front
        }
    }

    /*
    // Envia um email para o usuário com o link para alterar a senha
    public function sendMessageForForgoatPassword(Request $request, Response $response): void
    {
        $user = \json_decode($request->body(), true); // Recebe os dados do front
        $this->helper->arrayValidate($user, ['user', 'message']); // Verifica se todos os dados necessários foram enviados
        $user = $this->helper->sanitizeArray($user); // Sanitiza os dados
        $user = $this->helper->convertType($user, ['string']); // Converte os tipos dos dados

        $this->userExist($user['user']); // Verifica se o usúario já está cadastrado

        // Faz o pedido ao banco de dados e recebe sua resposta
        $response = $this->getUser($user['user']);

        //     // Verifica se houve retorno
        //     if (empty($response))
        // {
        //     return;
        //     if (!isset($user['password'])) {
        //         $response = $this->getUser($user['user']);
        //         if ($response['status'] == 200) {
        //             $this->sendEmail([
        //                 'from' => 'exampleemail@gmail.com',
        //                 'to' => $response['message']['email'],
        //                 'fromName' => 'Example Name',
        //                 'toName' => $response['message']['name'],
        //                 'subject' => 'Alterar senha do usuario',
        //                 'message' => 'Olá ' . $response['name'] . ', você solicitou uma troca de senha? Caso tenha sido você, clique no link a seguir <b>youtube.com</b>. caso não, ignore!.'
        //             ]);
        //         } else {
        //             $this->helper->message(['message' => $response['message']], $response['status']);
        //             die();
        //         }
        //     }
        // }
    }
        */
}
