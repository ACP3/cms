{if isset($results_mods)}
{foreach $results_mods as $result_mod}
<h4>{$result_mod.title}</h4>
<ol>
{foreach $result_mod.results as $row}
	<li class="dataset-box">
		<div class="header">
			<a href="{$row.hyperlink}">{$row.headline}</a>
		</div>
{if !empty($row.text)}
		<div class="content">
			{$row.text}
		</div>
{/if}
	</li>
{/foreach}
</ol>
{/foreach}
{else}
<div class="alert alert-block align-center">
	<h5>{$no_search_results}</h5>
</div>
{/if}