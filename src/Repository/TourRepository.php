<?php

declare(strict_types=1);

namespace App\Repository;

use App\Exception\TourException;
use PDOException;

class TourRepository extends BaseRepository
{
    public function __construct(\PDO $database)
    {
        $this->database = $database;
    }

    public function checkAndGetTour($userId)
    {
        $query = 'SELECT articletitle,article_id, articleimage,longitude,latitude,actual_location,parentid,user_id,date_created FROM (mobile_articles m INNER JOIN tours t ON m.articlesid = t.article_id) WHERE `user_id` =:userId AND `status` = :status';
        $statement = $this->database->prepare($query);
        $status = 1;
        $statement->bindParam(':status', $status);
        $statement->bindParam(':userId', $userId);
        $statement->execute();
        $tours = $statement->fetchAll(\PDO::FETCH_ASSOC);
        if (empty($tours)) {
            throw new TourException('Note not found.', 404);
        }

        return $tours;
    }
    public function checkTourByStatus($userId)
    {
        $status = 1;
        $query  = 'SELECT * FROM `tours` WHERE `status =:status` AND `user_id` = :userId ';
        $statement = $this->database->prepary($query);
        $statement->bindParam('status', $status);
        $statement->bindParam('userId', $userId);
        $statement->execute();
        $resp = $statement->fetchAll(\PDO::FETCH_ASSOC);
        if (empty($resp)) {
            throw new TourException('Note has been deleted', 400);
        }
        return $resp;
    }

    public function getTours()
    {
        $query = 'SELECT m.articlesid, m.articletitle, m.visible, m.articleimage, m.longitude,m.latitude,m.actual_location, m.parentid,t.user_id, t.date_created FROM mobile_articles m INNER JOIN tours t ON m.articlesid = t.article_id';
        $statement = $this->database->prepare($query);
        $statement->execute();

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }



    public function createTour($data)
    {
        $query = 'INSERT INTO tours (`article_id`,`user_id`, `status` ) VALUES (:article_id, :user_id, :status)';
        $status = 2;
        $statement = $this->database->prepare($query);
        $statement->bindParam(':article_id', $data->articlesId);
        $statement->bindParam(':user_id', $data->userId);
        $statement->bindParam(':status', $status);
        $statement->execute();

        return $this->checkAndGetTour($data->userId);
    }

    public function getTourLikes($articleId)
    {
        $query = 'SELECT `likes` FROM articles WHERE `articlesid` = :articleid';
        $statement = $this->database->prepare($query);
        $statement->bindParam(':articleid', $articleId);
        $statement->execute();

        $likes = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return (int) $likes['likes'];
    }

    public function updateTourLike($articleId, $likes)
    {
        $query = 'UPDATE articles SET `likes` = :likes  WHERE `article_id` = :article_id';
        $statement = $this->database->prepare($query);
        $statement->bindParam(':article_id', $articleId);

        $statement->bindParam(':likes', $likes);
        $statement->execute();
    }

    public function updateTourDislikes($articleId, $dislikes)
    {
        $query = 'UPDATE articles SET `dislikes` = :likes  WHERE `articlesid` = :article_id';
        $statement = $this->database->prepare($query);
        $statement->bindParam(':article_id', $articleId);

        $statement->bindParam(':likes', $dislikes);
        $statement->execute();
    }

    public function likeTour($articleId)
    {
        $likes = $this->getTourLikes($articleId) + 1;

        $this->updateTourLike($articleId, $likes);
        return "success";
    }

    public function deleteTour(int $articleId, string $userId)
    {
        try {
            $check = $this->checkTourByStatus($userId);
            if ($check) {
                $query = 'UPDATE tours SET `status` = :status WHERE `article_id` = :article_id AND `user_id` = :user_id ';
                $statement = $this->database->prepare($query);
                $status = 0;
                $statement->bindParam(':article_id', $articleId);
                $statement->bindParam(':user_id', $userId);
                $statement->bindParam(':status', $status);
                $statement->execute();
                return  "Deleted";
            }
            return "tour already deleted";
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }
}
