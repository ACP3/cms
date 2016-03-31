{extends file="asset:layout.tpl"}

{block CONTENT}
    <article>
        <section class="picture">
            {if isset($picture_next)}
                <a href="{uri args="gallery/index/details/id_`$picture_next`"}">
                    <img src="{uri args="gallery/index/image/id_`$picture.id`/action_normal"}" width="{$picture.width}" height="{$picture.height}" alt="" class="img-thumbnail">
                </a>
            {else}
                <img src="{uri args="gallery/index/image/id_`$picture.id`/action_normal"}" width="{$picture.width}" height="{$picture.height}" alt="" class="img-thumbnail">
            {/if}
            <div class="description">
                {$picture.description}
            </div>
            <footer>
                <ul class="pager">
                    {if !empty($picture_previous)}
                        <li class="previous">
                            <a href="{uri args="gallery/index/details/id_`$picture_previous`"}" rel="prev">
                                &laquo;
                                {lang t="gallery|previous_image"}
                            </a>
                        </li>
                    {/if}
                    <li>
                        <a href="{uri args="gallery/index/pics/id_`$picture.gallery_id`"}">
                            {lang t="gallery|picture_index"}
                        </a>
                    </li>
                    {if !empty($picture_next)}
                        <li class="next">
                            <a href="{uri args="gallery/index/details/id_`$picture_next`"}" rel="next">
                                {lang t="gallery|next_image"}
                                &raquo;
                            </a>
                        </li>
                    {/if}
                </ul>
            </footer>
        </section>
        {if $comments_allowed === true}
            <section id="comments">
                {load_module module="frontend/comments" args=['gallery', $picture.id]}
                {load_module module="frontend/comments/index/create" args=['gallery', $picture.id]}
            </section>
        {/if}
    </article>
{/block}
