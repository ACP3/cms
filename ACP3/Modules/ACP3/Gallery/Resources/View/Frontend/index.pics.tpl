{extends file="asset:`$LAYOUT`"}

{if !empty($gallery.subtitle)}
    {block PAGE_TITLE}
        {$smarty.block.parent}
        <p class="fs-5 mb-2">{$gallery.subtitle}</p>
    {/block}
{/if}

{block CONTENT}
    {if !empty($gallery.description)}
        {$gallery.description|rewrite_uri}
    {/if}
    {if !empty($gallery.pictures)}
        <div class="gallery-pictures">
            {foreach $gallery.pictures as $row}
                <a href="{uri args="gallery/index/details/id_`$row.id`"}"
                   class="gallery-picture-thumb"
                   data-pswp-src="{$row.uri_picture}"
                   data-pswp-width="{$row.width}"
                   data-pswp-height="{$row.height}">
                    <img src="{$row.uri_thumbnail}"
                         alt="{$row.description|strip_tags|trim}"
                         width="{$row.thumbnail_width}"
                         height="{$row.thumbnail_height}"
                         loading="lazy"
                         class="img-thumbnail">
                </a>
            {/foreach}
        </div>
        {event name="share.layout.add_social_sharing"}
        {javascripts}
            {include_js module="gallery" file="frontend/index.pics" depends="photoswipe"}
        {/javascripts}
    {else}
        {include file="asset:System/Partials/no_results.tpl" no_results_text={lang t="gallery|no_pictures"}}
    {/if}
{/block}
