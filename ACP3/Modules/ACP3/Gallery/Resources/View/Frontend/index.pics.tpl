{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if !empty($gallery.description)}
        {$gallery.description}
    {/if}
    {if !empty($gallery.pictures)}
        {if $overlay == 1}
            <div class="gallery-pictures">
                {foreach $gallery.pictures as $row}
                    <a href="{$row.uri_picture}"
                       class="gallery-picture-thumb"
                       data-pswp-width="{$row.width}"
                       data-pswp-height="{$row.height}"
                       {if !empty($row.title)}data-caption="{$row.title|strip_tags|trim}"{/if}>
                        <img src="{$row.uri_thumbnail}"
                             alt="{$row.description|strip_tags|trim}"
                             width="{$row.thumbnail_width}"
                             height="{$row.thumbnail_height}"
                             loading="lazy"
                             class="img-thumbnail">
                    </a>
                {/foreach}
            </div>
            {javascripts}
                {include_js module="gallery" file="frontend/index.pics" depends="photoswipe"}
            {/javascripts}
        {else}
            {foreach $gallery.pictures as $row}
                <a href="{uri args="gallery/index/details/id_`$row.id`"}"
                   class="gallery-picture-thumb">
                    <img src="{$row.uri_thumbnail}"
                         alt="{$row.description|strip_tags|trim}"
                         width="{$row.thumbnail_width}"
                         height="{$row.thumbnail_height}"
                         loading="lazy"
                         class="img-thumbnail">
                </a>
            {/foreach}
        {/if}
        {event name="share.layout.add_social_sharing"}
    {else}
        {include file="asset:System/Partials/no_results.tpl" no_results_text={lang t="gallery|no_pictures"}}
    {/if}
{/block}
