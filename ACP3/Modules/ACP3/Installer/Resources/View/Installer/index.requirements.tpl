{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    <div class="row">
        <div class="col-sm-7">
            <fieldset>
                <legend>{lang t="installer|step_3_legend_1"}</legend>
                <p>
                    {lang t="installer|step_3_paragraph_1"}
                </p>

                <div class="row">
                    <div class="col-sm-6">
                        <table class="table table-condensed">
                            <thead>
                            <tr>
                                <th></th>
                                <th style="width:33%">{lang t="installer|required"}</th>
                                <th style="width:33%">{lang t="installer|found"}</th>
                            </tr>
                            </thead>
                            <tbody>
                            {foreach $requirements as $row}
                                <tr>
                                    <td>{$row.name}</td>
                                    <td>{$row.required}</td>
                                    <td>
                                        <span style="color:#{$row.color}">{$row.found}{if $row.color == 'f00'} - {lang t="installer|installation_impossible"}{/if}</span>
                                    </td>
                                </tr>
                            {/foreach}
                            </tbody>
                        </table>
                    </div>
                    <div class="col-sm-6">
                        <ul class="unstyled">
                            {foreach $files_dirs as $row}
                                <li>
                                    <strong>{$row.path}</strong>
                                    <span class="label label-{$row.class_1}">{$row.exists}</span>
                                    <span class="label label-{$row.class_2}">{$row.writable}</span>
                                </li>
                            {/foreach}
                        </ul>
                    </div>
                </div>
            </fieldset>
        </div>
        <div class="col-sm-5">
            <fieldset>
                <legend>{lang t="installer|step_3_legend_2"}</legend>
                <p>
                    {lang t="installer|step_3_paragraph_2"}
                </p>
                <ul class="unstyled">
                    {foreach $php_settings as $row}
                        <li>
                            <strong>{$row.setting}</strong> <span class="label label-{$row.class}">{$row.value}</span>
                        </li>
                    {/foreach}
                </ul>
            </fieldset>
        </div>
    </div>
    {if $stop_install === true}
        <div class="alert alert-danger text-center">
            <strong>{lang t="installer|stop_installation"}</strong>
        </div>
    {else}
        <div class="well well-sm text-center">
            {if $check_again === true}
                <a href="{$REQUEST_URI}"
                   class="btn btn-warning"
                   data-ajax-form="true"
                   data-ajax-form-loading-text="{lang t="installer|loading_please_wait"}">
                    <i class="glyphicon glyphicon-refresh"></i>
                    {lang t="installer|check_again"}
                </a>
                {javascripts}
                    {include_js module="system" file="partials/ajax-form"}
                {/javascripts}
            {else}
                <a href="{uri args="installer/index/install"}" class="btn btn-default">{lang t="installer|installer_index_install"}</a>
            {/if}
        </div>
    {/if}
{/block}
