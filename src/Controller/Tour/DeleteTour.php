<?php

declare(strict_types=1);

namespace App\Controller\Tour;

use Slim\Http\Request;
use Slim\Http\Response;

class DeleteTour extends BaseTour
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $this->setParams($request, $response, $args);
        $input = $this->getInput();
        $userId =  $this->args['id'];
        $task = $this->getTourService()->deleteTour($input, $userId);
        // $task = array('deleted', 'succesfully');

        return $this->jsonResponse('success', $task, 204);
    }
}
