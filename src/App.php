<?php

namespace Trochilidae;

use Trochilidae\Container\Container;

class App extends Container
{
    protected $basePath;

    public function __construct($basePath = null)
    {
        static::setInstance($this);
        $this['app'] = $this;

        if ($basePath) {
            $this->setBasePath($basePath);
        }
        $this->config();
        $this->provider();
    }

    public function run()
    {
        $this['server']->start();
    }

    public function setBasePath($basePath)
    {
        $this->basePath = rtrim($basePath, '\/');
        $this['path'] = $this->basePath . DIRECTORY_SEPARATOR . 'app';
        $this['path.config'] = $this->basePath . DIRECTORY_SEPARATOR . 'config';
        $this['path.base'] = $this->basePath;
        return $this;
    }

    private function config(): void
    {
        $dir = opendir($this['path.config']);

        while (($file = readdir($dir)) !== false) {
            if (strpos($file, 'php')) {
                $this['_config_' . str_replace('.php', '', $file)] = include $this['path.config'] . DIRECTORY_SEPARATOR . $file;
            }
        }

    }

    private function provider(): void
    {
        $list = config('provider');
        foreach ($list as $provider) {
            (new $provider)->register();
        }
    }
}