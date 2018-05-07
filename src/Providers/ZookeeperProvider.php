<?php

namespace Trochilidae\Providers;

use Trochilidae\Support\ServiceProvider;

class ZookeeperProvider extends ServiceProvider
{

    public function register(): void
    {
        if (!class_exists('Zookeeper')) {
            exit("you need to install Zookeeper");
        }

        if (!file_exists(app('path.base') . DIRECTORY_SEPARATOR . '.zk')) {
            exit(".zk file not exists");
        }

        $zkHost = trim(file_get_contents(app('path.base') . DIRECTORY_SEPARATOR . '.zk'));

        $zkClient = new \Zookeeper($zkHost);

        $configArr = $this->load($zkClient);

        if (!empty($configArr)) {
            foreach ($configArr as $key => $value) {
                $this->setEnvironmentVariable($key, $value);
            }
        }
    }

    protected function setEnvironmentVariable($name, $value): void
    {
        // If PHP is running as an Apache module and an existing
        // Apache environment variable exists, overwrite it
        if (function_exists('apache_getenv') && function_exists('apache_setenv') && apache_getenv($name)) {
            apache_setenv($name, $value);
        }

        if (function_exists('putenv')) {
            putenv("$name=$value");
        }

        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }

    protected function load($zkClient)
    {
        $config = [];
        $path = config('app.zk');
        if ($zkClient->exists($path)) {
            $nodeChildren = $zkClient->getChildren($path);
            if (!empty($nodeChildren)) {
                foreach ($nodeChildren as $value) {
                    $config[$value] = $zkClient->get($path . '/' . $value);
                }
            }
        }

        return $config;
    }
}