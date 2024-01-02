{extends file="asset:`$LAYOUT`"}

{if !empty($file.subtitle)}
    {block PAGE_TITLE}
        {$smarty.block.parent}
        <p class="fs-5 mb-2">{$file.subtitle}</p>
    {/block}
{/if}

{block EDIT_CONTENT}
    {check_access mode="link" path="acp/files/index/edit/id_`$file.id`/" iconSet="solid" icon="pencil" blank=true selectors="w-100 my-3"}
{/block}

{block CONTENT}
    <time class="text-muted d-block mb-3" datetime="{date_format date=$file.start format="c"}">
        {date_format date=$file.start format=$dateformat}
    </time>
    <div class="mb-3">
        {$file.text|rewrite_uri}
    </div>
    <div class="list-group mb-3">
        <a href="{uri args="files/index/download/id_`$file.id`"}" class="list-group-item list-group-item-action">
            {icon iconSet="solid" icon="download"}
            {lang t="files|download_file"}
            {if !empty($file.size)}
                ({$file.size})
            {else}
                ({lang t="files|unknown_filesize"})
            {/if}
        </a>
    </div>
    {event name="share.layout.add_social_sharing"}
    {event name="files.layout.details_after" file=$file}
{/block}
