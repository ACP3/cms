{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if !empty($pictures)}
        {if $overlay == 1}
            {foreach $pictures as $row}
                <a href="{uri args="gallery/index/image/id_`$row.id`/action_normal"}"
                   class="gallery-picture-thumb"
                   data-fancybox="gallery"
                   data-type="image"
                   {if !empty($row.description)}data-caption="{$row.description|strip_tags|trim}"{/if}>
                    <img src="{uri args="gallery/index/image/id_`$row.id`/action_thumb"}"
                         alt="{$row.description|strip_tags|trim}"
                         class="img-thumbnail">
                </a>
            {/foreach}
            {javascripts}
                {include_js depends="fancybox"}
            {/javascripts}
        {else}
            {foreach $pictures as $row}
                <a href="{uri args="gallery/index/details/id_`$row.id`"}"
                   class="gallery-picture-thumb">
                    <img src="{uri args="gallery/index/image/id_`$row.id`/action_thumb"}"
                         alt="{$row.description|strip_tags|trim}"
                         class="img-thumbnail">
                </a>
            {/foreach}
        {/if}
    {else}
        {include file="asset:System/Partials/no_results.tpl" no_results_text={lang t="gallery|no_pictures"}}
    {/if}
{/block}
