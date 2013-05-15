<!DOCTYPE html>
<html lang="{$LANG}" dir="{$LANG_DIRECTION}">
<head>
<title>{$TITLE} | {$PAGE_TITLE}</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="{$ROOT_DIR}libraries/bootstrap/css/bootstrap.css">
<link rel="stylesheet" type="text/css" href="{$ROOT_DIR}libraries/bootstrap/css/bootstrap-responsive.css">
<link rel="stylesheet" type="text/css" href="{$INSTALLER_ROOT_DIR}design/style.css">
<script type="text/javascript" src="{$ROOT_DIR}libraries/js/jquery.min.js"></script>
<script type="text/javascript" src="{$ROOT_DIR}libraries/bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript">
$(document).ready(function($) {
	if ($('#config-form').length > 0) {
		$('#config-form').data('changed', false);
		$('#config-form input, #config-form select').change(function() {
			$('#config-form').data('changed', true);
		});
	}

	// Sprachdropdown
	$('#languages :submit').hide();
	$('#lang').change(function() {
		var reload = true;
		if ($('#config-form').length > 0 &&
			$('#config-form').data('changed') == true) {
			reload = confirm('{lang t="form_change_warning"}');
		}
		
		if (reload == true)
			$('#languages').submit();
	});
});
</script>
<!--[if lt IE 9]>
<script src="{$ROOT_DIR}libraries/js/html5shiv.js"></script>
<![endif]-->
</head>

<body>
	<div class="container">
		<h1 id="logo" class="visible-desktop">{$PAGE_TITLE}</h1>
		<div class="navbar">
			<div class="navbar-inner">
				<a href="{$ROOT_DIR}" class="brand hidden-desktop">{$PAGE_TITLE}</a>
				<div class="container">
					<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</a>
					<div class="nav-collapse">
{if isset($navbar)}
						<ul class="nav">
{foreach $navbar as $key => $value}
							<li{if $value.active === true} class="active"{/if}><a href="#">{$value.lang}</a></li>
{/foreach}
						</ul>
{/if}
						<form action="{$REQUEST_URI}" method="post" id="languages" class="navbar-form pull-right">
							<select name="lang" id="lang">
{foreach $LANGUAGES as $row}
								<option value="{$row.language}"{$row.selected}>{$row.name}</option>
{/foreach}
							</select>
							<input type="submit" name="languages" value="{lang t="submit"}" class="btn">
						</form>
					</div>
				</div>
			</div>
		</div>
		<main role="main" id="content">
			<h1>{$TITLE}</h1>
			{$CONTENT}
		</main>
	</div>
</body>
</html>