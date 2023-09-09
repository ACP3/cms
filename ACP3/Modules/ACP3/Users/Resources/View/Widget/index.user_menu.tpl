<div class="card mb-3">
    <div class="card-header">
        {lang t="users|user_menu"}
    </div>
    <ul class="list-group list-group-flush">
        <li>
            <a href="{uri args="users/account"}" class="list-group-item list-group-item-action border-top-0">
                {icon iconSet="solid" icon="house"}
                {lang t="users|home"}
            </a>
        </li>
        {if !empty($user_sidebar.modules)}
            <li id="menu-administration" class="dropdown">
                <a href="{uri args="acp/acp"}" id="menu-admin-label" class="list-group-item list-group-item-action border-top-0 dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                    {icon iconSet="solid" icon="file"}
                    {lang t="users|administration"}
                </a>
                <ul class="dropdown-menu" role="menu" aria-labelledby="menu-admin-label">
                    {foreach $user_sidebar.modules as $translatedModuleName => $row}
                        <li>
                            <a href="{uri args="acp/`$row.name`"}" class="dropdown-item">{$translatedModuleName}</a>
                        </li>
                    {/foreach}
                </ul>
            </li>
        {/if}
        {if !empty($user_sidebar.system)}
            <li id="menu-system" class="dropdown">
                <a href="{uri args="acp/system"}" id="menu-system-label" class="list-group-item list-group-item-action border-top-0 dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                    {icon iconSet="solid" icon="gear"}
                    {lang t="system|system"}
                </a>
                <ul class="dropdown-menu" role="menu" aria-labelledby="menu-system-label">
                    {foreach $user_sidebar.system as $row}
                        <li><a href="{uri args="acp/`$row.path`"}" class="dropdown-item">{$row.name}</a></li>
                    {/foreach}
                </ul>
            </li>
        {/if}
        <li>
            <a href="{uri args="users/index/logout"}" class="list-group-item list-group-item-action border-0">
                {icon iconSet="solid" icon="power-off"}
                {lang t="users|logout"}
            </a>
        </li>
    </ul>
</div>
