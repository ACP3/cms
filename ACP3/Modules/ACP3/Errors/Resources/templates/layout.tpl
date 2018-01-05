<!DOCTYPE html>
<html lang="{$LANG}" dir="{$LANG_DIRECTION}">
<head>
    {include file="asset:System/Partials/head.tpl" inline}
</head>

<body>
<div class="container text-center">
    <header>
        <h1 id="logo">
            <a href="{uri args=""}">
                <img src="{image file="logo.png"}"
                     srcset="{image file="logo.png"} 1x, {image file="logo@2x.png"} 2x"
                     alt="{site_title}">
            </a>
        </h1>
    </header>
    <main id="content">
        <h1 class="h2">{page_title}</h1>
        {block CONTENT}{/block}
    </main>
</div>
<!-- JAVASCRIPTS -->
</body>
</html>
