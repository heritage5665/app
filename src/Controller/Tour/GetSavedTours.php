<?php

declare(strict_types=1);

namespace App\Controller\Tour;

use Slim\Http\Request;
use Slim\Http\Response;

class GetSavedTours extends BaseTour
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $this->setParams($request, $response, $args);

        $userId = $args['id'];
        $task =  $this->getTourService()->getTour($userId);

        return $this->jsonResponse('success', $task, 200);
    }
}
