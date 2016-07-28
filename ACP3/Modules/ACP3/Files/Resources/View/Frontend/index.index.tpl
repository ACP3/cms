{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if !empty($categories)}
        <table class="table table-striped">
            <thead>
            <tr>
                <th colspan="2">{lang t="files|category_select"}</th>
            </tr>
            </thead>
            <tbody>
            {foreach $categories as $cat}
                <tr>
                    <td style="width:25%"><a href="{uri args="files/index/files/cat_`$cat.id`"}">{$cat.title}</a></td>
                    <td>{$cat.description}</td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    {else}
        {include file="asset:System/Partials/no_results.tpl"}
    {/if}
{/block}
