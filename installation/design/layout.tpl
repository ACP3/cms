<!DOCTYPE html>
<html lang="{$LANG}">
<head>
<title>ACP3 {lang t="installation|installation"} :: {$TITLE}</title>
<meta charset="UTF-8">
<script type="text/javascript" src="{$ROOT_DIR}../designs/acp3/js/jquery.min.js"></script>
<script type="text/javascript" src="{$ROOT_DIR}../designs/acp3/js/bootstrap.min.js"></script>
<script type="text/javascript" src="{$ROOT_DIR}design/script.js"></script>
<link rel="stylesheet" type="text/css" href="{$ROOT_DIR}../designs/acp3/css/bootstrap.css">
<link rel="stylesheet" type="text/css" href="{$ROOT_DIR}../designs/acp3/css/bootstrap-responsive.css">
<link rel="stylesheet" type="text/css" href="{$ROOT_DIR}design/style.css">
</head>

<body>
	<div class="container-fluid">
		<h1 id="logo">ACP3</h1>
		<div class="row-fluid">
			<div class="span2 well" style="padding:8px">
				<ul class="nav nav-list">
					<li class="nav-header">Navigation</li>
{foreach from=$PAGES item=row}
					<li{$row.selected}><a>{$row.title}</a></li>
{/foreach}
					<li class="divider"></li>
				</ul>
				<form action="{$REQUEST_URI}" method="post" id="languages" class="form-inline">
					<select name="lang" id="lang" class="span12">
{foreach from=$LANGUAGES item=row}
						<option value="{$row.dir}"{$row.selected}>{$row.name}</option>
{/foreach}
					</select>
					<input type="submit" name="languages" value="{lang t="common|submit"}" class="btn">
				</form>
			</div>
			<div class="span10">
				<h2>{$TITLE}</h2>
				{$CONTENT}
			</div>
		</div>
	</div>
</body>
</html>