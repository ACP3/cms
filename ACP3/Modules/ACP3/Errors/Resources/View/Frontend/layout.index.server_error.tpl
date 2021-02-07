<!DOCTYPE html>
<html lang="{$LANG}" dir="{$LANG_DIRECTION}">
<head>
    {include file="asset:System/Partials/head.tpl" inline}
    <!-- JAVASCRIPTS -->
</head>

<body>
<div class="container text-center">
    <h1 id="logo">
        <a href="{uri args=""}">
            <img src="{image file="logo.png"}"
                 srcset="{image file="logo.png"} 1x, {image file="logo@2x.png"} 2x"
                 alt="{site_title}">
        </a>
    </h1>
    <main id="content">
        <h2>{page_title}</h2>
        {block CONTENT}{/block}
    </main>
</div>
</body>
</html>
