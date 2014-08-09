<div class="row">
    <div class="col-lg-7">
        <fieldset>
            <legend>{lang t="install|step_3_legend_1"}</legend>
            <p>
                {lang t="install|step_3_paragraph_1"}
            </p>

            <div class="row">
                <div class="col-lg-6">
                    <table class="table table-condensed">
                        <thead>
                        <tr>
                            <th></th>
                            <th style="width:33%">{lang t="install|required"}</th>
                            <th style="width:33%">{lang t="install|found"}</th>
                        </tr>
                        </thead>
                        <tbody>
                        {foreach $requirements as $row}
                            <tr>
                                <td>{$row.name}</td>
                                <td>{$row.required}</td>
                                <td>
                                    <span style="color:#{$row.color}">{$row.found}{if $row.color == 'f00'} - {lang t="install|installation_impossible"}{/if}</span>
                                </td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
                </div>
                <div class="col-lg-6">
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
    <div class="col-lg-5">
        <fieldset>
            <legend>{lang t="install|step_3_legend_2"}</legend>
            <p>
                {lang t="install|step_3_paragraph_2"}
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
</div>{if isset($stop_install)}
    <div class="alert alert-error text-center">
        <strong>{lang t="install|stop_installation"}</strong>
    </div>{else}
    <div class="well well-sm text-center">
        {if isset($check_again)}
            <a href="{$REQUEST_URI}" class="btn btn-warning">{lang t="install|check_again"}</a>
        {else}
            <a href="{uri args="install/install"}" class="btn btn-default">{lang t="install|install_index"}</a>
        {/if}
    </div>{/if}