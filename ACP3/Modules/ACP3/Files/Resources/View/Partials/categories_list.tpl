{if !empty($categories)}
    <div class="card mb-3">
        {if !empty($title)}
            <header class="card-header">
                <h3 class="h6 card-title mb-0">
                    {$title}
                </h3>
            </header>
        {/if}
        <div class="list-group list-group-flush">
            {foreach $categories as $category}
                <a href="{uri args="files/index/files/cat_`$category.id`"}" class="list-group-item">{$category.title}</a>
            {/foreach}
        </div>
    </div>
    {if !empty($title)}
        <hr>
    {/if}
{/if}
