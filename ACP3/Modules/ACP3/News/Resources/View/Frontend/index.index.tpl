{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    <div class="navbar navbar-default">
    <div class="container-fluid">
        {if isset($categories)}
            {if {has_permission path="frontend/newsletter/index/index"}}
                <div class="navbar-text">
                    <a href="{uri args="newsletter"}">{lang t="newsletter|subscribe_unsubscribe_the_newsletter"}</a>
                </div>
            {/if}
            <form action="{uri args="news"}" method="post" class="navbar-form navbar-right">
                <div class="form-group">
                    {$categories}
                </div>
                <button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
            </form>
        {/if}
    </div>
    </div>
    {if !empty($news)}
        {include file="asset:System/pagination.tpl" pagination=$pagination}
        {foreach $news as $row}
            <article class="dataset-box">
                <header class="navbar navbar-default">
                    <div class="navbar-header">
                        <h2 class="navbar-brand"><a href="{uri args="news/index/details/id_`$row.id`"}">{$row.title}</a></h2>
                    </div>
                    <time class="navbar-text small pull-right" datetime="{date_format date=$row.start format="c"}">{date_format date=$row.start format=$dateformat}</time>
                </header>
                <div class="content">
                    {$row.text|rewrite_uri}
                </div>
                {if isset($row.comments_count)}
                    <footer class="text-center">
                        <a href="{uri args="news/index/details/id_`$row.id`"}#comments">{lang t="comments|comments"}</a>
                        <span>({$row.comments_count})</span>
                    </footer>
                {/if}
            </article>
        {/foreach}
        {include file="asset:System/pagination.tpl" pagination=$pagination}
    {else}
        {include file="asset:System/Partials/no_results.tpl"}
    {/if}

    {$smarty.block.parent}
{/block}
