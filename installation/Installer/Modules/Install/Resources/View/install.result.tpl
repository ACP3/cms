{extends file="asset:layout.tpl"}

{block CONTENT prepend}
    {if isset($install_error)}
        <p>
            {lang t="install|installation_error"}
        </p>
        <div class="well well-sm text-center">
            <a href="{uri args="install"}" class="btn btn-default">{lang t="install|back"}</a>
        </div>
    {else}
        <p>
            {lang t="install|installation_successful_1"}
        </p>
        <div class="alert alert-warning">
            {lang t="install|installation_successful_2"}
        </div>
        <div class="well well-sm text-center">
            <a href="{$ROOT_DIR}" class="btn btn-default">{lang t="install|go_to_website"}</a>
            <a href="{$ROOT_DIR}acp/" class="btn btn-default">{lang t="install|log_into_admin_panel"}</a>
        </div>
    {/if}
{/block}