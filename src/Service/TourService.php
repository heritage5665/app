<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\AuthException;
use App\Exception\UserException;
use App\Repository\TourRepository;
use stdClass;

class TourService extends BaseService
{
    protected $tourRepository;

    protected $redisService;

    public function __construct(TourRepository $tourRepository, RedisService $redisService)
    {
        $this->tourRepository = $tourRepository;
        $this->redisService = $redisService;
    }


    public function getTours(): array
    {
        return $this->tourRepository->getTours();
    }

    public function getTour($userId)
    {
        $userTour =  $this->tourRepository->checkAndGetTour($userId);
        if (empty($userTour)) {
            throw new UserException("no tour found for this user", 400);
        }
        return $userTour;
    }


    public function createTour($input)
    {
        $tour = new \stdClass();
        $data = json_decode(json_encode($input), false);
        if (!isset($data->articlesId)) {
            throw new AuthException('Invalid data: articles Id is required.', 400);
        }
        if (!isset($data->userId)) {
            throw new AuthException('Invalid data: articles Id is required.', 400);
        }
        $tour->artilceId = filter_input(INPUT_POST, $data->articlesId);
        $tour->userId = self::validateUserId($data->userId);

        $tours = $this->tourRepository->createTour($data);
        //$key = $this->redisService->generateKey("note:" . $tours->userId);
        // $this->redisService->setex($key, $tours);

        return $tours;
    }

    public function deleteTour($input, $userId)
    {
        $tour = new \stdClass();
        $data = json_decode(json_encode($input), false);

        if (!isset($data->articlesId)) {
            throw new AuthException('Invalid data: tour id is required');
        }
        if (!isset($userId)) {
            throw new AuthException('Invalid data: user id is required');
        }
        $tour->id = (int) filter_var($data->articlesId);
        $userId = self::validateUserId($userId);
        return $this->tourRepository->deleteTour($tour->id, $userId);
    }


    public function checkAndGetTourLikes($articleId)
    {
        return $this->tourRepository->getTourLikes($articleId);
    }

    public function likeTour(int $userId, int $articleId)
    {
        $likes = $this->checkAndGetTourLikes($articleId);
        if ($likes) {
            $likes = (int) $likes->likes + 1;
        } else {
            return false;
        }
        $this->tourRepository->updateTourLikes($userId, $articleId, $likes);

        return 'likes updated successfully';
    }
}
