<?php

declare(strict_types=1);

namespace App\Ebcms\Theme\Http;

use App\Psrphp\Admin\Http\Common;
use App\Psrphp\Admin\Lib\Dir;
use App\Psrphp\Admin\Lib\Response;
use PsrPHP\Session\Session;
use Exception;
use Throwable;
use ZipArchive;

class Cover extends Common
{
    public function get(
        Session $session
    ) {
        try {
            $item = $session->get('item');
            Dir::del($item['item_path']);
            $this->unZip($item['tmpfile'], $item['item_path']);
            return Response::success('文件更新成功!');
        } catch (Throwable $th) {
            return Response::error($th->getMessage());
        }
    }

    private function unZip($file, $destination)
    {
        $zip = new ZipArchive();
        if ($zip->open($file) !== true) {
            throw new Exception('Could not open archive');
        }
        if (true !== $zip->extractTo($destination)) {
            throw new Exception('Could not extractTo ' . $destination);
        }
        if (true !== $zip->close()) {
            throw new Exception('Could not close archive ' . $file);
        }
    }
}
