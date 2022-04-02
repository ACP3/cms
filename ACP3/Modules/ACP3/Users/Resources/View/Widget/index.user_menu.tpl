<div class="card mb-3">
    <div class="card-header">
        {lang t="users|user_menu"}
    </div>
    <ul class="list-group list-group-flush">
        <li class="list-group-item">
            <a href="{uri args="users/account"}">
                {icon iconSet="solid" icon="house"}
                {lang t="users|home"}
            </a>
        </li>
        {if !empty($user_sidebar.modules)}
            <li id="menu-administration" class="list-group-item dropdown">
                <a href="{uri args="acp/acp"}" id="menu-admin-label" class="dropdown-toggle" data-bs-toggle="dropdown">
                    {icon iconSet="solid" icon="file"}
                    {lang t="users|administration"}
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
            <li id="menu-system" class="list-group-item dropdown">
                <a href="{uri args="acp/system"}" id="menu-system-label" class="dropdown-toggle" data-bs-toggle="dropdown">
                    {icon iconSet="solid" icon="gear"}
                    {lang t="system|system"}
                </a>
                <ul class="dropdown-menu" role="menu" aria-labelledby="menu-system-label">
                    {foreach $user_sidebar.system as $row}
                        <li{if $row.is_active} class="active"{/if}><a href="{uri args="acp/`$row.path`"}">{$row.name}</a></li>
                    {/foreach}
                </ul>
            </li>
        {/if}
        <li class="list-group-item">
            <a href="{uri args="users/index/logout"}">
                {icon iconSet="solid" icon="power-off"}
                {lang t="users|logout"}
            </a>
        </li>
    </ul>
</div>
