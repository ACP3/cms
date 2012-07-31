<div class="picture">
{if isset($picture_next)}
	<a href="{uri args="gallery/details/id_`$picture_next.id`"}"><img src="{uri args="gallery/image/id_`$picture.id`/action_normal"}" width="{$picture.width}" height="{$picture.height}" alt=""></a>
{else}
	<img src="{uri args="gallery/image/id_`$picture.id`/action_normal"}" width="{$picture.width}" height="{$picture.height}" alt="">
{/if}
	<div class="description">
		{$picture.description}
	</div>
	<div id="pagination">
{if isset($picture_back)}
		<a href="{uri args="gallery/details/id_`$picture_back.id`"}" rel="prev" class="previous">&laquo; {lang t="gallery|previous_image"}</a>
{/if}
		<a href="{uri args="gallery/pics/id_`$picture.gallery_id`"}" class="no-border">{lang t="gallery|picture_index"}</a>
{if isset($picture_next)}
		<a href="{uri args="gallery/details/id_`$picture_next.id`"}" rel="next" class="next">{lang t="gallery|next_image"} &raquo;</a>
{/if}
	</div>
</div>
{if isset($comments)}
{$comments}
{/if}