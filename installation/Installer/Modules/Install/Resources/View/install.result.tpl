{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    <p>
        {lang t="install|installation_successful_1"}
    </p>
    <div class="alert alert-warning">
        {lang t="install|installation_successful_2"}
    </div>
    <div class="card bg-light">
        <div class="card-body p-2 text-center">
            <a href="{$ROOT_DIR}" class="btn btn-light">{lang t="install|go_to_website"}</a>
            <a href="{$ROOT_DIR}acp/" class="btn btn-light">{lang t="install|log_into_admin_panel"}</a>
        </div>
    </div>
{/block}
