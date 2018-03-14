<form action="{uri args="share/index/rate/id_`$rating.share_id`"}"
      method="post"
      accept-charset="UTF-8"
      data-ajax-form="true"
      data-ajax-form-target-element="#rating-wrapper"
      data-ajax-form-loading-overlay="false">
    <div class="rating">
        {if isset($rating.already_rated) && $rating.already_rated}
            <small class="rating__already-rated">{lang t="share|already_rated"}</small>
        {/if}
        {$rating_rounded=$rating.average_rating|round:0|default:0}
        {for $i=5 to 1 step -1}
            <label for="rating-star-{$i}"
                   title="{lang t="share|rate_with_x_stars" args=['%stars%' => $i]}"
                   class="rating__star{if $i == $rating_rounded} rating__star_active{/if}">
                <input type="radio" id="rating-star-{$i}" name="stars" value="{$i}" class="hidden" required>
            </label>
        {/for}
    </div>
</form>
{if isset($rating.total_ratings) && $rating.total_ratings > 0}
    <div class="rating-summary"
         itemprop="aggregateRating"
         itemscope itemtype="http://schema.org/AggregateRating">
            <span itemprop="ratingValue">{$rating.average_rating|string_format:"%.2f"}</span> / 5
        {if $rating.total_ratings > 1}
            ({lang t="share|total_x_ratings" args=['%ratings%' => $rating.total_ratings]})
        {else}
            ({lang t="share|total_1_rating"})
        {/if}
    </div>
{else}
    <div class="rating-summary">
        {lang t="share|no_ratings_yet"}
    </div>
{/if}
