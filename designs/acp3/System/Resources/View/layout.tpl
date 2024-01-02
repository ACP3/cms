<!DOCTYPE html>
<html lang="{$LANG}" dir="{$LANG_DIRECTION}">
<head>
    {include file="asset:System/Partials/head.tpl"}
    <!-- JAVASCRIPTS -->
</head>

<body itemscope="" itemtype="http://schema.org/WebPage" data-bs-no-jquery>
{event name="layout.body_start"}
{load_module module="widget/users/index/login"}
{load_module module="widget/users/index/user_menu"}
<div id="wrapper" class="container">
    <h1 class="d-none d-lg-block my-2 mx-3">
        {if $IS_HOMEPAGE}
            {include file="asset:System/Partials/picture.tpl" picture=['filename' => 'logo', 'module' => 'system', 'alt' => {site_title}]}
        {else}
            <a href="{uri args=""}">
                {include file="asset:System/Partials/picture.tpl" picture=['filename' => 'logo', 'module' => 'system', 'alt' => {site_title}]}
            </a>
        {/if}
    </h1>
    <nav id="main-navigation" class="navbar navbar-expand-lg navbar-light bg-light mb-3">
        <div class="container-fluid">
            <a class="navbar-brand d-lg-none" href="{$ROOT_DIR}">
                {include file="asset:System/Partials/picture.tpl" picture=['filename' => 'logo', 'module' => 'system', 'alt' => {site_title}, 'height' => 30]}
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main-navigation-content" aria-controls="main-navigation-content" aria-expanded="false" aria-label="{lang t="system|toggle_navigation"}">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="main-navigation-content">
                {navbar block="main" class="navbar-nav me-auto mb-2 mb-lg-0" classLink="nav-link" itemSelectors="nav-item"}
                {load_module module="widget/search"}
            </div>
        </div>
    </nav>
    <div class="row">
        <main id="content" class="{if $IN_ADM}col{else}col col-md-9{/if}">
            <div id="breadcrumb">
                {block BREADCRUMB}
                    {breadcrumb}
                {/block}
            </div>
            {block PAGE_TITLE}
                <h2 itemprop="name">{page_title}</h2>
            {/block}
            {event name="layout.content_before"}
            {block EDIT_CONTENT}{/block}
            {block CONTENT}{/block}
            {event name="layout.content_after"}
        </main>
        {if !$IN_ADM}
            <aside id="sidebar" class="col-md-3 d-none d-md-block">
                {load_module module="widget/newsletter"}
                {load_module module="widget/news"}
                {load_module module="widget/files"}
                {load_module module="widget/articles"}
                {load_module module="widget/articles/index/single" args=['id' => 1]}
                {load_module module="widget/gallery"}
                {load_module module="widget/polls"}
            </aside>
        {/if}
    </div>
    <footer class="row mb-2">
        <div class="col-sm mb-2 text-center text-sm-start">
            &copy; {site_title}
        </div>
        <div class="col-sm text-center text-sm-end">
            {navbar block="sidebar" class="list-inline" itemSelectors="list-inline-item" use_bootstrap=false}
        </div>
    </footer>
</div>
</body>
</html>
