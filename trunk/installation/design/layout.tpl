<!DOCTYPE html>
<html lang="{$LANG}">
<head>
<title>ACP3 {lang t="installation"} :: {$TITLE}</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="{$ROOT_DIR}libraries/bootstrap/css/bootstrap.css">
<link rel="stylesheet" type="text/css" href="{$ROOT_DIR}libraries/bootstrap/css/bootstrap-responsive.css">
<link rel="stylesheet" type="text/css" href="{$INSTALLER_DIR}design/style.css">
<script type="text/javascript" src="{$ROOT_DIR}libraries/js/jquery.min.js"></script>
<script type="text/javascript" src="{$ROOT_DIR}libraries/bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript">
$(document).ready(function($) {
	// Sprachdropdown
	$('#languages :submit').hide();
	$('#lang').change(function() {
		$('#languages').submit();
	});
});
</script>
<!--[if lt IE 9]>
<script src="{$ROOT_DIR}libraries/js/html5shiv.js"></script>
<![endif]-->
</head>

<body>
	<div class="container-fluid">
		<h1 id="logo">ACP3</h1>
		<div class="row-fluid">
			<div class="span3 well well-small">
{if isset($PAGES)}
				<ul class="nav nav-list">
					<li class="nav-header">Navigation</li>
{foreach from=$PAGES item=row}
					<li{$row.selected}><a>{lang t="`$row.file`"}</a></li>
{/foreach}
					<li class="divider"></li>
				</ul>
{/if}
				<form action="{$REQUEST_URI}" method="post" id="languages" class="form-inline">
					<select name="lang" id="lang" class="span12">
{foreach from=$LANGUAGES item=row}
						<option value="{$row.language}"{$row.selected}>{$row.name}</option>
{/foreach}
					</select>
					<input type="submit" name="languages" value="{lang t="submit"}" class="btn">
				</form>
			</div>
			<div class="span9">
				<h2>{$TITLE}</h2>
				{$CONTENT}
			</div>
		</div>
	</div>
</body>
</html>