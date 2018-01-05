{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    <div class="row">
        <div class="col-md-7">
            <fieldset>
                <legend>{lang t="install|step_3_legend_1"}</legend>
                <p>
                    {lang t="install|step_3_paragraph_1"}
                </p>
                <table class="table table-condensed">
                    <thead>
                    <tr>
                        <th></th>
                        <th style="width:33%">{lang t="install|required"}</th>
                        <th style="width:33%">{lang t="install|found"}</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    {foreach $requirements as $row}
                        <tr>
                            <td>{$row.name}</td>
                            <td>{$row.required}</td>
                            <td>
                                {$row.found}
                            </td>
                            <td class="{if $row.success}text-success{else}text-danger{/if}">
                                {if $row.success}
                                    <i class="glyphicon glyphicon-ok"></i>
                                {else}
                                    <i class="glyphicon glyphicon-remove" title="{lang t="install|installation_impossible"}"></i>
                                {/if}
                            </td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
            </fieldset>
            <fieldset>
                <legend>{lang t="install|step_3_legend_2"}</legend>
                <dl class="dl-horizontal modules-list">
                    {foreach $files_dirs as $row}
                        <dt>
                            {$row.path}
                        </dt>
                        <dd>
                            <span class="label {if $row.exists}label-success{else}label-danger{/if}">{$row.exists_lang}</span>
                            <span class="label {if $row.writable}label-success{else}label-danger{/if}">{$row.writable_lang}</span>
                        </dd>
                    {/foreach}
                </dl>
            </fieldset>
        </div>
        <div class="col-md-5">
            <fieldset>
                <legend>{lang t="install|step_3_legend_3"}</legend>
                <p>
                    {lang t="install|step_3_paragraph_2"}
                </p>
                <ul class="list-unstyled">
                    {foreach $php_settings as $row}
                        <li>
                            <strong>{$row.setting}</strong>
                            <span class="label {if $row.success}label-success{else}label-warning{/if}">{$row.value}</span>
                        </li>
                    {/foreach}
                </ul>
            </fieldset>
        </div>
    </div>
    {if $stop_install === true}
        <div class="alert alert-danger text-center">
            <strong>{lang t="install|stop_installation"}</strong>
        </div>
    {else}
        <div class="well well-sm text-center">
            {if $check_again === true}
                <a href="{$REQUEST_URI}" class="btn btn-warning">{lang t="install|check_again"}</a>
            {else}
                <a href="{uri args="install/install"}" class="btn btn-default">{lang t="install|install_index"}</a>
            {/if}
        </div>
    {/if}
{/block}
