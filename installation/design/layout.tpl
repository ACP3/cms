{if $IS_AJAX === true}
    {include file="asset:ajax.tpl" inline}
{else}
    <!DOCTYPE html>
    <html lang="{$LANG}" dir="{$LANG_DIRECTION}">
    <head>
        <title>{$TITLE} | {$PAGE_TITLE}</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="{$ROOT_DIR}ACP3/Modules/ACP3/System/Resources/Assets/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="{$INSTALLER_ROOT_DIR}design/Assets/css/style.css">
        <link rel="stylesheet" type="text/css" href="{$INSTALLER_ROOT_DIR}Installer/Modules/Install/Resources/Assets/css/style.css">
        <!--[if lt IE 9]>
            <script src="{$ROOT_DIR}ACP3/Modules/ACP3/System/Resources/Assets/js/libs/html5shiv.js"></script>
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
                        <select name="lang" id="lang" class="form-control" title="{lang t="install|select_language"}">
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
    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            var $configForm = $('#config-form');
            if ($configForm.length > 0) {
                $configForm.data('changed', false);
                $configForm.find('input, select').change(function () {
                    $configForm.data('changed', true);
                });
            }

            // Sprachdropdown
            $('#languages').find(':submit').hide();
            $('#lang').change(function () {
                var reload = true;
                if ($configForm.length > 0 && $configForm.data('changed') == true) {
                    reload = confirm('{lang t="install|form_change_warning"}');
                }

                if (reload === true) {
                    $('#languages').submit();
                }
            });
        });
    </script>
    </body>
    </html>
{/if}
