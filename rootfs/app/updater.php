<?php

declare(strict_types=1);

namespace netcup\DNS\API;

require_once __DIR__ . '/src/Client.php';
require_once __DIR__ . '/src/DynDNS.php';
require_once __DIR__ . '/src/Config.php';

/**
 * Reads an environment variable, falling back to the content of the file
 * referenced by <VAR>_FILE when the variable itself is not set (Docker secrets).
 */
function resolve_env(string $name, string $default = ''): string
{
    if (isset($_ENV[$name])) {
        return $_ENV[$name];
    }
    $fileVar = $name . '_FILE';
    if (isset($_ENV[$fileVar])) {
        $path = $_ENV[$fileVar];
        if (is_readable($path)) {
            return trim(file_get_contents($path));
        }
    }
    return $default;
}

/**
 * Validates that a URL uses http or https scheme and returns it, or throws on invalid input.
 */
function validate_url(string $url, string $varName): string
{
    $scheme = parse_url($url, PHP_URL_SCHEME);
    if ($scheme !== 'http' && $scheme !== 'https') {
        throw new \InvalidArgumentException("$varName must use http or https scheme.");
    }
    return $url;
}

if ('yes' === $_ENV['IPV4']) {
    $ipv4Url = validate_url($_ENV['IPV4_URL'] ?? 'http://v4.ident.me', 'IPV4_URL');
    $ipv4 = trim(file_get_contents($ipv4Url));
} else {
    $ipv4 = null;
}

if ('yes' === $_ENV['IPV6']) {
    $ipv6Url = validate_url($_ENV['IPV6_URL'] ?? 'http://v6.ident.me', 'IPV6_URL');
    $ipv6 = trim(file_get_contents($ipv6Url));
} else {
    $ipv6 = null;
}

if (!$ipv4 && !$ipv6) {
    throw new \UnexpectedValueException('ehm?');
}

$config = new Config(
    $_ENV['DOMAIN'],
    $_ENV['MODE'],
    (int)resolve_env('CUSTOMER_ID'),
    resolve_env('API_KEY'),
    resolve_env('API_PASSWORD'),
    (int)($_ENV['TTL'] ?? 0),
    'yes' === $_ENV['FORCE'],
);

(new DynDNS($config, $ipv4, $ipv6))->update();
