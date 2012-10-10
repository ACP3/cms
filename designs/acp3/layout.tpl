<!DOCTYPE html>
<html lang="{$LANG}" dir="{$LANG_DIRECTION}">
<head>
<title>{$HEAD_TITLE}</title>
{$META}
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="{$MIN_STYLESHEET}">
<script type="text/javascript" src="{$MIN_JAVASCRIPT}"></script>
<link rel="alternate" type="application/rss+xml" href="{uri args="feeds/list/feed_news"}" title="{$PAGE_TITLE} - {lang t="news|news"}">
<link rel="alternate" type="application/rss+xml" href="{uri args="feeds/list/feed_files"}" title="{$PAGE_TITLE} - {lang t="files|files"}">
<!--[if lt IE 9]>
<script src="{$ROOT_DIR}libraries/js/html5shiv.js"></script>
<![endif]-->
</head>

<body>
	<div class="container-fluid">
		<h1 id="logo" class="visible-desktop">{$PAGE_TITLE}</h1>
		<div class="navbar navbar-inverse">
			<div class="navbar-inner">
				<a href="{$ROOT_DIR}" class="brand hidden-desktop">{$PAGE_TITLE}</a>
				<div class="container">
					<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</a>
					<div class="nav-collapse">
						{navbar block="main"}
						{load_module module="search|sidebar"}
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span2">
				<div class="well well-small">
					{navbar block="sidebar" class="nav-list"}
					{load_module module="users|sidebar"}
				</div>
			</div>
			<div class="span8">
				<div id="breadcrumb">
					{$BREADCRUMB}
				</div>
				<h2>{$TITLE}</h2>
				{$CONTENT}
			</div>
			<div class="span2">
				<div class="well well-small">
					{load_module module="news|sidebar"}
					{load_module module="files|sidebar"}
					{load_module module="gallery|sidebar"}
					{load_module module="polls|sidebar"}
				</div>
			</div>
		</div>
	</div>
</body>
</html>