<div class="alert alert-{if !empty($alert_type)}{$alert_type}{else}warning{/if} text-center">
    {if !empty($no_results_text)}
        {$no_results_text}
    {else}
        {lang t="system|no_entries"}
    {/if}
</div>
