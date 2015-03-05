{extends file="asset:layout.tpl"}

{block CONTENT}
    {if !empty($galleries)}
        {$pagination}
        {foreach $galleries as $row}
            <div class="dataset-box">
                <div class="navbar navbar-default">
                    <div class="navbar-header">
                        <h2 class="navbar-brand">
                            <a href="{uri args="gallery/index/pics/id_`$row.id`"}">
                                {$row.title}
                                {if $row.pics == 1}
                                    ({$row.pics} {lang t="gallery|picture"})
                                {else}
                                    ({$row.pics} {lang t="gallery|pictures"})
                                {/if}
                            </a>
                        </h2>
                    </div>
                    <time class="navbar-text small pull-right" datetime="{date_format date=$row.start format="c"}">{date_format date=$row.start format=$dateformat}</time>
                </div>
            </div>
        {/foreach}
    {else}
        <div class="alert alert-warning text-center">
            <strong>{lang t="system|no_entries"}</strong>
        </div>
    {/if}
{/block}