<!DOCTYPE html>
<html lang="{$LANG}" dir="{$LANG_DIRECTION}">
<head>
    <title>{$HEAD_TITLE}</title>
    {$META}
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="{$MIN_STYLESHEET}">
    <script type="text/javascript" src="{$MIN_JAVASCRIPT}"></script>
    <link rel="alternate" type="application/rss+xml" href="{uri args="feeds/index/index/feed_news"}" title="{$PAGE_TITLE} - {lang t="news|news"}">
    <link rel="alternate" type="application/rss+xml" href="{uri args="feeds/index/index/feed_files"}" title="{$PAGE_TITLE} - {lang t="files|files"}">
    <!--[if lt IE 9]>
    <script src="{$ROOT_DIR}libraries/js/html5shiv.js"></script>
    <![endif]-->
</head>

<body>
<div class="container-fluid">
    <h1 id="logo" class="hidden-xs"><a href="{$ROOT_DIR}">{$PAGE_TITLE}</a></h1>
    <nav class="navbar navbar-default" role="navigation">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse">
                    <span class="sr-only">{lang t="system|toggle_navigation"}</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a href="{$ROOT_DIR}" class="navbar-brand hidden-lg">{$PAGE_TITLE}</a>
            </div>
            <div id="navbar-collapse" class="collapse navbar-collapse">
                {navbar block="main"}
                {load_module module="sidebar/search"}
            </div>
        </div>
    </nav>
    <div class="row">
        <div class="col-sm-3 col-md-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">{lang t="system|navigation"}</h3>
                </div>
                {navbar block="sidebar" class="list-group" classLink="list-group-item" dropdownItemClass="dropdown" itemTag="" dropdownWrapperTag="div" tag="div"}
            </div>
            {load_module module="sidebar/users/index/user_menu"}
            {load_module module="sidebar/users/index/login"}
        </div>
        <main role="main" id="content" class="col-sm-9 col-md-8">
            <div id="breadcrumb">
                {$BREADCRUMB}
            </div>
            <h2>{$TITLE}</h2>
            {$CONTENT}
        </main>
        <div class="col-md-2 hidden-xs hidden-sm">
            {load_module module="sidebar/news"}
            {load_module module="sidebar/newsletter"}
            {load_module module="sidebar/files"}
            {load_module module="sidebar/gallery"}
            {load_module module="sidebar/polls"}
        </div>
    </div>
</div>
</body>
</html>