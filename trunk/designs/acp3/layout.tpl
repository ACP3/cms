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
		<div id="box" class="container">
			<h1 id="logo" class="visible-lg"><a href="{$ROOT_DIR}">{$PAGE_TITLE}</a></h1>
			<nav class="navbar navbar-default" role="navigation">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
						<span class="sr-only">{lang t="system|toggle_navigation"}</span>
						<span class="glyphicon glyphicon-bar"></span>
						<span class="glyphicon glyphicon-bar"></span>
						<span class="glyphicon glyphicon-bar"></span>
					</button>
					<a href="{$ROOT_DIR}" class="navbar-brand hidden-lg">{$PAGE_TITLE}</a>
				</div>
				<div class="collapse navbar-collapse navbar-ex1-collapse">
					{navbar block="main"}
					{load_module module="search|sidebar"}
				</div>
			</nav>
			<div class="row">
				<div class="col-lg-2">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title">{lang t="system|navigation"}</h3>
						</div>
						<div class="panel-body">
							{navbar block="sidebar" class="list-group" classLink="list-group-item" dropdownItemClass="dropdown" itemTag="" dropdownWrapperTag="div" tag="div" inlineStyles="margin-bottom:0"}
						</div>
					</div>
					{load_module module="users|sidebar"}
				</div>
				<main role="main" class="col-lg-8">
					<div id="breadcrumb">
						{$BREADCRUMB}
					</div>
					<h2>{$TITLE}</h2>
					{$CONTENT}
				</main>
				<div class="col-lg-2">
					{load_module module="news|sidebar"}
					{load_module module="newsletter|sidebar"}
					{load_module module="files|sidebar"}
					{load_module module="gallery|sidebar"}
					{load_module module="polls|sidebar"}
				</div>
			</div>
		</div>
	</body>
</html>