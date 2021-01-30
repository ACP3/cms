<!DOCTYPE html>
<html lang="{$LANG}" dir="{$LANG_DIRECTION}">
<head>
    <meta charset="UTF-8">
    <title>{$TITLE} | {$PAGE_TITLE}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- STYLESHEETS -->
    {include_stylesheet module="system" file="style"}
    <!--[if lt IE 9]>
        {include_js module="system" file="html5shiv"}
    <![endif]-->
</head>

<body>
<div class="container">
    <div id="logo" class="text-center hidden-xs">
        <img src="{image file="logo.png"}"
             srcset="{image file="logo.png"} 1x, {image file="logo@2x.png"} 2x"
             alt="{$PAGE_TITLE}">
    </div>
    <nav id="main-navigation" class="navbar navbar-default{if empty($navbar)} visible-xs{/if}">
        <div class="navbar-header">
            {if !empty($navbar)}
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse">
                    <span class="sr-only">{lang t="installer|toggle_navigation"}</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            {/if}
            <span class="navbar-brand hidden-sm hidden-md hidden-lg">
                <img src="{image file="logo.png"}"
                     srcset="{image file="logo.png"} 1x, {image file="logo@2x.png"} 2x"
                     alt="{$PAGE_TITLE}">
            </span>
        </div>
        {if !empty($navbar)}
            <div id="navbar-collapse" class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    {foreach $navbar as $key => $value}
                        <li {if $value->isActive() === true} class="active"{elseif $value->isComplete() === true} class="complete"{/if}>
                            <a href="#">{$value->getTitle()}</a>
                        </li>
                    {/foreach}
                </ul>
            </div>
        {/if}
    </nav>
    <main id="content">
        <h1 class="h2">{$TITLE}</h1>
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
                            title="{lang t="installer|select_language"}"
                            data-change-language-warning="{lang t="installer|form_change_warning"}">
                        {foreach $LANGUAGES as $row}
                            <option value="{$row.value}"{$row.selected}>{$row.lang}</option>
                        {/foreach}
                    </select>
                    <button type="submit" name="languages" class="btn btn-primary btn-sm">
                        {lang t="installer|submit"}
                    </button>
                </form>
                {javascripts}
                    {include_js file="language-switcher"}
                {/javascripts}
            </div>
        </div>
    </footer>
</div>
<!-- JAVASCRIPTS -->
</body>
</html>
