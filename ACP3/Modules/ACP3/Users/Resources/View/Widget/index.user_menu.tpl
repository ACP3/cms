<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">{lang t="users|user_menu"}</h3>
    </div>
    <div class="list-group">
        <a href="{uri args="users/account"}" class="list-group-item">
            <i class="fas fa-home"></i>
            {lang t="users|home"}
        </a>
        {if !empty($user_sidebar.modules)}
            <div id="menu-administration" class="list-group-item dropdown">
                <a href="{uri args="acp/acp"}" id="menu-admin-label" class="dropdown-toggle" data-toggle="dropdown" data-target="#menu-administration">
                    <i class="fas fa-file"></i>
                    {lang t="users|administration"}
                    <b class="caret"></b>
                </a>
                <ul class="dropdown-menu" role="menu" aria-labelledby="menu-admin-label">
                    {foreach $user_sidebar.modules as $row}
                        <li{if $row.is_active} class="active"{/if}>
                            <a href="{uri args="acp/`$row.path`"}">{lang t="`$row.name`|`$row.name`"}</a>
                        </li>
                    {/foreach}
                </ul>
            </div>
        {/if}
        {if !empty($user_sidebar.system)}
            <div id="menu-system" class="list-group-item dropdown">
                <a href="{uri args="acp/system"}" id="menu-system-label" class="dropdown-toggle" data-toggle="dropdown" data-target="#menu-system">
                    <i class="fas fa-cog"></i>
                    {lang t="system|system"}
                    <b class="caret"></b>
                </a>
                <ul class="dropdown-menu" role="menu" aria-labelledby="menu-system-label">
                    {foreach $user_sidebar.system as $row}
                        <li{if $row.is_active} class="active"{/if}><a href="{uri args="acp/`$row.path`"}">{$row.name}</a></li>
                    {/foreach}
                </ul>
            </div>
        {/if}
        <a href="{uri args="users/index/logout"}" class="list-group-item">
            <i class="fas fa-power-off"></i>
            {lang t="users|logout"}
        </a>
    </div>
</div>
