<!DOCTYPE html>
<html lang="{$LANG}">
<head>
<title>{$PAGE_TITLE} :: {$TITLE}</title>
{$META}
<link rel="stylesheet" type="text/css" href="{$MIN_STYLESHEET}">
<script type="text/javascript" src="{$MIN_JAVASCRIPT}"></script>
<link rel="alternate" type="application/rss+xml" href="{uri args="feeds/list/feed_news"}" title="{$PAGE_TITLE} - {lang t="news|news"}">
<link rel="alternate" type="application/rss+xml" href="{uri args="feeds/list/feed_files"}" title="{$PAGE_TITLE} - {lang t="files|files"}">
</head>

<body>
	<div class="container-fluid">
		<div class="navbar">
			<div class="navbar-inner">
				<div class="container">
					<a class="brand" href="{$ROOT_DIR}">{$PAGE_TITLE}</a>
					{navbar block="main"}
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span2 well" style="padding:8px">
				{navbar block="sidebar" class="nav-list"}
				{load_module module="users|sidebar"}
			</div>
			<div class="span8">
				<div id="breadcrumb">
					{$BREADCRUMB}
				</div>
				<h2>{$TITLE}</h2>
				{$CONTENT}
			</div>
			<div class="span2 well" style="padding:8px">
				{load_module module="news|sidebar"}
				{load_module module="files|sidebar"}
				{load_module module="gallery|sidebar"}
				{load_module module="polls|sidebar"}
			</div>
		</div>
	</div>
</body>
</html>