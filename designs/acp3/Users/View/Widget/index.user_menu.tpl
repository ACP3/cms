<nav class="navbar sticky-top navbar-expand-lg navbar-light bg-light shadow-sm" id="nav-user-menu">
    <div class="container">
        <a class="navbar-brand d-md-none" href="#">{lang t="users|user_menu"}</a>
        <button class="navbar-toggler"
                type="button"
                data-toggle="collapse"
                data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent"
                aria-expanded="false"
                aria-label="{lang t="system|toggle_navigation"}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            {if !empty($user_sidebar.modules) || !empty($user_sidebar.system)}
                <ul class="navbar-nav">
                    {if !empty($user_sidebar.modules)}
                        <li class="nav-item dropdown">
                            <a href="{uri args="acp/acp"}"
                               id="menu-admin-label"
                               class="nav-link dropdown-toggle"
                               data-toggle="dropdown"
                               data-target="#menu-administration">
                                <i class="fas fa-file"></i>
                                {lang t="users|administration"}
                            </a>
                            <div class="dropdown-menu"
                                role="menu"
                                aria-labelledby="menu-admin-label">
                                {foreach $user_sidebar.modules as $row}
                                    <a href="{uri args="acp/`$row.path`"}" class="dropdown-item">{lang t="`$row.name`|`$row.name`"}</a>
                                {/foreach}
                            </div>
                        </li>
                    {/if}
                    {if !empty($user_sidebar.system)}
                        <li class="nav-item dropdown">
                            <a href="{uri args="acp/system"}"
                               id="menu-system-label"
                               class="nav-link dropdown-toggle"
                               data-toggle="dropdown"
                               data-target="#menu-system">
                                <i class="fas fa-cog"></i>
                                {lang t="system|system"}
                            </a>
                            <div class="dropdown-menu"
                                role="menu"
                                aria-labelledby="menu-system-label">
                                {foreach $user_sidebar.system as $row}
                                    <a href="{uri args="acp/`$row.path`"}" class="dropdown-item">{$row.name}</a>
                                {/foreach}
                            </div>
                        </li>
                    {/if}
                </ul>
            {/if}
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a href="{uri args="users/account"}"
                       class="nav-link">
                        <i class="fas fa-home"></i>
                        {lang t="users|home"}
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{uri args="users/index/logout"}"
                       class="nav-link">
                        <i class="fas fa-power-off"></i>
                        {lang t="users|logout"}
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
