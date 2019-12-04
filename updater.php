<?php

declare(strict_types=1);

namespace netcup\DNS\API;

require_once __DIR__ . '/src/Client.php';
require_once __DIR__ . '/src/DynDNS.php';
require_once __DIR__ . '/src/Config.php';

if ('yes' === $_ENV['IPV4']) {
    $ipv4 = trim(file_get_contents('https://v4.ident.me'));
} else {
    $ipv4 = null;
}

if ('yes' === $_ENV['IPV6']) {
    $ipv6 = trim(file_get_contents('https://v6.ident.me'));
} else {
    $ipv6 = null;
}

if (!$ipv4 && !$ipv6) {
    throw new \UnexpectedValueException('ehm?');
}

$config = new Config(
    $_ENV['DOMAIN'],
    $_ENV['MODE'],
    (int)$_ENV['CUSTOMER_ID'],
    $_ENV['API_KEY'],
    $_ENV['API_PASSWORD'],
    (int)($_ENV['TTL'] ?? 0),
    'yes' === $_ENV['FORCE'],
);

(new DynDNS($config, $ipv4, $ipv6))->update();
