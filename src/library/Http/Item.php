<?php

declare(strict_types=1);

namespace App\Ebcms\Theme\Http;

use App\Psrphp\Admin\Http\Common;
use App\Ebcms\Theme\Model\Server;
use App\Psrphp\Admin\Lib\Response;
use PsrPHP\Request\Request;
use PsrPHP\Template\Template;

class Item extends Common
{
    public function get(
        Request $request,
        Server $server,
        Template $template
    ) {
        $data = [];
        $res = $server->query('/detail', [
            'name' => $request->get('name'),
        ]);
        if ($res['errcode']) {
            return Response::error($res['message'], $res['redirect_url'] ?? '', $res['errcode']);
        }
        $data['theme'] = $res['data'];
        return $template->renderFromFile('item@ebcms/theme', $data);
    }
}
