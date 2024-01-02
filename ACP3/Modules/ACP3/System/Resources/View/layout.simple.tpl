<!DOCTYPE html>
<html lang="{$LANG}" dir="{$LANG_DIRECTION}" class="min-vh-100">
<head>
    {include file="asset:System/Partials/head.tpl"}
    <!-- JAVASCRIPTS -->
</head>

<body class="min-vh-100 d-flex align-items-center p-4 bg-body-tertiary" itemscope="" itemtype="http://schema.org/WebPage" data-bs-no-jquery>
{event name="layout.body_start"}
<main id="content" class="container">
    <div class="row">
        <div class="col col-md-10 offset-md-1 col-lg-8 offset-lg-2 col-xl-6 offset-xl-3">
            <div class="text-center mb-3">
                {if $IS_HOMEPAGE}
                    {include file="asset:System/Partials/picture.tpl" picture=['filename' => 'logo', 'module' => 'system', 'alt' => {site_title}]}
                {else}
                    <a href="{uri args=""}">
                        {include file="asset:System/Partials/picture.tpl" picture=['filename' => 'logo', 'module' => 'system', 'alt' => {site_title}]}
                    </a>
                {/if}
            </div>
            <div class="bg-white border p-3 shadow-sm">
                <h1 class="h3 mb-3 fw-normal">{page_title}</h1>
                {event name="layout.content_before"}
                {block EDIT_CONTENT}{/block}
                {block CONTENT}{/block}
                {event name="layout.content_after"}
            </div>
        </div>
    </div>
</main>
</body>
</html>
