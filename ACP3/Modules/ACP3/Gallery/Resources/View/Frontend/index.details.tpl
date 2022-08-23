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
                <nav aria-label="{lang t="system|pagination"}">
                    <ul class="pagination justify-content-center">
                        {if !empty($picture_previous)}
                            <li class="page-item">
                                <a href="{uri args="gallery/index/details/id_`$picture_previous`"}" class="page-link" rel="prev">
                                    &laquo;
                                    {lang t="gallery|previous_image"}
                                </a>
                            </li>
                        {else}
                            <li class="page-item disabled">
                                <span class="page-link">
                                    &laquo;
                                    {lang t="gallery|previous_image"}
                                </span>
                            </li>
                        {/if}
                        <li class="page-item">
                            <a href="{uri args="gallery/index/pics/id_`$picture.gallery_id`"}" class="page-link">
                                {lang t="gallery|picture_index"}
                            </a>
                        </li>
                        {if !empty($picture_next)}
                            <li class="page-item">
                                <a href="{uri args="gallery/index/details/id_`$picture_next`"}" class="page-link" rel="next">
                                    {lang t="gallery|next_image"}
                                    &raquo;
                                </a>
                            </li>
                        {else}
                            <li class="page-item disabled">
                                <span class="page-link">
                                    {lang t="gallery|next_image"}
                                    &raquo;
                                </span>
                            </li>
                        {/if}
                    </ul>
                </nav>
            </footer>
        </section>
        {event name="gallery.layout.details_after" gallery_picture=$picture}
    </article>
{/block}
