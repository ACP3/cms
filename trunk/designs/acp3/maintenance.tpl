<!DOCTYPE html>
<html lang="{$LANG}">
<head>
<title>{$PAGE_TITLE}</title>
<meta charset="UTF-8">
<link rel="stylesheet" type="text/css" href="{$DESIGN_PATH}css/bootstrap.css">
<link rel="stylesheet" type="text/css" href="{$DESIGN_PATH}css/bootstrap-responsive.css">
<style type="text/css">
#maintenance {
	width: 70%;
	height: 40px;
	margin: -20px auto;
	padding: 0 20px;
	line-height: 40px;
	text-align: center;
	position: absolute;
	top: 50%;
	left: 15%;
}
</style>
</head>

<body>
	<div id="maintenance" class="alert">
		<strong>{$CONTENT}</strong>
	</div>
</body>
</html>