<?php

declare(strict_types=1);

namespace App\Ebcms\Theme\Http;

use App\Ebcms\Admin\Http\Common;
use DigPHP\Request\Request;
use Ebcms\Framework\Config;
use Ebcms\Framework\Framework;

class Change extends Common
{

    public function post(
        Request $request,
        Config $config
    ) {
        $name = $request->post('name');

        if (!preg_match('/^[a-zA-Z0-9]+$/u', $name)) {
            return $this->error('参数错误！');
        }

        $theme = $config->get('theme@ebcms.theme', []);
        $theme['name'] = $theme['name'] ?? '';
        $theme['name'] = $theme['name'] == $name ? '' : $name;

        if (!is_dir(Framework::getRoot() . '/config/ebcms/theme/')) {
            mkdir(Framework::getRoot() . '/config/ebcms/theme/', 0755, true);
        }

        file_put_contents(Framework::getRoot() . '/config/ebcms/theme/theme.php', '<?php return ' . var_export($theme, true) . ';');

        return $this->success('操作成功！');
    }
}
