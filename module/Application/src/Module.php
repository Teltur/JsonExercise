<?php

namespace Application;

use Zend\Mvc\MvcEvent;

class Module
{
    const VERSION = '0.1';

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     *
     * @param \Zend\Mvc\MvcEvent $e
     */
    public function onBootstrap(MvcEvent $e)
    {
        // any origin is allowed
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day
        }

    }
}
