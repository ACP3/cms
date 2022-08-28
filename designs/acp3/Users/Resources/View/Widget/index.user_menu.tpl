<nav id="nav-user-menu" class="navbar navbar-expand-lg sticky-top navbar-light bg-light">
    <div class="container">
        <span class="navbar-brand d-lg-none">{lang t="users|user_menu"}</span>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav-user-menu-content" aria-controls="nav-user-menu-content" aria-expanded="false" aria-label="{lang t="system|toggle_navigation"}">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="nav-user-menu-content">
            {if !empty($user_sidebar.modules) || !empty($user_sidebar.system)}
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    {if !empty($user_sidebar.modules)}
                        <li class="nav-item dropdown">
                            <a href="{uri args="acp/acp"}" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" id="menu-administration" aria-expanded="false">
                                {icon iconSet="solid" icon="file"}
                                {lang t="users|administration"}
                            </a>
                            <ul class="dropdown-menu" role="menu" aria-labelledby="menu-administration">
                                {foreach $user_sidebar.modules as $translatedModuleName => $row}
                                    <li>
                                        <a href="{uri args="acp/`$row.name`"}" class="dropdown-item{if $row.is_active} active{/if}">{$translatedModuleName}</a>
                                    </li>
                                {/foreach}
                            </ul>
                        </li>
                    {/if}
                    {if !empty($user_sidebar.system)}
                        <li class="nav-item dropdown">
                            <a href="{uri args="acp/system"}" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" id="menu-system" aria-expanded="false">
                                {icon iconSet="solid" icon="gear"}
                                {lang t="system|system"}
                            </a>
                            <ul class="dropdown-menu" role="menu" aria-labelledby="menu-system">
                                {foreach $user_sidebar.system as $row}
                                    <li><a href="{uri args="acp/`$row.path`"}" class="dropdown-item{if $row.is_active} active{/if}">{$row.name}</a></li>
                                {/foreach}
                            </ul>
                        </li>
                    {/if}
                </ul>
            {/if}
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a href="{uri args="users/account"}" class="nav-link">
                        {icon iconSet="solid" icon="house"}
                        {lang t="users|home"}
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{uri args="users/index/logout"}" class="nav-link">
                        {icon iconSet="solid" icon="power-off"}
                        {lang t="users|logout"}
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
