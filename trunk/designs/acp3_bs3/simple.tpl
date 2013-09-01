<!DOCTYPE html>
<html lang="{$LANG}">
<head>
<title>{$HEAD_TITLE}</title>
{$META}
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="{$MIN_STYLESHEET}">
<script type="text/javascript" src="{$MIN_JAVASCRIPT}"></script>
<!--[if lt IE 9]>
<script src="{$ROOT_DIR}libraries/js/html5shiv.js"></script>
<![endif]-->
</head>

<body>
<div id="content" class="container-fluid">
	<h1 id="page-title">{$TITLE}</h1>
	{$CONTENT}
</div>
</body>
</html>