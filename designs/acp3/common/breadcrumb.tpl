<a href="{$ROOT_DIR}">{lang t="common|home"}</a> &raquo;
{if isset($breadcrumb)}
{foreach $breadcrumb as $row}
{if $row.last === false && !empty($row.uri)}
<a href="{$row.uri}">{$row.title}</a> &raquo;
{elseif $row.last === true}
<span>{$row.title}</span>
{/if}
{/foreach}
{/if}