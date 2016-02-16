{extends file="asset:Errors/Frontend/layout.tpl"}

{block CONTENT}
    <div class="alert alert-warning text-center">
        {lang t="errors|server_unavailable"}
    </div>
    <p class="text-center">
        <a href="{$ROOT_DIR}" class="btn btn-primary btn-lg">{lang t="errors|retry"}</a>
    </p>
{/block}