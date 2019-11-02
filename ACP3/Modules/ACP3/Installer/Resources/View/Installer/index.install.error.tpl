{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    <p>
        {lang t="installer|installation_error"}
    </p>
    <div class="well well-sm text-center">
        <a href="{uri args="install"}" class="btn btn-default">{lang t="installer|back"}</a>
    </div>
{/block}
