<div id="breadcrumb">
    {block BREADCRUMB}
        {include file="asset:system/breadcrumb.tpl" breadcrumb=$BREADCRUMB}
    {/block}
</div>
<h2>{$TITLE}</h2>
{block CONTENT}
    {$CONTENT}
{/block}
<!-- JAVASCRIPTS -->