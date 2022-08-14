{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    <div class="row">
        <div class="col-md-7">
            <fieldset>
                <legend>{lang t="installer|step_3_legend_1"}</legend>
                <p>
                    {lang t="installer|step_3_paragraph_1"}
                </p>

                <table class="table table-condensed table-hover">
                    <thead>
                    <tr>
                        <th style="width:33%"></th>
                        <th>{lang t="installer|found"}</th>
                        <th>{lang t="installer|required"}</th>
                    </tr>
                    </thead>
                    <tbody>
                    {foreach $requirements as $row}
                        <tr>
                            <td><strong>{$row.name}</strong></td>
                            <td class="{if $row.satisfied}table-success{else}table-danger{/if}">
                                {$row.found}{if !$row.satisfied} - {lang t="installer|installation_impossible"}{/if}
                            </td>
                            <td>{$row.required}</td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
                <table class="table table-condensed table-hover">
                    <thead>
                    <tr>
                        <th style="width:33%"></th>
                        <th>{lang t="installer|found"}</th>
                    </tr>
                    </thead>
                    <tbody>
                    {foreach $files_dirs as $row}
                        <tr>
                            <td><strong>{$row.path}</strong></td>
                            <td>
                                <span class="badge {if $row.exists}bg-success{else}bg-danger{/if}">
                                    {lang t="installer|{if $row.exists}found{else}not_found{/if}"}
                                </span>
                                <span class="badge {if $row.writable}bg-success{else}bg-danger{/if}">
                                    {lang t="installer|{if $row.exists}writable{else}not_writable{/if}"}
                                </span>
                            </td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
            </fieldset>
        </div>
        <div class="col-md-5">
            <fieldset>
                <legend>{lang t="installer|step_3_legend_2"}</legend>
                <p>
                    {lang t="installer|step_3_paragraph_2"}
                </p>
                <ul class="list-unstyled">
                    {foreach $php_settings as $row}
                        <li>
                            <strong>{$row.setting}</strong> <span class="badge {if $row.satisfied}bg-success{else}bg-danger{/if}">{$row.value}</span>
                        </li>
                    {/foreach}
                </ul>
            </fieldset>
        </div>
    </div>
    {if $stop_install === true}
        <div class="alert alert-danger text-center" role="alert">
            <strong>{lang t="installer|stop_installation"}</strong>
        </div>
    {else}
        <div class="card bg-light mb-3">
            <div class="card-body text-center">
                {if $check_again === true}
                    <a href="{$REQUEST_URI}"
                       class="btn btn-warning"
                       data-ajax-form="true"
                       data-ajax-form-loading-text="{lang t="installer|loading_please_wait"}">
                        {icon iconSet="solid" icon="rotate"}
                        {lang t="installer|check_again"}
                    </a>
                    {javascripts}
                        {js_libraries enable="ajax-form"}
                    {/javascripts}
                {else}
                    <a href="{uri args="installer/index/install"}" class="btn btn-outline-primary">{lang t="installer|installer_index_install"}</a>
                {/if}
            </div>
        </div>
    {/if}
{/block}
