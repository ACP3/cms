{if !empty($title)}
    <p>{$title}</p>
{/if}
<div class="list-group">
    {foreach $categories as $category}
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-start"
           href="{uri args="files/index/files/cat_`$category.id`"}">
            <div class="ms-2 me-auto">
                <div class="fw-bold">{$category.title}</div>
                {if !empty($category.description)}
                    {$category.description}
                {/if}
            </div>
        </a>
    {/foreach}
</div>
