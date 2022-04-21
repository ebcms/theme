<?php

declare(strict_types=1);

namespace App\Ebcms\Theme\Http;

use App\Ebcms\Admin\Http\Common;
use DigPHP\Template\Template;
use Ebcms\Framework\Framework;

class Index extends Common
{
    public function get(
        Template $template
    ) {

        $themes = [];
        foreach (glob(Framework::getRoot() . '/theme/*/theme.json') as $file) {

            $name = substr($file, strlen(Framework::getRoot() . '/theme/'), -strlen('/theme.json'));
            $json = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

            $themes[] = [
                'name' => $name,
                'version' => $json['version'] ?? '',
                'title' => $json['title'] ?? '',
                'description' => $json['description'] ?? '',
                'thumb' => $json['thumb'] ?? '',
            ];
        }

        return $template->renderFromFile('index@ebcms/theme', [
            'themes' => $themes,
        ]);
    }
}
