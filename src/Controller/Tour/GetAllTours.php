<?php

declare(strict_types=1);

namespace App\Controller\Tour;

use Slim\Http\Request;
use Slim\Http\Response;

class GetAllTours extends BaseTour
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $this->setParams($request, $response, $args);
        $tasks = $this->getTourService()->getTours();

        return $this->jsonResponse('success', $tasks, 200);
    }
}
