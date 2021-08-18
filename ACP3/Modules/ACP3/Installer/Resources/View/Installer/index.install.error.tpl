{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    <p>
        {lang t="installer|installation_error"}
    </p>
    <div class="card bg-light mb-3">
        <div class="card-body text-center">
            <a href="{uri args="installer"}" class="btn btn-outline-primary">{lang t="installer|back"}</a>
        </div>
    </div>
{/block}
