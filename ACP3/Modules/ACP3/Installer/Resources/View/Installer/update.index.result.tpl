{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if !$hasErrors}
        <p>
            {lang t="installer|db_update_next_steps"}
        </p>
        {include file="asset:Installer/Partials/delete_installer_directory.tpl"}
    {else}
        <p>
            {lang t="installer|db_update_error"}
        </p>
    {/if}
    {if count($results) > 0}
        <dl>
            {foreach $results as $migrationFqcn => $result}
                <dt>
                    {$migrationFqcn}
                </dt>
                <dd>
                    {if $result === null}
                        <span class="badge bg-success">
                            {lang t="installer|migration_execution_success"}
                        </span>
                    {else}
                        <div class="alert alert-danger">
                            <h6 class="alert-heading">{lang t="installer|migration_execution_error"}</h6>
                            <ul class="mb-0">
                                {foreach $result as $error}
                                    <li>
                                        <pre>{$error}</pre>
                                    </li>
                                {/foreach}
                            </ul>
                        </div>
                    {/if}
                </dd>
            {/foreach}
        </dl>
    {/if}
    {include file="asset:Installer/Partials/next_steps.tpl"}
{/block}
