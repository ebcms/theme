<?php

declare(strict_types=1);

namespace App\Ebcms\Theme\Http;

use App\Ebcms\Admin\Http\Common;
use App\Ebcms\Theme\Traits\DirTrait;
use DigPHP\Request\Request;
use Ebcms\Framework\Framework;

class Delete extends Common
{
    use DirTrait;

    public function post(
        Request $request
    ) {
        $name = $request->post('name');

        if (!preg_match('/^[a-zA-Z0-9]+$/u', $name)) {
            return $this->error('参数错误！');
        }

        $this->delDir(Framework::getRoot() . '/theme/' . $name);

        unlink(Framework::getRoot() . '/config/ebcms/theme/theme.php');

        return $this->success('操作成功！');
    }
}
