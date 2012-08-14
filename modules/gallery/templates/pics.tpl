{if isset($pictures)}
<div class="pictures">
{if $overlay == 1}
<script type="text/javascript">
$(document).ready(function() {
	$(".pictures a").fancybox({
		type: 'image',
		padding: 0,
		nextClick: true,
		arrows: true,
		loop: true
	});
});
</script>
{foreach $pictures as $row}
	<a href="{$row.uri}"  data-fancybox-group="gallery"{if !empty($row.description)} title="{$row.description}"{/if}><img src="{uri args="gallery/image/id_`$row.id`/action_thumb"}" alt="" width="{$row.width}" height="{$row.height}"></a>
{/foreach}
{else}
{foreach $pictures as $row}
	<a href="{$row.uri}"><img src="{uri args="gallery/image/id_`$row.id`/action_thumb"}" alt="" width="{$row.width}" height="{$row.height}"></a>
{/foreach}
{/if}
</div>
{else}
<div class="alert alert-block align-center">
	<h5>{lang t="gallery|no_pictures"}</h5>
</div>
{/if}