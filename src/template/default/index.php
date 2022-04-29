{include common/header@ebcms/admin}
<script>
    function change(name) {
        $.ajax({
            type: "POST",
            url: "{echo $router->build('/ebcms/theme/change')}",
            data: {
                name: name
            },
            dataType: "JSON",
            success: function(response) {
                if (response.code == 0) {
                    location.reload();
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert('发生错误~');
            }
        });
    }

    function del(name) {
        if (confirm('确定删除该主题吗？删除后无法恢复！')) {
            $.ajax({
                type: "POST",
                url: "{echo $router->build('/ebcms/theme/delete')}",
                data: {
                    name: name
                },
                dataType: "JSON",
                success: function(response) {
                    if (response.code == 0) {
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('发生错误~');
                }
            });
        }
    }
</script>
<div class="container">
    <div class="my-4">
        <div class="h1">主题管理</div>
        <div class="text-muted fw-light">
            <span>主题位于 <code>/theme</code> 目录</span>
            {if in_array('ebcms/tstore', $framework->getAppList())}
            <span>，您可以通过<a href="{echo $router->build('/ebcms/tstore/index')}" class="mx-1 fw-bold">主题市场</a>在线安装主题~</span>
            {/if}
        </div>
    </div>
    <div class="d-flex flex-column gap-4">
        {foreach $themes as $theme}
        <div class="d-flex gap-3">
            <div>
                <img src="{echo $theme['thumb']}" class="img-thumbnail" width="130" alt="">
            </div>
            <div class="d-flex flex-column gap-2 flex-grow-1 bg-light p-3">
                <div><span class="fs-6 fw-bold">{$theme['title']?:'-'}</span><sup class="ms-1 text-secondary">{$theme['version']??''}</sup></div>
                <div>{$theme.description}</div>
                <div><code>/theme/{$theme.name}</code> </div>
                <div class="d-flex gap-2">
                    {if $config->get('theme.name@ebcms.theme', 'default') == $theme['name']}
                    <button class="btn btn-sm btn-danger" type="button" onclick="change('{$theme.name}');" data-bs-toggle="tooltip" data-bs-placement="right" title="点击停用该主题">使用中</button>
                    {else}
                    <button class="btn btn-sm btn-primary" type="button" onclick="change('{$theme.name}');" data-bs-toggle="tooltip" data-bs-placement="right" title="点击使用该主题">使用该主题</button>
                    {/if}
                    <!-- <button class="btn btn-sm btn-warning" type="button" onclick="del('{$theme.name}');" data-bs-toggle="tooltip" data-bs-placement="right" title="删除该主题">删除</button> -->
                </div>
            </div>
        </div>
        {/foreach}
    </div>
</div>
{include common/footer@ebcms/admin}