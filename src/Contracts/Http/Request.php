<?php
/**
 * Created by PhpStorm.
 * User: heyanlong
 * Date: 2018/5/7
 * Time: 下午3:31
 */

namespace Trochilidae\Contracts\Http;


use Psr\Http\Message\ServerRequestInterface;

interface Request extends ServerRequestInterface
{
    public function get();
}