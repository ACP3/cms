<div id="breadcrumb">
    {block BREADCRUMB}
        {include file="asset:System/Partials/breadcrumb.tpl" breadcrumb=$BREADCRUMB}
    {/block}
</div>
<h2 itemprop="name">{page_title}</h2>
{event name="layout.content_before"}
{block CONTENT}{/block}
{event name="layout.content_after"}
<!-- JAVASCRIPTS -->
