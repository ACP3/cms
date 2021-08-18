{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if !empty($galleries)}
        {include file="asset:System/Partials/pagination.tpl" pagination=$pagination}
        <div class="row">
            {foreach $galleries as $row}
                <div class="col-sm-3">
                    <div class="card">
                        {if !empty($row.file)}
                            <a href="{uri args="gallery/index/pics/id_`$row.id`"}">
                                <img src="{$row.file}" alt="{$row.title}" class="card-img-top">
                            </a>
                        {/if}
                        <div class="card-body">
                            <h3 class="card-title h5">{$row.title}</h3>
                            <time datetime="{date_format date=$row.start format="c"}" class="card-subtitle d-block mb-3">
                                {date_format date=$row.start format=$dateformat}
                            </time>
                            <a href="{uri args="gallery/index/pics/id_`$row.id`"}" class="btn btn-primary d-block" role="button">
                                {lang t="gallery|show_pictures" args=['%pictures%' => {$row.pics}]}
                            </a>
                        </div>
                    </div>
                </div>
            {/foreach}
        </div>
    {else}
        {include file="asset:System/Partials/no_results.tpl"}
    {/if}
{/block}
