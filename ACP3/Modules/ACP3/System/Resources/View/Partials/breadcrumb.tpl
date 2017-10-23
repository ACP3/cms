<ul class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
    <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
        <a itemprop="item" href="{uri args=""}">
            <span itemprop="name">{lang t="system|home"}</span>
        </a>
        <meta itemprop="position" content="1" />
    </li>
    {if isset($breadcrumb)}
        {foreach $breadcrumb as $row}
            {if !isset($row.last) && !empty($row.uri)}
                <li itemprop="itemListElement"
                    itemscope
                    itemtype="http://schema.org/ListItem"><a itemprop="item" href="{$row.uri}"><span itemprop="name">{$row.title}</span></a>
                    <meta itemprop="position" content="{($row@iteration + 1)}" />
                </li>
            {elseif isset($row.last)}
                <li class="active"
                    itemprop="itemListElement"
                    itemscope
                    itemtype="http://schema.org/ListItem"><span itemprop="item"><span itemprop="name">{$row.title}</span></span>
                    <meta itemprop="position" content="{($row@iteration + 1)}" />
                </li>
            {/if}
        {/foreach}
    {/if}
</ul>
