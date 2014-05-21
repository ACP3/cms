<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">{lang t="users|user_menu"}</h3>
    </div>
    <div class="list-group">
        <a href="{uri args="users/index/home"}" class="list-group-item">
            <i class="glyphicon glyphicon-home"></i>
            {lang t="users|home"}
        </a>
        {if isset($user_sidebar.modules)}
            <div class="list-group-item dropdown">
                <a href="{uri args="acp"}" id="menu-administration" class="dropdown-toggle" data-toggle="dropdown" data-target="#">
                    <i class="glyphicon glyphicon-file"></i>
                    {lang t="users|administration"}
                    <b class="caret"></b>
                </a>
                <ul class="dropdown-menu" role="menu" aria-labelledby="menu-administration">
                    {foreach $user_sidebar.modules as $row}
                        <li{$row.active}><a href="{uri args="acp/`$row.dir`"}">{$row.name}</a></li>
                    {/foreach}
                </ul>
            </div>
        {/if}
        {if isset($user_sidebar.system)}
            <div class="list-group-item dropdown">
                <a href="{uri args="acp/system"}" id="menu-system" class="dropdown-toggle" data-toggle="dropdown" data-target="#">
                    <i class="glyphicon glyphicon-wrench"></i>
                    {lang t="system|system"}
                    <b class="caret"></b>
                </a>
                <ul class="dropdown-menu" role="menu" aria-labelledby="menu-system">
                    {foreach $user_sidebar.system as $row}
                        <li{$row.active}><a href="{uri args="acp/system/index/`$row.page`"}">{$row.name}</a></li>
                    {/foreach}
                </ul>
            </div>
        {/if}
        <a href="{uri args="users/index/logout/last_`$user_sidebar.page`"}" class="list-group-item">
            <i class="glyphicon glyphicon-off"></i>
            {lang t="users|logout"}
        </a>
    </div>
</div>