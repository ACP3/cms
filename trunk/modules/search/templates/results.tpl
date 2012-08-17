{if isset($results_mods)}
<ol>
{foreach $results_mods as $result_mod}
{foreach $result_mod.results as $row}
	<li class="dataset-box">
		<div class="header">
			{$result_mod.title} - <a href="{$row.hyperlink}">{$row.headline}</a>
		</div>
{if !empty($row.text)}
		<div class="content">
			{$row.text}
		</div>
{/if}
	</li>
{/foreach}
{/foreach}
</ol>
{else}
<div class="alert alert-block align-center">
	<h5>{$no_search_results}</h5>
</div>
{/if}