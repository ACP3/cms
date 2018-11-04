<!DOCTYPE html>
<html lang="{$LANG}" dir="{$LANG_DIRECTION}">
<head>
    {include file="asset:System/Partials/head.tpl" inline}
</head>

<body itemscope="" itemtype="http://schema.org/WebPage">
{load_module module="widget/users/index/login"}
{load_module module="widget/users/index/user_menu"}
<div id="wrapper" class="container">
    <h1 id="logo" class="my-2 mx-3 d-none d-sm-block">
        {if $IS_HOMEPAGE}
            <img src="{image file="logo.png"}"
                 srcset="{image file="logo.png"} 1x, {image file="logo@2x.png"} 2x"
                 alt="{site_title}">
        {else}
            <a href="{uri args=""}">
                <img src="{image file="logo.png"}"
                     srcset="{image file="logo.png"} 1x, {image file="logo@2x.png"} 2x"
                     alt="{site_title}">
            </a>
        {/if}
    </h1>
    <nav id="main-navigation" class="navbar navbar-expand-lg navbar-light bg-light my-3 mt-sm-0">
        <a href="{$ROOT_DIR}" class="navbar-brand d-sm-none">
            <img src="{image file="logo.png"}"
                 srcset="{image file="logo.png"} 1x, {image file="logo@2x.png"} 2x"
                 alt="{site_title}">
        </a>
        <button class="navbar-toggler"
                type="button"
                data-toggle="collapse"
                data-target="#main-navigation-collapse"
                aria-controls="main-navigation-collapse"
                aria-expanded="false"
                aria-label="{lang t="system|toggle_navigation"}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="main-navigation-collapse">
            {navbar block="main"}
            {load_module module="widget/search"}
        </div>
    </nav>
    <div class="row">
        <main id="content" class="col-sm-12{if !$IN_ADM} col-md-9{/if}">
            <div id="breadcrumb">
                {block BREADCRUMB}
                    {include file="asset:System/Partials/breadcrumb.tpl" breadcrumb=$BREADCRUMB}
                {/block}
            </div>
            {block PAGE_TITLE}
                <h2 itemprop="name">{page_title}</h2>
            {/block}
            {event name="layout.content_before"}
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
    <footer class="row footer my-3">
        <div class="col-5 copyright">
            &copy; {site_title}
        </div>
        <div class="col-7">
            {navbar block="sidebar" class=["list-inline", "text-right", "mb-0"] itemSelectors=["list-inline-item"] use_bootstrap=false}
        </div>
    </footer>
</div>
<!-- JAVASCRIPTS -->
</body>
</html>
