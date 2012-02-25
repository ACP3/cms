<div class="picture">
{if isset($picture_next)}
	<a href="{uri args="gallery/details/id_`$picture_next.id`" alias="1"}"><img src="{uri args="gallery/image/id_`$picture.id`/action_normal"}" alt=""></a>
{else}
	<img src="{uri args="gallery/image/id_`$picture.id`/action_normal"}" alt="">
{/if}
	<div class="description">
		{$picture.description}
	</div>
	<div id="pagination">
{if isset($picture_back)}
		<a href="{uri args="gallery/details/id_`$picture_back.id`" alias="1"}" rel="prev" class="previous">&laquo; {lang t="gallery|previous_image"}</a>
{/if}
		<a href="{uri args="gallery/pics/id_`$picture.gallery_id`" alias="1"}">{lang t="gallery|picture_index"}</a>
{if isset($picture_next)}
		<a href="{uri args="gallery/details/id_`$picture_next.id`" alias="1"}" rel="next" class="next">{lang t="gallery|next_image"} &raquo;</a>
{/if}
	</div>
</div>
{if isset($comments)}
{$comments}
{/if}