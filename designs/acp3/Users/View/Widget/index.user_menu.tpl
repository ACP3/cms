<nav class="navbar navbar-default navbar-fixed-top" id="nav-user-menu">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#nav-user-menu-collapse">
                <span class="sr-only">{lang t="system|toggle_navigation"}</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <span class="navbar-brand hidden-md hidden-lg">
                {lang t="users|user_menu"}
            </span>
        </div>
        <div id="nav-user-menu-collapse" class="collapse navbar-collapse">
            {if !empty($user_sidebar.modules) || !empty($user_sidebar.system)}
                <ul class="nav navbar-nav">
                    {if !empty($user_sidebar.modules)}
                        <li class="dropdown">
                            <a href="{uri args="acp/acp"}" id="menu-admin-label" class="dropdown-toggle" data-toggle="dropdown" data-target="#menu-administration">
                                {icon iconSet="solid" icon="file"}
                                {lang t="users|administration"}
                                <b class="caret"></b>
                            </a>
                            <ul class="dropdown-menu" role="menu" aria-labelledby="menu-admin-label">
                                {foreach $user_sidebar.modules as $translatedModuleName => $row}
                                    <li{if $row.is_active} class="active"{/if}>
                                        <a href="{uri args="acp/`$row.name`"}">{$translatedModuleName}</a>
                                    </li>
                                {/foreach}
                            </ul>
                        </li>
                    {/if}
                    {if !empty($user_sidebar.system)}
                        <li class="dropdown">
                            <a href="{uri args="acp/system"}" id="menu-system-label" class="dropdown-toggle" data-toggle="dropdown" data-target="#menu-system">
                                {icon iconSet="solid" icon="cog"}
                                {lang t="system|system"}
                                <b class="caret"></b>
                            </a>
                            <ul class="dropdown-menu" role="menu" aria-labelledby="menu-system-label">
                                {foreach $user_sidebar.system as $row}
                                    <li{if $row.is_active} class="active"{/if}><a href="{uri args="acp/`$row.path`"}">{$row.name}</a></li>
                                {/foreach}
                            </ul>
                        </li>
                    {/if}
                </ul>
            {/if}
            <ul class="nav navbar-nav navbar-right">
                <li>
                    <a href="{uri args="users/account"}">
                        {icon iconSet="solid" icon="home"}
                        {lang t="users|home"}
                    </a>
                </li>
                <li>
                    <a href="{uri args="users/index/logout"}">
                        {icon iconSet="solid" icon="power-off"}
                        {lang t="users|logout"}
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
