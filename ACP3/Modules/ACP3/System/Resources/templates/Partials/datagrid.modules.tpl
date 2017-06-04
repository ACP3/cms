<div class="table-responsive">
    <table class="table table-striped table-hover datagrid">
        <thead>
        <tr>
            <th>{lang t="system|module_name"}</th>
            <th>{lang t="system|description"}</th>
            <th>{lang t="system|version"}</th>
            <th>{lang t="system|author"}</th>
            <th class="datagrid-column__actions">{lang t="system|action"}</th>
        </tr>
        </thead>
        <tbody>
        {foreach $modules as $row}
            <tr>
                <td>{lang t="system|module_name_formatted" args=['%package_name%' => $row.package_name, '%name%' => $row.name]}</td>
                <td>{$row.description}</td>
                <td>{$row.version}</td>
                <td>{$row.author|implode:', '}</td>
                <td class="text-center">
                    {if $row.installed === false && $row.installable === true}
                        <a href="{uri args="acp/system/extensions/modules/dir_`$row.dir`/action_install"}"
                           class="btn btn-block btn-success btn-xs"
                           title="{lang t="system|install_module"}"
                           data-ajax-form="true"
                           data-ajax-form-loading-text="{lang t="system|loading_please_wait"}"
                           data-hash-change="#tab-2">
                            <i class="glyphicon glyphicon-off"></i>
                            {lang t="system|install"}
                        </a>
                    {elseif $row.protected === true}
                        <i class="glyphicon glyphicon-remove-circle text-danger"
                           title="{lang t="system|protected_module_description"}"></i>
                    {elseif $row.installable === false}
                        <i class="glyphicon glyphicon-info-sign text-info"
                           title="{lang t="system|not_installable_module_description"}"></i>
                    {else}
                        {if $row.active === true}
                            <a href="{uri args="acp/system/extensions/modules/dir_`$row.dir`/action_deactivate"}"
                               class="btn btn-block btn-default btn-xs"
                               title="{lang t="system|disable_module"}"
                               data-ajax-form="true"
                               data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
                                <i class="glyphicon glyphicon-remove"></i>
                                {lang t="system|disable"}
                            </a>
                        {else}
                            <a href="{uri args="acp/system/extensions/modules/dir_`$row.dir`/action_activate"}"
                               class="btn btn-block btn-primary btn-xs"
                               title="{lang t="system|enable_module"}"
                               data-ajax-form="true"
                               data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
                                <i class="glyphicon glyphicon-ok"></i>
                                {lang t="system|enable"}
                            </a>
                        {/if}
                        <a href="{uri args="acp/system/extensions/modules/dir_`$row.dir`/action_uninstall"}"
                           class="btn btn-block btn-danger btn-xs"
                           title="{lang t="system|uninstall_module"}"
                           data-ajax-form="true"
                           data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
                            <i class="glyphicon glyphicon-off"></i>
                            {lang t="system|uninstall"}
                        </a>
                    {/if}
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
</div>
