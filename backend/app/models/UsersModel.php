<?php

namespace app\models;

use \PDO;
use \PDOException;
use \app\models\ConnectModel;


class UsersModel extends ConnectModel
{

    /**
     * @return array {status: number, message: array|string}
     */
    protected function getAllUser(): array
    {
        try {
            $sql = $this->connect()->prepare('SELECT * FROM users');
            $sql->execute();

            return ['status' => 200, 'message' => $sql->fetchAll(PDO::FETCH_ASSOC) ?? []];
        } catch (PDOException $pe) {
            throw new PDOException("Erro ao buscar o usuário " . $pe->getMessage(),  (int) $pe->getCode());
        }
    }

    /**
     * @return array {status: number, message: array|string}
     */
    protected function getUser(string $user): array
    {
        try {
            $sql = $this->connect()->prepare('SELECT * FROM users WHERE userhash = :user OR email = :user');
            $sql->bindValue(':user', $user);
            $sql->execute();

            // if ($sql->fetch() == null) {
            //     return ['status' => 403, 'message' => "Não foi possivel buscar o usuário", 'error' => $sql->errorInfo()];
            // }

            return ['status' => 200, 'message' => $sql->fetch(PDO::FETCH_ASSOC) ?? []];
        } catch (PDOException $pe) {
            throw new PDOException("Erro ao buscar usuário " . $pe->getMessage(), (int) $pe->getCode());
        }
    }

    /**
     * @return array {status: number, message: array|string}
     */
    protected function setNewUser(string $userhash, string $name, string $email, string $password, string $cpf, $dateofbirth, string $gender, string $phone, string|null $position): array
    {
        try {
            $sql = $this->connect()->prepare('
        INSERT INTO users(userhash,name, email, password, cpf, dateofbirth, gender, phone,position)
        VALUES(:userhash,:name, :email, :password, :cpf, :dateofbirth, :gender, :phone, :position)
        ');
            $sql->bindValue(':userhash', $userhash);
            $sql->bindValue(':name', $name);
            $sql->bindValue(':email', $email);
            $sql->bindValue(':password', password_hash($password, PASSWORD_DEFAULT));
            $sql->bindValue(':cpf', $cpf);
            $sql->bindValue(':dateofbirth', $dateofbirth);
            $sql->bindValue(':gender', $gender);
            $sql->bindValue(':phone', $phone);
            $sql->bindValue(':position', $position);
            $sql->execute();

            if ($sql->rowCount() === 0) {
                return ['status' => 403, 'message' => 'Não foi possivel cadastrar o usuario', 'error' => $sql->errorInfo()];
            }
            return ['status' => 201, 'message' => ''];
        } catch (PDOException $pe) {
            throw new PDOException("Erro ao criar o usuário: " . $pe->getMessage(), (int) $pe->getCode());
            if ((int) $pe->getCode() == 23000) {
                return ['status' => 500, 'message' => 'Não foi possivel cadastrar o usuario'];
            }
        }
    }

    /**
     * @return array {status: number, message: array|string}
     */
    protected function updateDataUser(
        string $name,
        string $email,
        string $password,
        $dateofbirth,
        string $gender,
        string $phone,
        string $hash
    ): array {
        try {
            $sql = $this->connect()->prepare('UPDATE users SET name = :name, email = :email, password = :password, dateofbirth = :dateofbirth, gender = :gender, phone = :phone WHERE userhash = :hash');
            $sql->bindValue(':name', $name);
            $sql->bindValue(':email', $email);
            $sql->bindValue(':password', password_hash($password, PASSWORD_DEFAULT));
            $sql->bindValue(':dateofbirth', $dateofbirth);
            $sql->bindValue(':gender', $gender);
            $sql->bindValue(':phone', $phone);
            $sql->bindValue(':hash', $hash);
            $sql->execute();

            if ($sql->rowCount() === 0) {
                return ['status' => 403, 'message' => 'Não foi possivel atualizar os dados do cliente'];
            }
            return ['status' => 201, 'message' => ''];
        } catch (PDOException $pe) {
            if ((int) $pe->getCode() == 23000) {
                return ['status' => 403, 'message' => 'Não foi possivel atualizar os dados do cliente'];
            }
            throw new PDOException("Erro ao atualizar usuário: " . $pe->getMessage(), (int) $pe->getCode());
        }
    }

    /**
     * @return array {status: number, message: string|void}
     */
    protected function activateAccount(string $hash): array
    {
        try {
            $sql = $this->connect()->prepare('UPDATE users set emailverify = :value WHERE userhash = :hash');
            $sql->bindValue(':value', true);
            $sql->bindValue(':hash', $hash);
            $sql->execute();

            if ($sql->rowCount() === 0) {
                return ['satus' => 403, 'message' => 'Não foi possivel verificar o email'];
            }
            return ['status' => 201, 'message' => ''];
        } catch (PDOException $pe) {
            throw new PDOException("Erro ao ativar usuário: " . $pe->getMessage(), (int) $pe->getCode());
        }
    }

    /**
     * @return array {status: number, message: string|void}
     */
    protected function deleteUser(string $hash): array
    {
        try {
            $sql = $this->connect()->prepare('DELETE FROM users WHERE userhash = :hash');
            $sql->bindValue(':hash', $hash);
            $sql->execute();

            if ($sql->rowCount() === 0) {
                return ['status' => 400, 'message' => 'Não foi possivel deletar a conta do usuario', 'error' => $sql->errorInfo()];
            }
            return ['status' => 201, 'message' => ''];
        } catch (PDOException $pe) {
            throw new PDOException("Erro ao deletar o usuário: " . $pe->getMessage(), (int) $pe->getCode());
        }
    }

    /**
     * @return array {status: number, message: string|void}
     */
    protected function setCompany(string $company, string $hash)
    {
        try {
            $sql = $this->connect()->prepare('UPDATE users SET company = :company WHERE userhash = :hash');
            $sql->bindValue(':company', $company);
            $sql->bindValue(':hash', $hash);
            $sql->execute();

            if ($sql->rowCount() === 0) {
                return ["status" => 400, "message" => "Erro ao entrar na empresa"];
            }
            return ["status" => 201, "message" => ""];
        } catch (PDOException $pe) {
            if ((int) $pe->getCode() == 23000) {
                return ["status" => 400, "message" => "Não foi possivel entrar na empresa!"];
            }
            throw new PDOException("Erro ao entrar na empresa: " . $pe->getMessage(), (int) $pe->getCode());
        }
    }

    /**
     * @return array {status: number, message: string|void}
     */
    protected function setNewPassword(string $hash, string $password)
    {
        try {
            $sql = $this->connect()->prepare('UPDATE users SET password = :password WHERE userhash = :hash');
            $sql->bindValue(':password', password_hash($password, PASSWORD_DEFAULT));
            $sql->bindValue(':hash', $hash);
            $sql->execute();

            if ($sql->rowCount() === 0) {
                return ['status' => 400, 'message' => 'Não foi possivel atualizar a senha'];
            }
            return ['status' => 201, 'message' => ''];
        } catch (PDOException $pe) {
            throw new PDOException('' . $pe->getMessage(), (int) $pe->getCode());
        }
    }
}
