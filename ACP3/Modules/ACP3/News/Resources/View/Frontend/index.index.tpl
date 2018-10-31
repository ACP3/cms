{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    <div class="card bg-light mb-3">
        <div class="card-body py-sm-2">
            <div class="row align-items-center">
                {if {has_permission path="frontend/newsletter/index/index"}}
                    <div class="col-sm mb-3 mb-sm-0">
                        <a href="{uri args="newsletter"}" class="card-link">
                            {lang t="newsletter|subscribe_unsubscribe_the_newsletter"}
                        </a>
                    </div>
                {/if}
                <div class="col-sm">
                    <form action="{uri args="news"}" method="post" class="form-inline d-flex justify-content-end">
                        {include file="asset:Categories/Partials/list.tpl" categories=$categories}
                        <button type="submit" name="submit" class="btn btn-primary mt-2 mt-sm-0 ml-2">{lang t="system|submit"}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {if !empty($news)}
        {include file="asset:System/Partials/pagination.tpl" pagination=$pagination}
        {foreach $news as $row}
            <article class="card mb-3">
                <header class="card-header d-sm-flex align-items-end">
                    <h3 class="h5 card-title flex-grow-1 mb-0">
                        <a href="{uri args="news/index/details/id_`$row.id`"}">{$row.title}</a>
                    </h3>
                    <time class="card-subtitle small" datetime="{date_format date=$row.start format="c"}">
                        {date_format date=$row.start format=$dateformat}
                    </time>
                </header>
                <div class="card-body">
                    {$row.text|rewrite_uri}
                </div>
                {if isset($row.comments_count)}
                    <footer class="card-footer text-center">
                        <a href="{uri args="news/index/details/id_`$row.id`"}#comments">{lang t="comments|comments"}</a>
                        <span>({$row.comments_count})</span>
                    </footer>
                {/if}
                {event name="news.event.news_index_after" id=$row.id title=$row.title}
            </article>
        {/foreach}
        {include file="asset:System/Partials/pagination.tpl" pagination=$pagination}
    {else}
        {include file="asset:System/Partials/no_results.tpl"}
    {/if}

    {$smarty.block.parent}
{/block}
