{$page.toc}
{$page.text}
{if !empty($page.next) || !empty($page.previous)}
<div id="pagination">
	{if !empty($page.previous)}<a href="{$page.previous}" rel="prev" class="previous">&laquo; {lang t="common|previous_page"}</a>{/if}
	{if !empty($page.next)}<a href="{$page.next}" rel="next" class="next">{lang t="common|next_page"} &raquo;</a>{/if}
</div>
{/if}