<?php

declare(strict_types=1);

namespace App\Repository;

use App\Exception\UserException;
use PDOException;
use SebastianBergmann\CodeCoverage\Exception;

class UserRepository extends BaseRepository
{
    public function __construct(\PDO $database)
    {
        $this->database = $database;
    }

    public function checkAndGetUser(string $userId)
    {
        $query = 'SELECT `user_id`, `email` FROM `auth` WHERE `user_id` = :userid';
        $statement = $this->database->prepare($query);
        $statement->bindParam('userid', $userId);
        $statement->execute();
        $user = $statement->fetchAll();
        if (empty($user)) {
            throw new UserException('User not found.', 404);
        }

        return $user;
    }

    public function checkUserByEmail(string $email)
    {
        $query = 'SELECT * FROM `auth` WHERE `email` = :email';
        $statement = $this->database->prepare($query);
        $statement->bindParam('email', $email);
        $statement->execute();
        $user = $statement->fetchObject();
        if (empty(!$user)) {
            throw new UserException('Email already exists.', 400);
        }
    }

    public function getUsers(): array
    {
        $query = 'SELECT `user_id`, `email` FROM `auth` ORDER BY `user_id`';
        $statement = $this->database->prepare($query);
        $statement->execute();

        return $statement->fetchAll();
    }

    // public function searchUsers(string $usersName): array
    // {
    //     $query = 'SELECT `id`, `email` FROM `auth` WHERE UPPER(name) LIKE :name ORDER BY `id`';
    //     $name = '%' . $usersName . '%';
    //     $statement = $this->database->prepare($query);
    //     $statement->bindParam('name', $name);
    //     $statement->execute();
    //     $users = $statement->fetchAll();
    //     if (!$users) {
    //         throw new UserException('User name not found.', 404);
    //     }

    //     return $users;
    // }

    public function loginUser(string $email, string $password)
    {
        $query = 'SELECT * FROM `auth` WHERE `email` = :email AND `password` = :password ORDER BY `user_id`';
        $statement = $this->database->prepare($query);
        $statement->bindParam('email', $email);
        $statement->bindParam('password', $password);
        $statement->execute();
        $user = $statement->fetchObject();
        if (empty($user)) {
            throw new UserException('Login failed: Email or password incorrect.', 400);
        }

        return $user;
    }

    public function createUser($user)
    {
        $query = 'INSERT INTO `auth` (`user_id`,`email`, `password`,`is_admin`) VALUES (:user_id, :email, :password, :is_admin)';
        $statement = $this->database->prepare($query);
        $statement->bindparam('user_id', $user->userId);
        $statement->bindParam('email', $user->email);
        $statement->bindParam('password', $user->password);
        $statement->bindParam('is_admin', $user->is_admin);


        $statement->execute();

        return $this->checkAndGetUser($user->userId);
    }

    public function createUserProfile($user)
    {
        $users = $this->createUser($user);
        try {

            $query = 'INSERT INTO `profiles` (`user_id`,`first_name`, `last_name`,`gender`,`email`) 
                    VALUES (:user_id, :firstName, :lastName, :gender, :email)';

            $statement = $this->database->prepare($query);
            $statement->bindParam('firstName', $user->firstName);
            $statement->bindParam('lastName', $user->lastName);
            $statement->bindParam('gender', $user->gender);
            $statement->bindParam('email', $user->email);
            $statement->bindParam('user_id', $user->userId);
            $statement->execute();
            // $users = $this->createUser($user);
            $profile = $this->getUserProfile($user->userId);
            $data['user'] = $users;
            $data['profle'] = $profile;
            return $data;
        } catch (PDOException $e) {

            throw new UserException("Sign up failed! $e", 500);
        }
    }

    function getUserProfile(string $userId)
    {
        try {
            $query = 'SELECT * FROM profiles WHERE `user_id` = :userId';
            $statement  = $this->database->prepare($query);
            $statement->bindParam('userId', $userId);
            $statement->execute();

            return $statement->fetchAll();
        } catch (PDOException $e) {
            return new UserException('could not fetch the specified user', 400);
        }
    }
    public function updateUser($user)
    {

        $query = 'UPDATE `auth` SET `email` = :email WHERE `user_id` = :user';
        $statement = $this->database->prepare($query);
        $statement->bindParam('user', $user->userId);
        $statement->bindParam('email', $user->email);
        $statement->execute();

        return $this->checkAndGetUser($user->userId);
    }
    public function updateUserProfile($user)
    {

        try {
            ksort($user);
            $fieldDetails = NULL;
            foreach ($user as $key => $value) {
                $fieldDetails .= "`$key`=:$key,";
            }
            $query = 'UPDATE `profile` SET $fieldDetails WHERE `user_id` = :user';
            $statement = $this->database->prepare($query);
            foreach ($user as $key => $value) {
                $statement->bindValue(":$key", $value);
            }
            if ($statement->execute()) {
                if (!$this->updateUser($user->userId)) {
                    throw new UserException("user not fould", 400);
                }
                return $this->getUserProfile($user['userProfile']);
            }
        } catch (PDOException $e) {
            return new UserException($e->getMessage(), 500);
        }
    }



    public function deleteUser(string $userId): string
    {
        $query = 'DELETE FROM `auth` WHERE `user_id` = :id';
        $statement = $this->database->prepare($query);
        $statement->bindParam('id', $userId);
        $statement->execute();

        return 'The user was deleted.';
    }


    public function deleteUserProfile(int $userId)
    {
        $query = 'DELETE FROM `profile` WHERE `user_id` = :userId';
        $statement = $this->database->prepare($query);
        $statement->bindParam('userId', $userId);
        $statement->execute();
    }
}
