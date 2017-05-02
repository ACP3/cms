<nav class="navbar navbar-default navbar-fixed-top" id="nav-user-menu">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#nav-user-menu-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <span class="navbar-brand hidden-md hidden-lg">
                {lang t="users|user_menu"}
            </span>
        </div>
        <div id="nav-user-menu-collapse" class="collapse navbar-collapse">
            {if isset($modules) || isset($system)}
                <ul class="nav navbar-nav">
                    {if isset($modules)}
                        <li class="dropdown">
                            <a href="{uri args="acp/acp"}" id="menu-admin-label" class="dropdown-toggle" data-toggle="dropdown" data-target="#menu-administration">
                                <i class="glyphicon glyphicon-file"></i>
                                {lang t="users|administration"}
                                <b class="caret"></b>
                            </a>
                            <ul class="dropdown-menu" role="menu" aria-labelledby="menu-admin-label">
                                {foreach $modules as $row}
                                    <li><a href="{uri args="acp/`$row.path`"}">{$row.name}</a></li>
                                {/foreach}
                            </ul>
                        </li>
                    {/if}
                    {if isset($system)}
                        <li class="dropdown">
                            <a href="{uri args="acp/system"}" id="menu-system-label" class="dropdown-toggle" data-toggle="dropdown" data-target="#menu-system">
                                <i class="glyphicon glyphicon-cog"></i>
                                {lang t="system|system"}
                                <b class="caret"></b>
                            </a>
                            <ul class="dropdown-menu" role="menu" aria-labelledby="menu-system-label">
                                {foreach $system as $row}
                                    <li><a href="{uri args="acp/`$row.path`"}">{$row.name}</a></li>
                                {/foreach}
                            </ul>
                        </li>
                    {/if}
                </ul>
            {/if}
            <ul class="nav navbar-nav navbar-right">
                <li>
                    <a href="{uri args="users/account"}">
                        <i class="glyphicon glyphicon-home"></i>
                        {lang t="users|home"}
                    </a>
                </li>
                <li>
                    <a href="{uri args="users/index/logout"}">
                        <i class="glyphicon glyphicon-off"></i>
                        {lang t="users|logout"}
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
