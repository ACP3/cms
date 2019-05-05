{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if !empty($gallery.description)}
        {$gallery.description}
    {/if}
    {if !empty($pictures)}
        {if $overlay == 1}
            {foreach $pictures as $row}
                <a href="{$row.uri_picture}"
                   class="gallery-picture-thumb"
                   data-fancybox="gallery"
                   data-type="image"
                   {if !empty($row.description)}data-caption="{$row.description|strip_tags|trim}"{/if}>
                    <img src="{$row.uri_thumb}"
                         alt="{$row.description|strip_tags|trim}"
                         class="img-thumbnail mb-1">
                </a>
            {/foreach}
            {js_libraries enable="fancybox"}
        {else}
            {foreach $pictures as $row}
                <a href="{uri args="gallery/index/details/id_`$row.id`"}"
                   class="gallery-picture-thumb">
                    <img src="{$row.uri_thumb}"
                         alt="{$row.description|strip_tags|trim}"
                         class="img-thumbnail mb-1">
                </a>
            {/foreach}
        {/if}
        {event name="share.layout.add_social_sharing"}
    {else}
        {include file="asset:System/Partials/no_results.tpl" no_results_text={lang t="gallery|no_pictures"}}
    {/if}
{/block}
