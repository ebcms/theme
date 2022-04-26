<?php

declare(strict_types=1);

namespace App\Ebcms\Theme;

use DiggPHP\Psr11\Container;
use DiggPHP\Template\Template;
use DiggPHP\Framework\AppInterface;
use DiggPHP\Framework\Config;
use DiggPHP\Framework\Framework;

class App implements AppInterface
{
    public static function onInit(
        Container $container,
        Config $config
    ) {
        $theme = $config->get('theme.name@ebcms.theme', 'default');
        $container->onInstance(Template::class, function (Template $template) use ($theme) {
            foreach (Framework::getAppList() as $app) {
                $template->addPath($app, Framework::getRoot() . '/theme/' . $theme . '/' . $app, 99);
            }
        });
    }
}
