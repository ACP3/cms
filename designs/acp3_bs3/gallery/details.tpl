<article>
	<section class="picture">
{if isset($picture_next)}
		<a href="{uri args="gallery/details/id_`$picture_next`"}">
			<img src="{uri args="gallery/image/id_`$picture.id`/action_normal"}" width="{$picture.width}" height="{$picture.height}" alt="" class="img-polaroid">
		</a>
{else}
		<img src="{uri args="gallery/image/id_`$picture.id`/action_normal"}" width="{$picture.width}" height="{$picture.height}" alt="" class="img-polaroid">
{/if}
		<div class="description">
			{$picture.description}
		</div>
		<footer>
			<ul class="pager">
{if isset($picture_back)}
				<li class="previous"><a href="{uri args="gallery/details/id_`$picture_back`"}" rel="prev">&laquo; {lang t="gallery|previous_image"}</a></li>
{/if}
				<li><a href="{uri args="gallery/pics/id_`$picture.gallery_id`"}">{lang t="gallery|picture_index"}</a></li>
{if isset($picture_next)}
				<li class="next"><a href="{uri args="gallery/details/id_`$picture_next`"}" rel="next">{lang t="gallery|next_image"} &raquo;</a></li>
{/if}
			</ul>
		</footer>
	</section>
{if isset($comments)}
{$comments}
{/if}
</article>