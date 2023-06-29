<?php

declare(strict_types=1);

namespace App\Ebcms\Theme\Http;

use App\Psrphp\Admin\Http\Common;
use App\Psrphp\Admin\Lib\Response;
use App\Psrphp\Admin\Lib\Zip;
use Composer\Autoload\ClassLoader;
use PsrPHP\Session\Session;
use ReflectionClass;
use Throwable;
use ZipArchive;

class Backup extends Common
{

    public function get(
        Session $session,
        Zip $zip
    ) {
        try {
            $root = dirname(dirname(dirname((new ReflectionClass(ClassLoader::class))->getFileName())));
            $item = $session->get('item');
            $item['backup_file'] = $root . '/backup/theme_' . $item['name'] . '_' . date('YmdHis') . '.zip';

            $zip->open($item['backup_file'], ZipArchive::CREATE);
            if (is_dir($item['item_path'])) {
                $zip->addDirectory($item['item_path'], $item['item_path'] . '/');
            }
            $zip->close();

            $session->set('item', $item);
            return Response::success('备份成功！', $item);
        } catch (Throwable $th) {
            return Response::error($th->getMessage());
        }
    }
}
