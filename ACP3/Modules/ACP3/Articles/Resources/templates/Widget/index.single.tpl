{if !empty($sidebar_article)}
    <section class="panel panel-default">
        <header class="panel-heading">
            <h2 class="panel-title">
                <a href="{uri args="articles/index/details/id_`$sidebar_article.id`"}">
                    {$sidebar_article.title}
                </a>
            </h2>
        </header>
        <div class="panel-body">
            {$sidebar_article.text|rewrite_uri}
        </div>
    </section>
{/if}
