{include common/header@psrphp/admin}
<div class="container">
    <div class="h1 my-4">主题商店</div>
    <div class="my-4 d-flex flex-column gap-4">
        <div class="d-flex gap-3">
            <div>
                <img src="{echo $theme['thumb']}" class="img-thumbnail" width="100" alt="">
            </div>
            <div class="d-flex flex-column gap-2 flex-grow-1 bg-light p-3">
                <div><span class="fs-6 fw-bold">{$theme['title']?:'-'}</span><sup class="ms-1 text-secondary">{$theme['version']??''}</sup></div>
                <div><code>{$theme.name}</code> </div>
                <div>
                    <a href="{$theme['url']??''}" target="_blank">详细介绍&gt;</a>
                </div>
            </div>
        </div>
        <div>
            <button class="btn btn-primary" onclick="EBCMS.handler();" id="handler">一键下载</button>
            <button class="btn btn-secondary ms-2" onclick="$('.console').html('')">清理日志</button>
        </div>
        <div class="console p-3 text-white bg-dark overflow-auto" style="height: 300px;"></div>
    </div>
</div>
<script>
    var EBCMS = {};
    $(function() {
        EBCMS.state = 0;
        EBCMS.stop = function(message) {
            EBCMS.console(message, 'red');
            EBCMS.console("完毕<hr>");
            EBCMS.state = 0;
            $("#handler").removeClass('btn-warning').addClass('btn-primary').html('一键下载');
        };
        EBCMS.handler = function() {
            if (EBCMS.state) {
                EBCMS.state = 0;
                EBCMS.console("正在终止...", 'red');
            } else {
                if (confirm('此操作可能发生意外风险，请先手动备份系统，继续吗？')) {
                    EBCMS.state = 1;
                    $("#handler").removeClass('btn-primary').addClass('btn-warning').html('一键终止');
                    EBCMS.check();
                }
            }
        }
        EBCMS.check = function() {
            EBCMS.console("版本检测...");
            $.ajax({
                type: "GET",
                url: "{echo $router->build('/ebcms/theme/check')}",
                data: {
                    name: "{$theme.name}",
                },
                dataType: "json",
                success: function(response) {
                    if (response.errcode) {
                        EBCMS.stop(response.message);
                    } else {
                        if (!EBCMS.state) {
                            EBCMS.stop('已终止(检测完毕)');
                            return;
                        }
                        EBCMS.console(response.message);
                        EBCMS.source();
                    }
                },
                error: function(context) {
                    EBCMS.stop("发生错误：" + context.statusText);
                }
            });
        };
        EBCMS.source = function() {
            EBCMS.console("获取资源信息...");
            $.ajax({
                type: "GET",
                url: "{echo $router->build('/ebcms/theme/source')}",
                data: {
                    name: "{$theme.name}",
                },
                dataType: "json",
                success: function(response) {
                    if (response.errcode) {
                        EBCMS.stop(response.message);
                    } else {
                        if (!EBCMS.state) {
                            EBCMS.stop('已终止(资源信息获取完毕)');
                            return;
                        }
                        EBCMS.console(response.message);
                        EBCMS.download();
                    }
                },
                error: function(context) {
                    EBCMS.stop("发生错误：" + context.statusText);
                }
            });
        };
        EBCMS.download = function() {
            EBCMS.console("开始下载~");
            $.ajax({
                type: "GET",
                url: "{echo $router->build('/ebcms/theme/download')}",
                dataType: "json",
                success: function(response) {
                    if (response.errcode) {
                        EBCMS.stop(response.message);
                    } else {
                        if (!EBCMS.state) {
                            EBCMS.stop('已终止(下载完毕)');
                            return;
                        }
                        EBCMS.console(response.message);
                        EBCMS.backup();
                    }
                },
                error: function(context) {
                    EBCMS.stop("发生错误：" + context.statusText);
                }
            });
        };
        EBCMS.backup = function() {
            EBCMS.console("主题备份中...");
            $.ajax({
                type: "GET",
                url: "{echo $router->build('/ebcms/theme/backup')}",
                dataType: "json",
                success: function(response) {
                    if (response.errcode) {
                        EBCMS.stop(response.message);
                    } else {
                        if (!EBCMS.state) {
                            EBCMS.stop('已终止(主题备份完成)');
                            return;
                        }
                        EBCMS.console(response.message);
                        EBCMS.cover();
                    }
                },
                error: function(context) {
                    EBCMS.stop("发生错误：" + context.statusText);
                }
            });
        };
        EBCMS.cover = function() {
            EBCMS.console("主题装载中...");
            $.ajax({
                type: "GET",
                url: "{echo $router->build('/ebcms/theme/cover')}",
                dataType: "json",
                success: function(response) {
                    if (response.errcode) {
                        EBCMS.rollback(response.message);
                    } else {
                        EBCMS.console("主题装载完毕，请到主题管理中启用该主题~");
                        if (!EBCMS.state) {
                            return;
                        }
                        EBCMS.console(response.message + "<hr>继续检测...<hr>");
                        EBCMS.check();
                    }
                },
                error: function(context) {
                    EBCMS.rollback("发生错误：" + context.statusText);
                }
            });
        };
        EBCMS.rollback = function(msg) {
            EBCMS.console(msg, 'red');
            EBCMS.console("还原中...");
            $.ajax({
                type: "GET",
                url: "{echo $router->build('/ebcms/theme/rollback')}",
                dataType: "json",
                success: function(response) {
                    if (response.errcode) {
                        if (confirm('还原失败，继续尝试还原吗？')) {
                            EBCMS.rollback(response.message);
                        } else {
                            EBCMS.stop('还原终止，请手动处理！');
                        }
                    } else {
                        EBCMS.stop(response.message);
                    }
                },
                error: function(context) {
                    if (confirm('还原失败，继续尝试还原吗？')) {
                        EBCMS.rollback("发生错误：" + context.statusText);
                    } else {
                        EBCMS.stop('还原终止，请手动处理！');
                    }
                }
            });
        };
        EBCMS.console = function(message, color) {
            $(".console").append("<div class=\"small\" style=\"color:" + (color ? color : 'white') + "\">[" + (new Date()).toLocaleString() + "] " + message + "</div>");
            $(".console").scrollTop(99999999);
        }
    });
</script>
{include common/footer@psrphp/admin}