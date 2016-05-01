{extends file="asset:`$LAYOUT`"}

{block CONTENT prepend}
    <p>
        {lang t="install|installation_error"}
    </p>
    <div class="well well-sm text-center">
        <a href="{uri args="install"}" class="btn btn-default">{lang t="install|back"}</a>
    </div>
{/block}
