<?php

$publicPath = realpath(__DIR__.'/../../public');
$requestPath = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/');
$assetPath = $publicPath.DIRECTORY_SEPARATOR.ltrim(str_replace('/', DIRECTORY_SEPARATOR, $requestPath), DIRECTORY_SEPARATOR);

if ($requestPath !== '/' && is_file($assetPath)) {
    return false;
}

require $publicPath.DIRECTORY_SEPARATOR.'index.php';
