<?php

namespace App\Controllers;


use Slim\Container;


/**
 * Class BaseController
 *
 * @package Auth\Controllers
 */

class BaseController
{

    protected $container;

    /**
     * BaseController constructor.
     *
     * @param Container $container
     */
    public function __construct($container)
    {
        $this->container = $container;

    }

    /**
     * Get container variables
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        if($this->container->get($name))
        {
            return $this->container->get($name);
        }
    }

}