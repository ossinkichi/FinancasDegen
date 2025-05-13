<?php

namespace App\Controllers;

use \Exception;
use Dotenv\Dotenv;
use Klein\Request;
use Klein\Response;
use PHPMailer\PHPMailer\PHPMailer;
use App\Controllers\BaseController;
use App\DTO\UserDto;
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
        $payload = \json_decode($request->body(), true); // Recebe os dados do front

        // Valida o usúario
        $this->validateLogin(userData: $payload, response: $response);
    }

    // Registra um novo usuário
    public function create(Request $request, Response $response): Response
    {
        try {
            $playload = \json_decode($request->body(), true); // Recebe os dados do front
            $userDto = UserDto::make($playload); // Cria um novo objeto UserDto com os dados recebidos

            // Verifica se todos os dados necessários foram enviados
            $userDto->userhash = $this->createHash(hash: $userDto->cpf); // Cria um hash para o usuário


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
    */
    // Atualiza os dados do usuário
    public function update(Request $request, Response $response): Response
    {
        $this->jwtHelper->validate($response); // Verifica se o token é valido
        try {
            $payload = \json_decode($request->body(), true); // Recebe os dados do front
            // $userDto = UserDto::make($payload); // Cria um novo objeto UserDto com os dados recebidos

            $this->repository->updateDataUser($payload); // Faz o pedido ao banco de dados e recebe sua resposta

            return $this->successRequest(response: $response, payload: [], statusCode: 201); // Retorna os dados ao front
        } catch (Exception $e) {
            return $this->errorRequest($response, throwable: $e, context: [
                'message' => 'Erro ao atualizar o usuário',
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ])->code(500); // Retorna o erro ao front
        }
    }

    // Delete um usuario
    public function delete(Request $request, Response $response): Response
    {

        $this->jwtHelper->validate($response); // Verifica se o token é valido
        try {
            $hash = $request->param('hash'); // Recebe o hash do usuario

            $this->repository->deleteUser(hash: $hash); // Faz o pedido ao banco de dados e recebe sua resposta

            return $this->successRequest(response: $response, payload: [], statusCode: 201);
        } catch (Exception $e) {
            return $this->errorRequest($response, throwable: $e, context: [
                'message' => 'Erro ao deletar o usuário',
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ])->code(500); // Retorna o erro ao front
        }
    }

    public function forgoatPasswordSendEmail(Request $request, Response $response): Response
    {
        try {
            $body = \json_decode($request->body(), true);  // Recebe os dados do front

            $userExist = $this->userExist(user: $body['user'], response: $response); // Verifica se o usúario já está cadastrado

            if ($userExist['deleted']) {
                return $this->errorRequest(response: $response, throwable: new Exception(), context: [
                    'message' => 'Email ou senha incorreto',
                ])->code(401); // Retorna o erro ao front
            }

            return $this->successRequest(response: $response, payload: [
                'message' => 'Email enviado com sucesso, por favor verifique sua caixa de entrada!',
                'data' => $userExist->userhash, // Converte os dados para JSON,
                'token' => $this->jwtHelper->generate(time: (60 * 30)), // Cria o token
            ], statusCode: 200); // Retorna os dados ao front
        } catch (Exception $e) {
            return $this->errorRequest($response, throwable: $e, context: [
                'message' => 'Erro ao enviar o email',
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ])->code(500); // Retorna o erro ao front
        }
    }


    public function forgoatPassword(Request $request, Response $response): Response
    {
        try {
            $body = \json_decode($request->body(), true);  // Recebe os dados do front

            $userExist = $this->userExist(user: $body['user'], response: $response); // Verifica se o usúario já está cadastrado

            if ($userExist->deleted) {
                return $this->errorRequest(response: $response, throwable: new Exception(), context: [
                    'message' => 'Email ou senha incorreto',
                ])->code(401); // Retorna o erro ao front
            }

            $this->passwordChangeVerification(
                password: $userExist->password,
                newPassword: $body['password'],
                newPasswordConfirm: $body['passwordConfirm'],
                response: $response
            ); // Verifica se as senhas são iguais

            $this->repository->setNewPassword(user: $body['user'], password: $body['password']);
            return $this->successRequest(response: $response, payload: [], statusCode: 201); // Retorna os dados ao front
        } catch (Exception $e) {
            return $this->errorRequest($response, throwable: $e, context: [
                'message' => 'Erro ao recuperar a senha do usuário',
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ])->code(500); // Retorna o erro ao front
        }
    }

    public function ChangePassword(Request $request, Response $response): Response
    {
        try {
            $payload = \json_decode($request->body(), true);  // Recebe os dados do front

            $userExist = $this->userExist(user: $payload['user'], response: $response); // Verifica se o usúario já está cadastrado

            $this->passwordChangeVerification(
                password: $userExist->password,
                newPassword: $payload['password'],
                newPasswordConfirm: $payload['passwordConfirm'],
                response: $response
            ); // Verifica se as senhas são iguais

            $this->repository->setNewPassword(user: $payload['user'], password: $payload['password']);

            return $this->successRequest(response: $response, payload: [], statusCode: 201); // Retorna os dados ao front
        } catch (Exception $e) {
            return $this->errorRequest($response, throwable: $e, context: [
                'message' => 'Erro ao trocar a senha do usuário',
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ])->code(500); // Retorna o erro ao front
        }
    }

    private function passwordChangeVerification(string $password, string $newPassword, string $newPasswordConfirm, Response $response)
    {
        if ($newPassword !== $newPasswordConfirm) {
            return $this->errorRequest(response: $response, throwable: new Exception(), context: [
                'message' => 'As senhas não conferem',
            ])->code(401); // Retorna o erro ao front
        }

        if (password_verify($password, $newPassword)) {
            return $this->errorRequest(response: $response, throwable: new Exception(), context: [
                'message' => 'A senha não pode ser igual a anterior',
            ]); // Retorna o erro ao front
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
    */
    /*/ Ingresa o usuário a uma empresa
    public function join(Request $request, Response $response): Response
    {
        try {
            $this->jwtHelper->validate($response); // Verifica se o token é valido
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

    /*/
    public function active(Request $request, Response $response): Response
    {
        try {
            $param = $request->param('hash'); // Recebe o hash do usuario

            $this->userExist(user: $param, response: $response);

            $this->repository->activateAccount(hash: $param); // Faz o pedido ao banco de dados e recebe sua resposta

            return $this->successRequest(response: $response, payload: [], statusCode: 201);
        } catch (Exception $e) {
            return $this->errorRequest(response: $response, throwable: $e, context: [
                'message' => 'Não foi possivel ativar a conta',
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ],);
        }
    }

    public function sendEmailForVerificationAccount(Request $request, Response $response): Response
    {
        try {

            return $this->successRequest(response: $response, payload: [
                'message' => 'Email enviado com sucesso, por favor verifique sua caixa de entrada!',
            ], statusCode: 200); // Retorna os dados ao front
        } catch (Exception $e) {
            return $this->errorRequest($response, throwable: $e, context: [
                'message' => 'Erro ao enviar o email de verificação',
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ])->code(500); // Retorna o erro ao front
        }
    }

    // Cria um hash para o usuario
    private function createHash(string $hash): string
    {
        return hash('sha256', $hash);
    }

    // Configura o email
    private function emailConfig(): PHPMailer
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
            throw new PHPMailerException($pme->errorMessage());
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
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
        }
    }

    private function validateLogin(array $userData, Response $response): Response
    {
        try {
            $user = $this->userExist(user: $userData['email'], response: $response); // Faz o pedido ao banco de dados e recebe sua resposta

            if ($user->deleted == false) {
                return $this->errorRequest(response: $response, throwable: new Exception(), context: [
                    'message' => 'Email ou senha incorreto',
                ])->code(401); // Retorna o erro ao front
            }

            if (!password_verify(password: $userData['password'], hash: $user->password)) {
                return $this->errorRequest(response: $response, throwable: new Exception(), context: [
                    'message' => 'Email ou senha incorreto',
                ])->code(401); // Retorna o erro ao front
            }

            if ($user->emailverify !== true) {
                return $this->errorRequest(response: $response, throwable: new Exception(), context: [
                    'message' => 'Email não verificado',
                ])->code(401); // Retorna o erro ao front
            }

            $user = $user->jsonSerialize(); // Converte os dados para JSON
            unset($user['password']); // Remove a senha do usuário

            return $this->successRequest(response: $response, payload: [
                'message' => 'Usuário encontrado',
                'data' => $user,
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
}
