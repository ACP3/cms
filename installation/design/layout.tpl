<!DOCTYPE html>
<html lang="{$LANG}" dir="{$LANG_DIRECTION}">
<head>
    <meta charset="UTF-8">
    <title>{$TITLE} | {$PAGE_TITLE}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="{$ROOT_DIR}ACP3/Modules/ACP3/System/Resources/Assets/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="{$DESIGN_PATH}Assets/css/style.min.css">
    <!-- STYLESHEETS -->
    <!--[if lt IE 9]>
        <script defer src="{$ROOT_DIR}ACP3/Modules/ACP3/System/Resources/Assets/js/html5shiv.min.js"></script>
    <![endif]-->
</head>

<body>
<div class="container">
    <h1 id="logo" class="my-2 text-center d-none d-sm-block">
        <img src="{$DESIGN_PATH}Assets/img/logo.png"
             srcset="{$DESIGN_PATH}Assets/img/logo.png 1x, {$DESIGN_PATH}Assets/img/logo@2x.png 2x"
             alt="{$PAGE_TITLE}">
    </h1>
    {if !empty($navbar)}
        <nav id="main-navigation" class="navbar navbar-expand-lg navbar-light bg-light my-3 mt-sm-0">
            <a href="{$ROOT_DIR}" class="navbar-brand d-sm-none">
                <img src="{$DESIGN_PATH}Assets/img/logo.png"
                     srcset="{$DESIGN_PATH}Assets/img/logo.png 1x, {$DESIGN_PATH}Assets/img/logo@2x.png 2x"
                     alt="{$PAGE_TITLE}">
            </a>
            <button class="navbar-toggler"
                    type="button"
                    data-toggle="collapse"
                    data-target="#main-navigation-collapse"
                    aria-controls="main-navigation-collapse"
                    aria-expanded="false"
                    aria-label="{lang t="install|toggle_navigation"}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="main-navigation-collapse">
                <ul class="nav navbar-nav">
                    {foreach $navbar as $key => $value}
                        <li class="nav-item{if $value.active === true} active{/if}">
                            <span class="nav-link{if $value.complete === true} text-success{/if}">{$value.lang}</span>
                        </li>
                    {/foreach}
                </ul>
            </div>
        </nav>
    {/if}
    <main id="content">
        <h2>{$TITLE}</h2>
        {block CONTENT}{/block}
    </main>
    <footer id="footer" class="row align-items-sm-center my-3">
        <div class="col-sm-6 text-center text-sm-left mb-2 mb-sm-0">
            &copy; ACP3 CMS
        </div>
        <div class="col-sm-6">
            <form action="{$REQUEST_URI}" method="post" id="languages" class="form-inline justify-content-end">
                <select name="lang"
                        id="lang"
                        class="form-control form-control-sm"
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
    </footer>
</div>
<script defer src="{$ROOT_DIR}ACP3/Modules/ACP3/System/Resources/Assets/js/jquery.min.js"></script>
<script defer src="{$ROOT_DIR}ACP3/Modules/ACP3/System/Resources/Assets/js/bootstrap.min.js"></script>
<script defer src="{$DESIGN_PATH}Assets/js/language-switcher.js"></script>
<!-- JAVASCRIPTS -->
</body>
</html>
