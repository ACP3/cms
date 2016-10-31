{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if isset($pictures)}
        {if $overlay == 1}
            {foreach $pictures as $row}
                <a href="{uri args="gallery/index/image/id_`$row.id`/action_normal"}"
                   data-fancybox-group="gallery"
                   {if !empty($row.description)}title="{$row.description|strip_tags}"{/if}>
                    <img src="{uri args="gallery/index/image/id_`$row.id`/action_thumb"}" alt="" class="img-thumbnail">
                </a>
            {/foreach}
            {javascripts}
                {include_js module="gallery" file="frontend/index.pics" depends="fancybox"}
            {/javascripts}
        {else}
            {foreach $pictures as $row}
                <a href="{uri args="gallery/index/details/id_`$row.id`"}">
                    <img src="{uri args="gallery/index/image/id_`$row.id`/action_thumb"}" alt="" class="img-thumbnail">
                </a>
            {/foreach}
        {/if}
    {else}
        <div class="alert alert-warning text-center">
            <strong>{lang t="gallery|no_pictures"}</strong>
        </div>
    {/if}
{/block}
