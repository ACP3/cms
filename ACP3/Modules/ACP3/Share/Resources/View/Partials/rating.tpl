<span class="rating">
    {$rating_rounded=$rating.average_rating|round:0}
    {for $i=5 to 1 step -1}
        <a href="{uri args="share/index/rate/stars_`$i`/id_`$rating.share_id`"}"
           data-ajax-form="true"
           data-ajax-form-target-element="#rating-wrapper"
           data-ajax-form-loading-overlay="false"
           title="{lang t="share|rate_with_x_stars" args=['%stars%' => $i]}"
           class="rating__star{if $i == $rating_rounded} rating__star_active{/if}"></a>
    {/for}
</span>
<div class="rating-summary">
    {if $rating.total_ratings > 0}
        {$rating.average_rating|string_format:"%.2f"} / 5
        ({lang t="share|total_x_ratings" args=['%ratings%' => $rating.total_ratings]})
    {else}
        {lang t="share|no_ratings_yet"}
    {/if}
</div>
