{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {redirect_message}
    <p>
        {lang t="system|delete_caches_description"}
    </p>
    <div class="well well-sm">
        <div class="row">
            <div class="col-sm-9 cache-type">
                {lang t="system|cache_type_general"}
            </div>
            <div class="col-sm-3">
                <a href="{uri args="acp/system/maintenance/cache/action_general"}" class="btn btn-block btn-warning">
                    <i class="glyphicon glyphicon-refresh"></i>
                    {lang t="system|refresh"}
                </a>
            </div>
        </div>
    </div>
    <div class="well well-sm">
        <div class="row">
            <div class="col-sm-9 cache-type">
                {lang t="system|cache_type_templates"}
            </div>
            <div class="col-sm-3">
                <a href="{uri args="acp/system/maintenance/cache/action_templates"}" class="btn btn-block btn-warning">
                    <i class="glyphicon glyphicon-refresh"></i>
                    {lang t="system|refresh"}
                </a>
            </div>
        </div>
    </div>
    <div class="well well-sm">
        <div class="row">
            <div class="col-sm-9 cache-type">
                {lang t="system|cache_type_minify"}
            </div>
            <div class="col-sm-3">
                <a href="{uri args="acp/system/maintenance/cache/action_minify"}" class="btn btn-block btn-warning">
                    <i class="glyphicon glyphicon-refresh"></i>
                    {lang t="system|refresh"}
                </a>
            </div>
        </div>
    </div>
    <div class="well well-sm">
        <div class="row">
            <div class="col-sm-9 cache-type">
                {lang t="system|cache_type_images"}
            </div>
            <div class="col-sm-3">
                <a href="{uri args="acp/system/maintenance/cache/action_images"}" class="btn btn-block btn-warning">
                    <i class="glyphicon glyphicon-refresh"></i>
                    {lang t="system|refresh"}
                </a>
            </div>
        </div>
    </div>
{/block}
