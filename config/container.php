<?php

use App\EntityManagerFactory;
use App\repositories\redis\LipsumRepository;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

$logLevel = $_ENV['APP_LOG_LEVEL'] ?? Logger::ERROR;

$definitions = [];

$definitions = [
    StreamHandler::class => DI\create()->constructor(__DIR__ . '/../var/log/app.log', $logLevel),
    Logger::class => DI\create()->constructor('app.log', [DI\get(StreamHandler::class)]),
];

if (isset($_ENV['DB_URL'])) {
    $definitions[EntityManagerInterface::class] = DI\factory([EntityManagerFactory::class, 'create']);
}

if (isset($_ENV['REDIS_HOST']) && isset($_ENV['REDIS_PORT'])) {

//    $definitions[Redis::class] = function () {
//        $redis = new Redis();
//        $redisHost = $_ENV['REDIS_HOST'] ?? '127.0.0.1';
//        $redisPort = $_ENV['REDIS_PORT'] ?? 6379;
//        $redis->connect($redisHost, $redisPort);
//        return $redis;
//    };

    $definitions[Redis::class] = DI\create()
        ->method('connect',
            $_ENV['REDIS_HOST'],
            $_ENV['REDIS_PORT'],
            0.0, null,
            0,
            0.0,
            []
        );

    $definitions[LipsumRepository::class] = DI\autowire();
}

return $definitions;