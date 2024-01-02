<!DOCTYPE html>
<html lang="{$LANG}" dir="{$LANG_DIRECTION}" class="h-100">
<head>
    {include file="asset:System/Partials/head.tpl"}
    <!-- JAVASCRIPTS -->
</head>

<body class="h-100 d-flex align-items-center p-4" itemscope="" itemtype="http://schema.org/WebPage" data-bs-no-jquery>
{event name="layout.body_start"}
<main id="content" class="container">
    <div class="text-center mb-3">
        {if $IS_HOMEPAGE}
            {include file="asset:System/Partials/picture.tpl" picture=['filename' => 'logo', 'module' => 'system', 'alt' => {site_title}]}
        {else}
            <a href="{uri args=""}">
                {include file="asset:System/Partials/picture.tpl" picture=['filename' => 'logo', 'module' => 'system', 'alt' => {site_title}]}
            </a>
        {/if}
    </div>
    <h1 class="h3 mb-3 fw-normal">{page_title}</h1>
    {event name="layout.content_before"}
    {block EDIT_CONTENT}{/block}
    {block CONTENT}{/block}
    {event name="layout.content_after"}
</main>
</body>
</html>
