<!DOCTYPE html>
<html lang="{$LANG}" dir="{$LANG_DIRECTION}">
<head>
    <title>{$TITLE} | {$PAGE_TITLE}</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="{$ROOT_DIR}ACP3/Modules/ACP3/System/Resources/Assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="{$DESIGN_PATH}Assets/css/style.css">
    <!-- STYLESHEETS -->
    <!--[if lt IE 9]>
        <script src="{$ROOT_DIR}ACP3/Modules/ACP3/System/Resources/Assets/js/libs/html5shiv.min.js"></script>
    <![endif]-->
</head>

<body>
<div class="container">
    <h1 id="logo" class="hidden-xs">
        <img src="{$DESIGN_PATH}Assets/img/logo.png"
             srcset="{$DESIGN_PATH}Assets/img/logo.png 1x, {$DESIGN_PATH}Assets/img/logo@2x.png 2x"
             alt="{$PAGE_TITLE}">
    </h1>
    <nav id="main-navigation" class="navbar navbar-default" role="navigation">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse">
                <span class="sr-only">{lang t="install|toggle_navigation"}</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <span class="navbar-brand hidden-sm hidden-md hidden-lg">
                <img src="{$DESIGN_PATH}Assets/img/logo.png"
                     srcset="{$DESIGN_PATH}Assets/img/logo.png 1x, {$DESIGN_PATH}Assets/img/logo@2x.png 2x"
                     alt="{$PAGE_TITLE}">
            </span>
        </div>
        <div id="navbar-collapse" class="collapse navbar-collapse">
            {if isset($navbar)}
                <ul class="nav navbar-nav">
                    {foreach $navbar as $key => $value}
                        <li{if $value.active === true} class="active"{/if}><a href="#">{$value.lang}</a></li>
                    {/foreach}
                </ul>
            {/if}
            <form action="{$REQUEST_URI}" method="post" id="languages" class="navbar-form navbar-right">
                <div class="form-group">
                    <select name="lang"
                            id="lang"
                            class="form-control"
                            title="{lang t="install|select_language"}"
                            data-change-language-warning="{lang t="install|form_change_warning"}">
                        {foreach $LANGUAGES as $row}
                            <option value="{$row.language}"{$row.selected}>{$row.name}</option>
                        {/foreach}
                    </select>
                </div>
                <input type="submit" name="languages" value="{lang t="install|submit"}" class="btn btn-primary">
            </form>
        </div>
    </nav>
    <main role="main" id="content">
        <h2>{$TITLE}</h2>
        {block CONTENT}{/block}
    </main>
</div>
<script type="text/javascript" src="{$ROOT_DIR}ACP3/Modules/ACP3/System/Resources/Assets/js/libs/jquery.min.js"></script>
<script type="text/javascript" src="{$ROOT_DIR}ACP3/Modules/ACP3/System/Resources/Assets/js/libs/bootstrap.min.js"></script>
<script type="text/javascript" src="{$DESIGN_PATH}Assets/js/language-switcher.js"></script>
<!-- JAVASCRIPTS -->
</body>
</html>
