{if $sharing.ratings_active === true && !empty($sharing.rating)}
    <div id="rating-wrapper">
        {include file="asset:Share/Partials/rating.tpl" rating=$sharing.rating}
    </div>
    {if !empty($sharing.services)}
        <hr>
    {/if}
    {javascripts}
    {js_libraries enable='font-awesome'}
    {/javascripts}
{/if}
{if !empty($sharing.services)}
    {load_module module="widget/share/index/index" path=$sharing.path}
    {js_libraries enable='shariff'}
{/if}
