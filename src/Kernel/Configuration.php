<?php

namespace App\Kernel;

use Symfony\Component\Yaml\Yaml;

class Configuration
{
    private $parameters = array();

    public function __construct()
    {
        $this->parameters = Yaml::parseFile(dirname(__DIR__) . '/../config/parameters.yml');
    }

    public function getParameter($key) {
        if (array_key_exists($key, $this->parameters['parameters'])) {
            return $this->parameters['parameters'][$key];
        }

        return null;
    }
}
