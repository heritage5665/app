<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\TaskException;
use App\Exception\UserException;
use Exception;
use Respect\Validation\Validator as v;

abstract class BaseService
{
    protected static function validateUserName(string $name): string
    {
        if (!v::alnum()->length(2, 100)->validate($name)) {
            throw new UserException('Invalid name.', 400);
        }

        return $name;
    }
    protected static function validateUserId($userId)
    {
        if (!v::alnum()->length(12, 23)->validate($userId)) {
            throw new UserException('Invalid user ID', 400);
        }
        return $userId;
    }
    protected static function validatePhone($phone)
    {
        if (!v::phone()->validate($phone));
    }
    protected static function validateGender(string $gender)
    {
        if (!v::alpha()->length(2, 10)->validate($gender)) {
            throw new UserException('Invalid Gender', 400);
        }
        return $gender;
    }
    protected static function validateProfileStatus($status)
    {
        if (!v::alnum()->length(2, 2255)->validate($status)) {
            throw new UserException('Invalid profile status provided', 400);
        }
        return htmlentities($status);
    }

    protected static function validateEmail(string $emailValue): string
    {
        $email = filter_var($emailValue, FILTER_SANITIZE_EMAIL);
        if (!v::email()->validate($email)) {
            throw new UserException('Invalid email', 400);
        }

        return $email;
    }

    // protected static function validateTourName(string $name): string
    // {
    //     if (!v::length(2, 100)->validate($name)) {
    //         throw new TaskException('Invalid name.', 400);
    //     }

    //     return $name;
    // }

    // protected static function validateRole(int $status): int
    // {
    //     if (!v::numeric()->between(0, 1)->validate($status)) {
    //         throw new TaskException('Invalid status', 400);
    //     }

    //     return $status;
    // }
}
