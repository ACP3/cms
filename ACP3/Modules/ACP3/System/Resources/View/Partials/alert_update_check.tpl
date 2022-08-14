<div class="alert alert-warning p-1 text-center">
    <small>
        {if $update.is_latest}
            {lang t="system|acp3_up_to_date"}
        {else}
            {lang t="system|acp3_not_up_to_date" args=['%link_start%' => "<a href=\"`$update.url`\" target=\"_blank\">",'%link_end%' => '</a>']}
        {/if}
    </small>
</div>
