<ul class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
    <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
        <a itemprop="item" href="{uri args=""}" title="{lang t="system|home"}">
            <span itemprop="name">{site_title}</span>
        </a>
        <meta itemprop="position" content="1" />
    </li>
    {foreach $breadcrumb as $row}
        <li {if isset($row.last) && $row.last === true}class="active"{/if}
            itemprop="itemListElement"
            itemscope
            itemtype="http://schema.org/ListItem"><a itemprop="item" href="{$row.uri}"><span itemprop="name">{$row.title}</span></a>
            <meta itemprop="position" content="{($row@iteration + 1)}" />
        </li>
    {/foreach}
</ul>
