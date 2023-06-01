<?php

declare(strict_types=1);

namespace App\Ebcms\Theme\Http;

use App\Ebcms\Admin\Traits\ResponseTrait;
use App\Ebcms\Admin\Traits\RestfulTrait;
use PsrPHP\Psr16\LocalAdapter;
use PsrPHP\Request\Request;

class Api
{
    use RestfulTrait;
    use ResponseTrait;

    public function post(
        Request $request,
        LocalAdapter $cache
    ) {
        if (!$token = $request->get('token')) {
            return $this->error('token校验失败！');
        }
        if ($token != $cache->get('themeapitoken')) {
            return $this->error('token校验失败！');
        }
        $cache->set($token, $request->post(), 10);
        return $this->success('success');
    }
}
