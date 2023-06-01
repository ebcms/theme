<?php

declare(strict_types=1);

namespace App\Ebcms\Theme\Http;

use App\Psrphp\Admin\Http\Common;
use App\Ebcms\Theme\Model\Server;
use App\Psrphp\Admin\Lib\Response;
use Composer\Autoload\ClassLoader;
use PsrPHP\Psr16\LocalAdapter;
use PsrPHP\Request\Request;
use PsrPHP\Router\Router;
use PsrPHP\Session\Session;
use ReflectionClass;
use Throwable;

class Source extends Common
{
    public function get(
        Request $request,
        Server $server,
        Router $router,
        LocalAdapter $cache,
        Session $session
    ) {
        try {
            $token = 'theme_' . md5(uniqid() . rand(10000000, 99999999));
            $cache->set('themeapitoken', $token, 30);
            $name = $request->get('name');
            $param = [
                'api' => $router->build('/ebcms/theme/api', [
                    'token' => $token
                ]),
                'name' => $name,
            ];
            $root = dirname(dirname(dirname((new ReflectionClass(ClassLoader::class))->getFileName())));
            $json_file = $root . '/theme/' . $name . '/config.json';
            if (file_exists($json_file)) {
                $json = json_decode(file_get_contents($json_file), true);
                $param['version'] = $json['version'];
            }

            $res = $server->query('/source', $param);
            if ($res['errcode']) {
                return Response::error($res['message'], $res['redirect_url'] ?? '', $res['errcode']);
            }
            if (null === $item = $cache->get($token)) {
                return Response::error('超时，请重新操作~');
            }
            $item['item_path'] = $root . '/theme/' . $name;
            $session->set('item', $item);
            return Response::success($res['message']);
        } catch (Throwable $th) {
            return Response::error($th->getMessage());
        }
    }
}
