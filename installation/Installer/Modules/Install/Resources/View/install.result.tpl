{extends file="asset:`$LAYOUT`"}

{block CONTENT prepend}
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
{/block}
