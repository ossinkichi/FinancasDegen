<?php

namespace App\Repositories;

use \PDO;
use App\DTO\UserDto;
use App\Entities\UserEntity;
use App\Concern\InteractsWithDatabase;
use App\Exceptions\RepositoryException;

class UsersRepository
{
    use InteractsWithDatabase;

    /**
     * @return array {status: number, message: array|string}
     */
    public function getAllUser(): array
    {
        $sql = $this->connect()->prepare('SELECT * FROM users');
        $sql->execute();

        return \array_map(fn($model) => UserEntity::make($model), $sql->fetchAll(PDO::FETCH_ASSOC));
    }

    public function getUser(string $user): object
    {
        $sql = $this->connect()->prepare('SELECT * FROM users WHERE userhash = :user OR email = :user');
        $sql->bindValue(':user', $user);
        $sql->execute();

        return UserEntity::make($sql->fetch(PDO::FETCH_ASSOC));
    }

    /**
     * @return array {status: number, message: array|string}
     */
    public function setNewUser(UserDto $userDto): void
    {
        $sql = $this->connect()->prepare('
        INSERT INTO users(userhash,name, email, password, cpf, dateofbirth, gender, phone,position)
        VALUES(:userhash,:name, :email, :password, :cpf, :dateofbirth, :gender, :phone, :position)
        ');
        $sql->bindValue(':userhash', $userDto->userhash);
        $sql->bindValue(':name', $userDto->name);
        $sql->bindValue(':email', $userDto->email);
        $sql->bindValue(':password', password_hash($userDto->password, PASSWORD_DEFAULT));
        $sql->bindValue(':cpf', $userDto->cpf);
        $sql->bindValue(':dateofbirth', $userDto->dateofbirth);
        $sql->bindValue(':gender', $userDto->gender);
        $sql->bindValue(':phone', $userDto->phone);
        $sql->bindValue(':position', $userDto->position);
        $sql->execute();

        if ($sql->rowCount() === 0) {
            throw RepositoryException::entityNotFound('users', $userDto->name);
        }
    }

    /**
     * @return array {status: number, message: array|string}
     */
    public function updateDataUser(array $user): void
    {

        $sql = $this->connect()->prepare('UPDATE users SET name = :name, email = :email, dateofbirth = :dateofbirth, gender = :gender, phone = :phone WHERE userhash = :hash OR email = :email');
        $sql->bindValue(':hash', $user['userhash']);
        $sql->bindValue(':name', $user['name']);
        $sql->bindValue(':email', $user['email']);
        $sql->bindValue(':dateofbirth', $user['dateofbirth']);
        $sql->bindValue(':gender', $user['gender']);
        $sql->bindValue(':phone', $user['phone']);
        $sql->execute();

        if ($sql->rowCount() === 0) {
            throw RepositoryException::entityNotFound('users', $user['name']);
        }
    }

    /**
     * @return array {status: number, message: string|void}
     */
    public function activateAccount(string $hash): void
    {
        $sql = $this->connect()->prepare('UPDATE users set emailverify = :value WHERE userhash = :hash');
        $sql->bindValue(':value', true);
        $sql->bindValue(':hash', $hash);
        $sql->execute();

        if ($sql->rowCount() === 0) {
            throw RepositoryException::entityNotFound('users', $hash);
        }
    }

    /**
     * @return array {status: number, message: string|void}
     */
    public function deleteUser(string $hash): void
    {
        $sql = $this->connect()->prepare('UPDATE users set deleted = true WHERE userhash = :hash');
        $sql->bindValue(':hash', $hash);
        $sql->execute();

        if ($sql->rowCount() === 0) {
            throw RepositoryException::entityNotFound('users', $hash);
        }
    }

    /**
     * @return array {status: number, message: string|void}
     */
    public function setCompany(UserDto $userDto): void
    {
        $sql = $this->connect()->prepare('UPDATE users SET company = :company WHERE userhash = :hash');
        $sql->bindValue(':company', $userDto->company);
        $sql->bindValue(':hash', $userDto->userhash);
        $sql->execute();

        if ($sql->rowCount() === 0) {
            throw RepositoryException::entityNotFound('users', $userDto->name);
        }
    }

    /**
     * @return array {status: number, message: string|void}
     */
    public function setNewPassword(string $user, string $password): void
    {
        $sql = $this->connect()->prepare('UPDATE users SET password = :password WHERE userhash = :hash OR email = :user');
        $sql->bindValue(':password', password_hash($password, PASSWORD_DEFAULT));
        $sql->bindValue(':user', $user);
        $sql->execute();

        if ($sql->rowCount() === 0) {
            throw RepositoryException::entityNotFound('users', $user);
        }
    }
}
