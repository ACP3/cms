{if $IS_AJAX === true}
    {include file="asset:ajax.tpl"}
{else}
    <!DOCTYPE html>
    <html lang="{$LANG}" dir="{$LANG_DIRECTION}">
    <head>
        <title>{$TITLE} | {$PAGE_TITLE}</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="{$ROOT_DIR}ACP3/Modules/System/Resources/Assets/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="{$INSTALLER_ROOT_DIR}design/style.css">
        <script type="text/javascript" src="{$ROOT_DIR}ACP3/Modules/System/Resources/Assets/js/libs/jquery.min.js"></script>
        <script type="text/javascript" src="{$ROOT_DIR}ACP3/Modules/System/Resources/Assets/js/libs/bootstrap.min.js"></script>
        <!--[if lt IE 9]>
        <script src="{$ROOT_DIR}ACP3/Modules/System/Resources/Assets/js/libs/html5shiv.js"></script>
        <![endif]-->
    </head>

    <body>
    <div class="container">
        <h1 id="logo" class="hidden-xs">{$PAGE_TITLE}</h1>
        <nav class="navbar navbar-default" role="navigation">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse">
                    <span class="sr-only">{lang t="install|toggle_navigation"}</span> <span class="icon-bar"></span>
                    <span class="icon-bar"></span> <span class="icon-bar"></span>
                </button>
                <a href="{$ROOT_DIR}" class="navbar-brand hidden-sm hidden-md hidden-lg">{$PAGE_TITLE}</a>
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
                        <select name="lang" id="lang" class="form-control">
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
            {block CONTENT}
                {$CONTENT}
            {/block}
        </main>
    </div>
    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            var $configForm = $('#config-form')
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