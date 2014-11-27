<ul class="breadcrumb">
    <li><a href="{uri args=""}">{lang t="system|home"}</a></li>
    {if isset($breadcrumb)}
        {foreach $breadcrumb as $row}
            {if !isset($row.last) && !empty($row.uri)}
                <li><a href="{$row.uri}">{$row.title}</a></li>
            {elseif isset($row.last)}
                <li class="active">{$row.title}</li>
            {/if}
        {/foreach}
    {/if}
</ul>