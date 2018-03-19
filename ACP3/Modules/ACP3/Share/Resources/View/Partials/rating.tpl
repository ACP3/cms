<form action="{uri args="share/index/rate/id_`$rating.share_id`"}"
      method="post"
      accept-charset="UTF-8"
      data-ajax-form="true"
      data-ajax-form-target-element="#rating-wrapper"
      data-ajax-form-loading-overlay="false">
    <div class="rating">
        <div class="rating__average" style="width:{(($rating.average_rating|default:0) * 100 / 5)|round:0}%">
            <span class="rating__star"></span>
            <span class="rating__star"></span>
            <span class="rating__star"></span>
            <span class="rating__star"></span>
            <span class="rating__star"></span>
        </div>
        {for $i=5 to 1 step -1}
            <label for="rating-star-{$i}"
                   title="{lang t="share|rate_with_x_stars" args=['%stars%' => $i]}"
                   class="rating__star">
                <input type="radio" id="rating-star-{$i}" name="stars" value="{$i}" class="hidden" required>
            </label>
        {/for}
    </div>
</form>
{if isset($rating.already_rated) && $rating.already_rated}
    <div class="small rating__already-rated">
        <em>{lang t="share|already_rated"}</em>
    </div>
{/if}
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
