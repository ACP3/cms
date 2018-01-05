<table class="table table-striped">
    {if !empty($title)}
        <thead>
        <tr>
            <th colspan="2">{$title}</th>
        </tr>
        </thead>
    {/if}
    <tbody>
    {foreach $categories as $category}
        <tr>
            <td style="width:25%"><a href="{uri args="files/index/files/cat_`$category.id`"}">{$category.title}</a></td>
            <td>{$category.description}</td>
        </tr>
    {/foreach}
    </tbody>
</table>
