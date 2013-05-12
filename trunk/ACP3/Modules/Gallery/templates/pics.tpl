{if isset($pictures)}
{if $overlay == 1}
{js_libraries enable="fancybox"}
<script type="text/javascript">
$(document).ready(function() {
	$(".thumbnails li a").fancybox({
		type: 'image',
		padding: 0,
		nextClick: true,
		arrows: true,
		loop: true
	});
});
</script>
<ul class="thumbnails">
{foreach $pictures as $row}
	<li>
		<a href="{$row.uri}" class="thumbnail" data-fancybox-group="gallery"{if !empty($row.description)} title="{$row.description}"{/if}>
			<img src="{uri args="gallery/image/id_`$row.id`/action_thumb"}" alt="" width="{$row.width}" height="{$row.height}">
		</a>
	</li>
{/foreach}
</ul>
{else}
<ul class="thumbnails">
{foreach $pictures as $row}
	<li>
		<a href="{$row.uri}" class="thumbnail">
			<img src="{uri args="gallery/image/id_`$row.id`/action_thumb"}" alt="" width="{$row.width}" height="{$row.height}">
		</a>
	</li>
{/foreach}
</ul>
{/if}
{else}
<div class="alert align-center">
	<strong>{lang t="gallery|no_pictures"}</strong>
</div>
{/if}