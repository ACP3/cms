<nav aria-label="{lang t="system|pagination"}">
    <ul class="pagination justify-content-center">
        {if !empty($pager.previous)}
            <li class="page-item">
                <a href="{$pager.previous}" rel="prev" class="page-link">&laquo; {$pager.previousLabel}</a>
            </li>
        {elseif isset($pager.showEmpty)}
            <li class="page-item disabled">
                <a class="page-link">&laquo; {$pager.previousLabel}</a>
            </li>
        {/if}
        {if !empty($pager.index)}
            <li class="page-item">
                <a href="{$pager.index}" class="page-link">
                    {$pager.indexLabel}
                </a>
            </li>
        {/if}
        {if !empty($pager.next)}
            <li class="page-item">
                <a href="{$pager.next}" rel="next" class="page-link">{$pager.nextLabel} &raquo;</a>
            </li>
        {elseif isset($pager.showEmpty)}
            <li class="page-item disabled">
                <a class="page-link">{$pager.nextLabel} &raquo;</a>
            </li>
        {/if}
    </ul>
</nav>
