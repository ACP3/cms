{extends file="asset:layout.tpl"}

{block CONTENT prepend}
    {if isset($articles)}
        {$pagination}
        {foreach $articles as $row}
            <div class="dataset-box">
                <div class="navbar navbar-default">
                    <div class="navbar-header">
                        <h2 class="navbar-brand"><a href="{uri args="articles/index/details/id_`$row.id`"}">{$row.title}</a></h2>
                    </div>
                    <small class="navbar-text pull-right">
                        <time datetime="{date_format date=$row.start format="c"}">{date_format date=$row.start}</time>
                    </small>
                </div>
            </div>
        {/foreach}
    {else}
        <div class="alert alert-warning text-center">
            <strong>{lang t="system|no_entries"}</strong>
        </div>
    {/if}
{/block}