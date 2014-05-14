{if isset($pictures)}
    {if $overlay == 1}
        {js_libraries enable="fancybox"}
        {include_js module="gallery" file="pics"}
        <ul class="thumbnails">
            {foreach $pictures as $row}
                <li>
                    <a href="{$row.uri}" class="thumbnail" data-fancybox-group="gallery"{if !empty($row.description)} title="{$row.description}"{/if}>
                        <img src="{uri args="gallery/image/id_`$row.id`/action_thumb"}" alt="" width="{$row.width}" height="{$row.height}">
                    </a>
                </li>
            {/foreach}
        </ul>
    {else}
        <ul class="thumbnails">
            {foreach $pictures as $row}
                <li>
                    <a href="{$row.uri}" class="thumbnail">
                        <img src="{uri args="gallery/image/id_`$row.id`/action_thumb"}" alt="" width="{$row.width}" height="{$row.height}">
                    </a>
                </li>
            {/foreach}
        </ul>
    {/if}
{else}
    <div class="alert alert-warning text-center">
        <strong>{lang t="gallery|no_pictures"}</strong>
    </div>
{/if}