{if !empty($sharing.services)}
    {load_module module="widget/share/index/index" path=$sharing.path}
    {js_libraries enable='shariff'}
{/if}
{if $sharing.ratings_active === true && !empty($sharing.rating_stats)}
    {if !empty($sharing.services)}
        <hr>
    {/if}
    {include file="asset:Share/Partials/ratings.tpl" sharing=$sharing}
{/if}
