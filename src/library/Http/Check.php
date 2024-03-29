<?php

declare(strict_types=1);

namespace App\Ebcms\Theme\Http;

use App\Psrphp\Admin\Http\Common;
use App\Ebcms\Theme\Model\Server;
use App\Psrphp\Admin\Lib\Response;
use Composer\Autoload\ClassLoader;
use PsrPHP\Request\Request;
use ReflectionClass;
use Throwable;

class Check extends Common
{
    public function get(
        Server $server,
        Request $request
    ) {
        try {
            $root = dirname(dirname(dirname((new ReflectionClass(ClassLoader::class))->getFileName())));
            $name = $request->get('name');
            $param = [
                'name' => $name,
            ];
            $json_file = $root . '/theme/' . $name . '/config.json';
            if (file_exists($json_file)) {
                $json = json_decode(file_get_contents($json_file), true);
                $param['version'] = $json['version'];
            }
            $res = $server->query('/check', $param);
            if ($res['errcode']) {
                return Response::error($res['message'], $res['redirect_url'] ?? '', $res['errcode'], $res['data'] ?? null);
            }
            return Response::success($res['message'], $res['data'] ?? null);
        } catch (Throwable $th) {
            return Response::error($th->getMessage());
        }
    }
}
