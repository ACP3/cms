<ul class="breadcrumb">
	<li><a href="{$ROOT_DIR}">{lang t="common|home"}</a> <span class="divider">/</span></li>
{if isset($breadcrumb)}
{foreach $breadcrumb as $row}
{if $row.last === false && !empty($row.uri)}
	<li><a href="{$row.uri}">{$row.title}</a> <span class="divider">/</span></li>
{elseif $row.last === true}
	<li class="active">{$row.title}</li>
{/if}
{/foreach}
{/if}
</ul>