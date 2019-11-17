<?php

declare(strict_types=1);

use App\Repository\UserRepository;
use App\Repository\TourRepository;

use Psr\Container\ContainerInterface;

$container = $app->getContainer();

$container['user_repository'] = function (ContainerInterface $container): UserRepository {
    return new UserRepository($container->get('db'));
};

$container['tour_repository'] = function (ContainerInterface $container): TourRepository {
    return new TourRepository($container->get('db'));
};
