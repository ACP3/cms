<!DOCTYPE html>
<html lang="{$LANG}">
<head>
<title>ACP3 {lang t="installation|installation"} :: {$title}</title>
<meta charset="UTF-8">
<script type="text/javascript" src="{$ROOT_DIR}../designs/acp3/jquery/jquery.min.js"></script>
<script type="text/javascript" src="{$ROOT_DIR}../designs/acp3/jquery/jquery.cookie.js"></script>
<script type="text/javascript" src="{$ROOT_DIR}../designs/acp3/jquery/jquery.ui.min.js"></script>
<script type="text/javascript" src="{$ROOT_DIR}design/script.js"></script>
<link rel="stylesheet" type="text/css" href="{$ROOT_DIR}design/style.css">
<link rel="stylesheet" type="text/css" href="{$ROOT_DIR}../designs/acp3/jquery/jquery-ui.css">
<!--[if lte IE 6]>
<link rel="stylesheet" type="text/css" href="{$ROOT_DIR}design/ie.css">
<![endif]-->
</head>

<body>
	<div id="box">
		<div id="header">
			<h1 id="page-title">ACP3 {lang t="installation|installation"}</h1>
			<ul>
{foreach from=$pages item=row}
				<li><span{$row.selected}>{$row.title}</span></li>
{/foreach}
			</ul>
			<form action="{$REQUEST_URI}" method="post" id="languages">
				<label for="lang">
					{lang t="installation|language"}
					<select name="lang" id="lang">
{foreach from=$languages item=row}
						<option value="{$row.dir}"{$row.selected}>{$row.name}</option>
{/foreach}
					</select>
				</label>
				<input type="submit" name="submit" value="{lang t="common|submit"}" class="form">
			</form>
		</div>
		<div id="content">
			<h1>{$title}</h1>
			{$content}
		</div>
		<div id="footer"></div>
	</div>
</body>
</html>