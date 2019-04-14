<!DOCTYPE html>
<html lang="{$LANG}" dir="{$LANG_DIRECTION}">
<head>
    <meta charset="UTF-8">
    <title>{$TITLE} | {$PAGE_TITLE}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="{$ROOT_DIR}ACP3/Modules/ACP3/System/Resources/Assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="{$DESIGN_PATH}Assets/css/style.min.css">
    <!-- STYLESHEETS -->
    <!--[if lt IE 9]>
        <script defer src="{$ROOT_DIR}ACP3/Modules/ACP3/System/Resources/Assets/js/html5shiv.min.js"></script>
    <![endif]-->
</head>

<body>
<div class="container">
    <h1 id="logo" class="text-center hidden-xs">
        <img src="{$DESIGN_PATH}Assets/img/logo.png"
             srcset="{$DESIGN_PATH}Assets/img/logo.png 1x, {$DESIGN_PATH}Assets/img/logo@2x.png 2x"
             alt="{$PAGE_TITLE}">
    </h1>
    <nav id="main-navigation" class="navbar navbar-default{if empty($navbar)} visible-xs{/if}">
        <div class="navbar-header">
            {if !empty($navbar)}
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse">
                    <span class="sr-only">{lang t="install|toggle_navigation"}</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            {/if}
            <span class="navbar-brand hidden-sm hidden-md hidden-lg">
                <img src="{$DESIGN_PATH}Assets/img/logo.png"
                     srcset="{$DESIGN_PATH}Assets/img/logo.png 1x, {$DESIGN_PATH}Assets/img/logo@2x.png 2x"
                     alt="{$PAGE_TITLE}">
            </span>
        </div>
        {if !empty($navbar)}
            <div id="navbar-collapse" class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    {foreach $navbar as $key => $value}
                        <li {if $value.active === true} class="active"{elseif $value.complete === true} class="complete"{/if}>
                            <a href="#">{$value.lang}</a>
                        </li>
                    {/foreach}
                </ul>
            </div>
        {/if}
    </nav>
    <main id="content">
        <h2>{$TITLE}</h2>
        {block CONTENT}{/block}
    </main>
    <footer id="footer">
        <div class="row">
            <div class="col-sm-6">
                &copy; ACP3 CMS
            </div>
            <div class="col-sm-6 text-right">
                <form action="{$REQUEST_URI}" method="post" id="languages" class="form-inline">
                    <select name="lang"
                            id="lang"
                            class="form-control input-sm"
                            title="{lang t="install|select_language"}"
                            data-change-language-warning="{lang t="install|form_change_warning"}">
                        {foreach $LANGUAGES as $row}
                            <option value="{$row.language}"{$row.selected}>{$row.name}</option>
                        {/foreach}
                    </select>
                    <button type="submit" name="languages" class="btn btn-primary btn-sm">
                        {lang t="install|submit"}
                    </button>
                </form>
            </div>
        </div>
    </footer>
</div>
<script defer src="{$ROOT_DIR}ACP3/Modules/ACP3/System/Resources/Assets/js/polyfill.min.js"></script>
<script defer src="{$ROOT_DIR}ACP3/Modules/ACP3/System/Resources/Assets/js/jquery.min.js"></script>
<script defer src="{$ROOT_DIR}ACP3/Modules/ACP3/System/Resources/Assets/js/bootstrap.min.js"></script>
<script defer src="{$DESIGN_PATH}Assets/js/language-switcher.min.js"></script>
<!-- JAVASCRIPTS -->
</body>
</html>
