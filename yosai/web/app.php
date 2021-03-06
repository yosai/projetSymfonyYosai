<?php

use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;

// If you don't want to setup permissions the proper way, just uncomment the following PHP line
// read https://symfony.com/doc/current/setup.html#checking-symfony-application-configuration-and-setup
// for more information
//umask(0000);

// This check prevents access to debug front controllers that are deployed by accident to production servers.
// Feel free to remove this, extend it, or make something more sophisticated.
if (isset($_SERVER['HTTP_CLIENT_IP'])
    || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
    || !(in_array(@$_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'], true) || PHP_SAPI === 'cli-server')
) {

    require __DIR__.'/../vendor/autoload.php';
    if (PHP_VERSION_ID < 70000) {
        include_once __DIR__.'/../var/bootstrap.php.cache';
    }

    $kernel = new AppKernel('prod', false);
    if (PHP_VERSION_ID < 70000) {
        $kernel->loadClassCache();
    }
    //$kernel = new AppCache($kernel);

    // When using the HttpCache, you need to call the method in your front controller instead of relying on the configuration parameter
    //Request::enableHttpMethodParameterOverride();
    $request = Request::createFromGlobals();
    $response = $kernel->handle($request);
    $response->send();
    $kernel->terminate($request, $response);

} else {
    require __DIR__.'/../vendor/autoload.php';
    Debug::enable();

    $kernel = new AppKernel('dev', true);
    if (PHP_VERSION_ID < 70000) {
        $kernel->loadClassCache();
    }
    $request = Request::createFromGlobals();
    $response = $kernel->handle($request);
    $response->send();
    $kernel->terminate($request, $response);

}
