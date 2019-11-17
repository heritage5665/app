<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\UserException;
use App\Repository\UserRepository;
use \Firebase\JWT\JWT;
use PDOException;

class UserService extends BaseService
{
    protected $userRepository;

    protected $redisService;

    public function __construct(UserRepository $userRepository, RedisService $redisService)
    {
        $this->userRepository = $userRepository;
        $this->redisService = $redisService;
    }

    protected function checkAndGetUser(string $userId)
    {
        return $this->userRepository->checkAndGetUser($userId);
    }

    public function getUsers(): array
    {
        return $this->userRepository->getUsers();
    }

    public function getUser($userId)
    {
        // $key = $this->redisService->generateKey("user:$email");
        // if ($this->redisService->exists($key)) {
        //     $data = $this->redisService->get($key);
        //     $user = json_decode(json_encode($data), false);
        // } else {
        //     $user = $this->checkAndGetUser($email);
        //     //$this->redisService->setex($key, $user);
        // }
        $user = $this->checkAndGetUser($userId);

        return $user;
    }

    // public function searchUsers(string $usersName): array
    // {
    //     return $this->userRepository->searchUsers($usersName);
    // }

    public function createUser($input)
    {
        $user = new \stdClass();
        $data = json_decode(json_encode($input), false);
        if (!isset($data->firstName)) {
            throw new UserException('The field "First Name" is required.', 400);
        }
        if (!isset($data->lastName)) {
            throw new UserException('The field "last Name" is required.', 400);
        }
        if (!isset($data->gender)) {
            throw new UserException('The field "gender" is required.', 400);
        }

        if (!isset($data->email)) {
            throw new UserException('The field "email" is required.', 400);
        }
        if (!isset($data->password)) {
            throw new UserException('The field "password" is required.', 400);
        }
        if (!isset($data->userType)) {

            throw new UserException('The field "password" is required.', 400);
        }
        $user->userId = \uniqid();
        $user->firstName = self::validateUserName($data->firstName);
        $user->lastName = self::validateUserName($data->lastName);
        $user->gender  = self::validateGender($data->gender);
        $user->email = self::validateEmail($data->email);
        $user->password = hash('sha512', $data->password);
        $user->is_admin = htmlentities((int) $data->userType);
        $this->userRepository->checkUserByEmail($user->email);
        $users = $this->userRepository->createUserProfile($user);
        // $key = $this->redisService->generateKey("user:" . $users->user_id);
        // $this->redisService->setex($key, $users);

        return $users;
    }


    public function updateUser(array $input, $userId)
    {
        $response = array();
        $user = array();
        $data = json_decode(json_encode($input), false);
        if (isset($userId)) {
            $user['userId'] = self::validateUserId($userId);
            $userExit = $this->userRepository->checkAndGetUser($input->userId);
        }

        if (!$userExit) {
            throw new UserException('the user did not exit', 400);
        }

        if (!isset($data->userId) && !isset($data->email)) {
            throw new UserException('The user id and email is empty.', 400);
        }

        if (isset($data->firstname)) {
            $user->first_name = self::validateUserName($data->firstname);
        }
        if (isset($data->lastname)) {
            $user->last_name = self::validateUserName($data->lastname);
        }

        if (isset($data->profile_status)) {
            $user->profile_status = self::validateProfileStatus($data->profile_status);
        }
        if (isset($data->gender)) {
            $user->gender = self::validateGender($data->gender);
        }
        if (isset($data->phone_num)) {
            $user->phone_num = self::validatePhone($data->phone_num);
        }

        $user['email'] = self::validateEmail($data->email);
        try {
            $response['users'] = $this->userRepository->updateUser($user);

            $response['profile'] = $this->updateUserProfile($user);
            //$key = $this->redisService->generateKey("user:" . $response['users']->id);
            //$this->redisService->setex($key, $response['users']->user->id);

        } catch (PDOException $e) {
            return $e->getMessage();
        }

        return \json_encode($response);
    }

    public function deleteUser(string $userId) //: string
    {
        $this->checkAndGetUser($userId);
        $this->userRepository->deleteUserTasks($userId);
        $data = $this->userRepository->deleteUser($userId);
        $key = $this->redisService->generateKey("user:" . $userId);
        $this->redisService->del($key);

        // return $data;
    }

    public function loginUser(?array $input): string
    {
        $data = json_decode(json_encode($input), false);
        if (!isset($data->email)) {
            throw new UserException('The field "email" is required.', 400);
        }
        if (!isset($data->password)) {
            throw new UserException('The field "password" is required.', 400);
        }
        $password = hash('sha512', $data->password);
        $user = $this->userRepository->loginUser($data->email, $password);
        $token = array(
            'sub' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'iat' => time(),
            'exp' => time() + (7 * 24 * 60 * 60),
        );

        return JWT::encode($token, getenv('SECRET_KEY'));
    }

    public function updateUserProfile(array $user)
    {
        return $this->userRepository->updateUserProfile($user);
    }
}
