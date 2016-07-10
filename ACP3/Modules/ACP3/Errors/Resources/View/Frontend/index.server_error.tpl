{extends file="asset:$LAYOUT"}

{block CONTENT}
    <div class="alert alert-warning text-center">
        {lang t="errors|server_unavailable"}
    </div>
    <p class="text-center">
        <a href="{uri args=""}" class="btn btn-primary btn-lg">{lang t="errors|retry"}</a>
    </p>
{/block}
