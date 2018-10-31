{if !empty($sidebar_article)}
    <div class="card mb-3">
        <div class="card-header">
            <a href="{uri args="articles/index/details/id_`$sidebar_article.id`"}">
                {$sidebar_article.title}
            </a>
        </div>
        <div class="card-body">
            {$sidebar_article.text|rewrite_uri}
        </div>
    </div>
{/if}
