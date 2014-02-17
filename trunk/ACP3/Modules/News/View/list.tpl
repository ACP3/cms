<div class="navbar navbar-default">
    <div class="container-fluid">
        {if isset($categories)}
            {if {has_permission mod="newsletter" file="list"}}
                <div class="navbar-text">
                    <a href="{uri args="newsletter/list"}">{lang t="newsletter|list"}</a>
                </div>
            {/if}
            <form action="{uri args="news/list"}" method="post" class="navbar-form navbar-right">
                <div class="form-group">
                    {$categories}
                </div>
                <button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
            </form>
        {/if}
    </div>
</div>{if isset($news)}
{$pagination}
    {foreach $news as $row}
        <article class="dataset-box">
            <header class="navbar navbar-default">
                <div class="navbar-header">
                    <h2 class="navbar-brand"><a href="{uri args="news/details/id_`$row.id`"}">{$row.title}</a></h2>
                </div>
                <small class="navbar-text pull-right">
                    <time datetime="{$row.date_iso}">{$row.date_formatted}</time>
                </small>
            </header>
            <div class="content">
                {$row.text}
            </div>
            {if isset($row.comments_count)}
                <footer class="text-center">
                    <a href="{uri args="news/details/id_`$row.id`"}#comments">{lang t="comments|comments"}</a>
                    <span>({$row.comments_count})</span>
                </footer>
            {/if}
        </article>
    {/foreach}
{else}
    <div class="alert alert-warning text-center">
        <strong>{lang t="system|no_entries"}</strong>
    </div>
{/if}