<nav class="border rounded p-2 mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0" itemscope itemtype="http://schema.org/BreadcrumbList">
        <li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
            <a itemprop="item" href="{uri args=""}" title="{lang t="system|home"}">
                <span itemprop="name">{site_title}</span>
            </a>
            <meta itemprop="position" content="1" />
        </li>
        {foreach $breadcrumb as $row}
            <li class="breadcrumb-item{if isset($row.last) && $row.last === true} active{/if}"
                {if isset($row.last) && $row.last === true}aria-current="page"{/if}
                itemprop="itemListElement"
                itemscope
                itemtype="http://schema.org/ListItem"><a itemprop="item" href="{$row.uri}"><span itemprop="name">{$row.title}</span></a>
                <meta itemprop="position" content="{($row@iteration + 1)}" />
            </li>
        {/foreach}
    </ol>
</nav>
