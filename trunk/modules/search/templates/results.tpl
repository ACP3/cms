{if isset($results_mods)}
<div class="tabbable">
	<ul class="nav nav-tabs">
{$i=0}
{foreach $results_mods as $module => $values}
		<li{if $i === 0} class="active"{/if}><a href="#tab-{$values.dir}" data-toggle="tab">{$module}</a></li>
{$i=$i+1}
{/foreach}
	</ul>
	<div class="tab-content">
{$i=0}
{foreach $results_mods as $module => $values}
		<div id="tab-{$values.dir}" class="tab-pane{if $i === 0} active{/if}">
			<ol>
{foreach $values.results as $row}
				<li class="dataset-box">
					<div class="header">
						<a href="{$row.hyperlink}">{$row.title}</a>
					</div>
{if !empty($row.text)}
					<div class="content">
						{$row.text}
					</div>
{/if}
				</li>
{/foreach}
			</ol>
		</div>
{$i=$i+1}
{/foreach}
	</div>
</div>
{else}
<div class="alert align-center">
	<strong>{$no_search_results}</strong>
</div>
{/if}