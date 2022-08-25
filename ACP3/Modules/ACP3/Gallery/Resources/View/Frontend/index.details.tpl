{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    <article>
        <section class="text-center mb-2">
            {if !empty($picture_next)}
                <a href="{uri args="gallery/index/details/id_`$picture_next`"}">
                    <img src="{$picture.file}"
                         width="{$picture.width}"
                         height="{$picture.height}"
                         alt="{$picture.description|strip_tags|trim}"
                         class="img-thumbnail mb-2">
                </a>
            {else}
                <img src="{$picture.file}"
                     width="{$picture.width}"
                     height="{$picture.height}"
                     alt="{$picture.description|strip_tags|trim}"
                     class="img-thumbnail mb-2">
            {/if}
            <div class="w-75 mx-auto">
                {$picture.description|rewrite_uri}
            </div>
            {event name="share.layout.add_social_sharing"}
            <footer>
                {include file="asset:System/Partials/pager.tpl" pager=['showEmpty' => true, 'index' => {uri args="gallery/index/pics/id_`$picture.gallery_id`"}, 'indexLabel' => {lang t="gallery|picture_index"}, 'next' => (!empty($picture_next)) ? {uri args="gallery/index/details/id_`$picture_next`"} : '', 'nextLabel' => {lang t="gallery|next_image"}, 'previous' => (!empty($picture_previous)) ? {uri args="gallery/index/details/id_`$picture_previous`"} : '', 'previousLabel' => {lang t="gallery|previous_image"}]}
            </footer>
        </section>
        {event name="gallery.layout.details_after" gallery_picture=$picture}
    </article>
{/block}
