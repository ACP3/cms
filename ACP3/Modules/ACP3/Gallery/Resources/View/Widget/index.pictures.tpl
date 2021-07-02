<ul class="gallery-pictures-widget list-unstyled">
    {foreach $gallery.pictures as $picture}
        <li>
            <a href="{$picture.uri_picture}"
               {if !empty($picture.title)}title="{$picture.title}"{/if}>
                <img src="{$picture.uri_thumb}"
                     alt="{$picture.title}"
                     loading="lazy"
                     class="img-thumbnail">
            </a>
        </li>
    {/foreach}
</ul>
