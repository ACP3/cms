{if isset($newsletters)}
    {$pagination}
    {foreach $newsletters as $row}
        <div class="dataset-box">
            <div class="navbar navbar-default">
                <div class="navbar-header">
                    <h2 class="navbar-brand">
                        <a href="{uri args="newsletter/index/details/id_`$row.id`"}">{$row.title}</a></h2>
                </div>
                <small class="navbar-text pull-right">
                    <time datetime="{$row.date_iso}">{$row.date_formatted}</time>
                </small>
            </div>
        </div>
    {/foreach}
{else}
    <div class="alert alert-warning text-center">
        <strong>{lang t="system|no_entries"}</strong>
    </div>
{/if}