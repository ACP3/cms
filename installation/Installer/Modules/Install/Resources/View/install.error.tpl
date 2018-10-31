{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    <p>
        {lang t="install|installation_error"}
    </p>
    <div class="card bg-light">
        <div class="card-body p-2">
            <a href="{uri args="install"}" class="btn btn-light">
                <i class="fas fa-chevron-left"></i>
                {lang t="install|back"}
            </a>
        </div>
    </div>
{/block}
