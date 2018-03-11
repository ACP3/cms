<span class="rating">
    {$rating_rounded=$sharing.rating_stats.average_rating|round:0}
    {for $i=5 to 1 step -1}
        <a href="#"
           title="{lang t="share|rate_with_x_stars" args=['%stars%' => $i]}"
           class="rating__star{if $i == $rating_rounded} rating__star_active{/if}"></a>
    {/for}
</span>
{if $sharing.rating_stats.total_ratings > 0}
    <div class="rating-summary">
        {$sharing.rating_stats.average_rating|round:2} / 5 ({lang t="share|total_x_ratings" args=['%ratings%' => $sharing.rating_stats.total_ratings]})
    </div>
{/if}
