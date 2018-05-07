<?php

function app($name = '')
{
    if (is_string($name) && $name != '') {
        return \Trochilidae\Container\Container::getInstance()->get($name);
    }
    return \Trochilidae\Container\Container::getInstance();
}

function env($name = '')
{

}

function config($path = '')
{
    $path = explode('.', $path);

    $config = [];
    foreach ($path as $k => $item) {
        if ($k === 0) {
            $config = app('_config_' . $item);
        } else {
            $config = $config[$item];
        }
    }

    return $config;
}