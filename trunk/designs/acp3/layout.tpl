<!DOCTYPE html>
<html lang="{$LANG}">
<head>
<title>{$PAGE_TITLE} :: {$TITLE}</title>
{$META}
<link rel="stylesheet" type="text/css" href="{$MIN_STYLESHEET}">
<script type="text/javascript" src="{$MIN_JAVASCRIPT}"></script>
<link rel="alternate" type="application/rss+xml" href="{uri args="feeds/list/feed_news"}" title="{$PAGE_TITLE} - {lang t="news|news"}">
<link rel="alternate" type="application/rss+xml" href="{uri args="feeds/list/feed_files"}" title="{$PAGE_TITLE} - {lang t="files|files"}">
<!--[if lt IE 9]>
<script src="{$DESIGN_PATH}js/html5shiv.js"></script>
<![endif]-->
</head>

<body>
	<div class="container-fluid">
		<h1 id="logo">{$PAGE_TITLE}</h1>
		<div class="navbar navbar-inverse">
			<div class="navbar-inner">
				{navbar block="main"}
			</div>
		</div>
		<div class="row-fluid">
			<div class="span2">
				<div class="well" style="padding:8px">
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
				<div class="well" style="padding:8px">
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