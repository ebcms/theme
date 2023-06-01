<?php

use App\Psrphp\Admin\Model\Account;
use App\Ebcms\Theme\Http\Index;
use PsrPHP\Framework\Framework;
use PsrPHP\Router\Router;

return Framework::execute(function (
    Account $account,
    Router $router
): array {
    $menus = [];
    if ($account->checkAuth(Index::class)) {
        $menus[] = [
            'title' => '主题商店',
            'url' => $router->build('/ebcms/theme/index'),
        ];
    }
    return [
        'menus' => $menus,
    ];
});
