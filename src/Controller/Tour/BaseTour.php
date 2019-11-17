<?php

declare(strict_types=1);

namespace App\Controller\Tour;

use App\Controller\BaseController;
use App\Service\TourService;
use Slim\Container;

abstract class BaseTour extends BaseController
{
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    protected function getTourService(): TourService
    {
        return $this->container->get('tour_service');
    }
}
