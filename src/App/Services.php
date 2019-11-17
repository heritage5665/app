<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use App\Service\UserService;
use App\Service\TourService;

$container = $app->getContainer();

$container['user_service'] = function (ContainerInterface $container): UserService {
    return new UserService($container->get('user_repository'), $container->get('redis_service'));
};

$container['tour_service'] = function (ContainerInterface $container): TourService {
    return new TourService($container->get('tour_repository'), $container->get('redis_service'));
};
