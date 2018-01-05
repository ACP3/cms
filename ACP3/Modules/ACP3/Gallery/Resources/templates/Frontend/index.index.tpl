{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if !empty($galleries)}
        {include file="asset:System/Partials/pagination.tpl" pagination=$pagination}
        <div class="row">
            {foreach $galleries as $row}
                <div class="col-sm-3 gallery">
                    <div class="thumbnail gallery__thumbnail">
                        <a href="{uri args="gallery/index/pics/id_`$row.id`"}">
                            {if !empty($row.picture_id)}
                                <img src="{uri args="gallery/index/image/id_`$row.picture_id`/action_thumb"}" alt="">
                            {/if}
                            <div class="caption">
                                <h2 class="h3 gallery__title">{$row.title}</h2>
                                <p>
                                    <time datetime="{date_format date=$row.start format="c"}">
                                        {date_format date=$row.start format=$dateformat}
                                    </time>
                                </p>
                                <p>
                                    <span class="btn btn-primary btn-block" role="button">
                                    {lang t="gallery|show_pictures" args=['%pictures%' => {$row.pics}]}
                                    </span>
                                </p>
                            </div>
                        </a>
                    </div>
                </div>
            {/foreach}
        </div>
    {else}
        {include file="asset:System/Partials/no_results.tpl"}
    {/if}
{/block}
