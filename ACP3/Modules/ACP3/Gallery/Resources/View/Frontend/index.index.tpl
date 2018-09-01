{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if !empty($galleries)}
        {include file="asset:System/Partials/pagination.tpl" pagination=$pagination}
        <div class="row">
            {foreach $galleries as $row}
                <div class="col-sm-3">
                    <div class="thumbnail">
                        {if !empty($row.picture_id)}
                            <a href="{uri args="gallery/index/pics/id_`$row.id`"}">
                                <img src="{uri args="gallery/index/image/id_`$row.picture_id`/action_thumb"}" alt="{$row.title}">
                            </a>
                        {/if}
                        <div class="caption">
                            <h3>{$row.title}</h3>
                            <p>
                                <time datetime="{date_format date=$row.start format="c"}">
                                    {date_format date=$row.start format=$dateformat}
                                </time>
                            </p>
                            <p>
                                <a href="{uri args="gallery/index/pics/id_`$row.id`"}" class="btn btn-primary btn-block" role="button">
                                    {lang t="gallery|show_pictures" args=['%pictures%' => {$row.pics}]}
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            {/foreach}
        </div>
    {else}
        {include file="asset:System/Partials/no_results.tpl"}
    {/if}
{/block}
