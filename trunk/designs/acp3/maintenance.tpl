<!DOCTYPE html>
<html lang="{$LANG}">
<head>
<title>{$PAGE_TITLE}</title>
<meta charset="UTF-8">
<style type="text/css">
/* Allgemeines */
* {
	margin: 0;
	padding: 0;
}
html, body {
	line-height: 1.166;
}
body {
	background: #fff;
	font: 62.5% 'Lucida Grande', 'Trebuchet MS', Arial, sans-serif;
	color: #333;
}
#maintenance {
	width: 70%;
	height: 40px;
	margin: -20px auto;
	padding: 0 20px;
	background: #ffc;
	border: 1px dotted #f00;
	font-weight: bold;
	font-size: 1.3em;
	line-height: 40px;
	text-align: center;
	position: absolute;
	top: 50%;
	left: 15%;
}
</style>
</head>

<body>
	<div id="maintenance">
		{$CONTENT}
	</div>
</body>
</html>