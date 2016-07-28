<!DOCTYPE html>
<html lang="{$LANG}" dir="{$LANG_DIRECTION}">
<head>
    {include file="asset:System/Partials/head.tpl" inline}
</head>

<body>
<div class="container text-center">
    <h1 id="logo">
        <a href="{uri args=""}">
            <img src="{$DESIGN_PATH}Assets/img/logo.png"
                 srcset="{$DESIGN_PATH}Assets/img/logo.png 1x, {$DESIGN_PATH}Assets/img/logo@2x.png 2x"
                 alt="{site_title}">
        </a>
    </h1>
    <main role="main" id="content">
        <h2>{page_title}</h2>
        {block CONTENT}{/block}
    </main>
</div>
<!-- JAVASCRIPTS -->
</body>
</html>
