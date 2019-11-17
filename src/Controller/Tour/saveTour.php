<?php

declare(strict_types=1);

namespace App\Controller\Tour;

use Slim\Http\Request;
use Slim\Http\Response;

class SaveTour extends BaseTour
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $this->setParams($request, $response, $args);
        $input = $this->getInput();
        $task = $this->getTourService()->createTour($input);

        return $this->jsonResponse('success', $task, 201);
    }
}
