<div class="picture">
	<img src="{uri args="gallery/image/id_`$picture.id`/action_normal"}" alt="">
	<div class="content">
		{$picture.description}
	</div>
	<div class="prev-next">
{if isset($picture_back)}
		<a href="{uri args="gallery/details/id_`$picture_back.id`" alias="1"}" class="prev"><img src="{uri args="gallery/image/id_`$picture_back.id`/action_thumb"}" alt=""></a>
{/if}
{if isset($picture_next)}
		<a href="{uri args="gallery/details/id_`$picture_next.id`" alias="1"}" class="next"><img src="{uri args="gallery/image/id_`$picture_next.id`/action_thumb"}" alt=""></a>
{/if}
		<div class="index">
			<a href="{uri args="gallery/pics/id_`$picture.gallery_id`" alias="1"}">{lang t="gallery|picture_index"}</a>
		</div>
	</div>
</div>
{if isset($comments)}
{$comments}
{/if}