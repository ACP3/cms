<!DOCTYPE html>
<html lang="{$LANG}" dir="{$LANG_DIRECTION}">
<head>
    <meta charset="UTF-8">
    <title>{site_and_page_title}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- STYLESHEETS -->
    {include_stylesheet module="system" file="style"}
    <!-- JAVASCRIPTS -->
</head>

<body>
<div class="container">
    <h1 class="d-none d-lg-block my-2 mx-3">
        <img src="{image file="logo.png" module="system"}"
             srcset="{image file="logo.png" module="system"} 1x, {image file="logo@2x.png" module="system"} 2x"
             alt="{site_title}">
    </h1>
    <nav id="main-navigation" class="navbar navbar-expand-lg navbar-light bg-light py-lg-0 mb-3{if empty($navbar)} d-none{/if}">
        <div class="container-fluid">
            <a class="navbar-brand d-lg-none" href="{$ROOT_DIR}">
                <img src="{image file="logo.png" module="system"}"
                     srcset="{image file="logo.png" module="system"} 1x, {image file="logo@2x.png" module="system"} 2x"
                     alt="{site_title}"
                     height="30">
            </a>
            {if !empty($navbar)}
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main-navigation-content" aria-controls="main-navigation-content" aria-expanded="false" aria-label="{lang t="system|toggle_navigation"}">
                    <span class="navbar-toggler-icon"></span>
                </button>
            {/if}
            {if !empty($navbar)}
                <div class="collapse navbar-collapse" id="main-navigation-content">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        {foreach $navbar as $key => $value}
                            <li class="nav-item{if $value->isComplete() === true} complete{/if}">
                                <a href="#" class="nav-link{if $value->isActive() === true} active{/if}">{$value->getTitle()}</a>
                            </li>
                        {/foreach}
                    </ul>
                </div>
            {/if}
        </div>
    </nav>
    <main id="content">
        <h2>{page_title}</h2>
        {block CONTENT}{/block}
    </main>
    <footer class="mb-2 row align-items-center">
        <div class="col-sm mb-2 mb-sm-0 text-center text-sm-start">
            &copy; ACP3 CMS
        </div>
        <div class="col-sm">
            <form action="{$REQUEST_URI}" method="post" id="languages" class="d-flex">
                <select name="lang"
                        id="lang"
                        class="form-select me-2"
                        title="{lang t="installer|select_language"}"
                        data-change-language-warning="{lang t="installer|form_change_warning"}">
                    {foreach $LANGUAGES as $row}
                        <option value="{$row.value}"{$row.selected}>{$row.lang}</option>
                    {/foreach}
                </select>
                <button type="submit" name="languages" class="btn btn-primary">
                    {lang t="installer|submit"}
                </button>
            </form>
            {javascripts}
                {include_js file="language-switcher" module="system"}
            {/javascripts}
        </div>
    </footer>
</div>
</body>
</html>
