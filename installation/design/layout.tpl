<!DOCTYPE html>
<html lang="{$LANG}" dir="{$LANG_DIRECTION}">
<head>
    <title>{$TITLE} | {$PAGE_TITLE}</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="{$ROOT_DIR}libraries/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="{$INSTALLER_ROOT_DIR}design/style.css">
    <script type="text/javascript" src="{$ROOT_DIR}libraries/js/jquery.min.js"></script>
    <script type="text/javascript" src="{$ROOT_DIR}libraries/bootstrap/js/bootstrap.min.js"></script>
    <!--[if lt IE 9]>
    <script src="{$ROOT_DIR}libraries/js/html5shiv.js"></script><![endif]-->
</head>

<body>
<div class="container">
    <h1 id="logo" class="visible-lg">{$PAGE_TITLE}</h1>
    <nav class="navbar navbar-default" role="navigation">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                <span class="sr-only">{lang t="toggle_navigation"}</span> <span class="glyphicon glyphicon-bar"></span>
                <span class="glyphicon glyphicon-bar"></span> <span class="glyphicon glyphicon-bar"></span>
            </button>
            <a href="{$ROOT_DIR}" class="navbar-brand hidden-lg">{$PAGE_TITLE}</a>
        </div>
        <div class="collapse navbar-collapse navbar-ex1-collapse">
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
                <input type="submit" name="languages" value="{lang t="submit"}" class="btn btn-primary">
            </form>
        </div>
    </nav>
    <main role="main" id="content">
        <h1>{$TITLE}</h1>
        {$CONTENT}
    </main>
</div>
<script type="text/javascript">
    $(document).ready(function ($) {
        if ($('#config-form').length > 0) {
            $('#config-form').data('changed', false);
            $('#config-form input, #config-form select').change(function () {
                $('#config-form').data('changed', true);
            });
        }

        // Sprachdropdown
        $('#languages :submit').hide();
        $('#lang').change(function () {
            var reload = true;
            if ($('#config-form').length > 0 &&
                    $('#config-form').data('changed') == true) {
                reload = confirm('{lang t="form_change_warning"}');
            }

            if (reload === true) {
                $('#languages').submit();
            }
        });
    });
</script>
</body>
</html>