{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {redirect_message}
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item"><a href="#tab-1" class="nav-link active" data-toggle="tab">{lang t="system|installed_modules"}</a></li>
        <li class="nav-item"><a href="#tab-2" class="nav-link" data-toggle="tab">{lang t="system|installable_modules"}</a></li>
    </ul>
    <div class="tab-content">
        <div id="tab-1" class="tab-pane fade show active">
            <div class="table-responsive">
                <table class="table table-striped table-hover datagrid">
                    <thead>
                    <tr>
                        <th>{lang t="system|module_name"}</th>
                        <th class="datagrid-column__max-width">{lang t="system|description"}</th>
                        <th>{lang t="system|author"}</th>
                        <th class="text-right">{lang t="system|version"}</th>
                        <th class="datagrid-column__actions">{lang t="system|action"}</th>
                    </tr>
                    </thead>
                    <tbody>
                    {foreach $installed_modules as $row}
                        <tr>
                            <td>{lang t="`$row.name`|`$row.name`"}</td>
                            <td>{lang t="`$row.name`|mod_description"}</td>
                            <td>{$row.author}</td>
                            <td class="text-right">{$row.version}</td>
                            <td class="text-center">
                                {if $row.protected === true}
                                    <i class="fas fa-ban text-danger"
                                       title="{lang t="system|protected_module_description"}"></i>
                                {elseif $row.installable === false}
                                    <i class="fas fa-info-circle text-info"
                                       title="{lang t="system|not_installable_module_description"}"></i>
                                {else}
                                    {if $row.active === true}
                                        <a href="{uri args="acp/system/extensions/modules/dir_`$row.dir`/action_deactivate"}"
                                           class="btn btn-block btn-light btn-sm"
                                           title="{lang t="system|disable_module"}"
                                           data-ajax-form="true"
                                           data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
                                            <i class="fas fa-trash"></i>
                                            {lang t="system|disable"}
                                        </a>
                                    {else}
                                        <a href="{uri args="acp/system/extensions/modules/dir_`$row.dir`/action_activate"}"
                                           class="btn btn-block btn-primary btn-sm"
                                           title="{lang t="system|enable_module"}"
                                           data-ajax-form="true"
                                           data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
                                            <i class="fas fa-check"></i>
                                            {lang t="system|enable"}
                                        </a>
                                    {/if}
                                    <a href="{uri args="acp/system/extensions/modules/dir_`$row.dir`/action_uninstall"}"
                                       class="btn btn-block btn-danger btn-sm"
                                       title="{lang t="system|uninstall_module"}"
                                       data-ajax-form="true"
                                       data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
                                        <i class="fas fa-power-off"></i>
                                        {lang t="system|uninstall"}
                                    </a>
                                {/if}
                            </td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
            </div>
        </div>
        <div id="tab-2" class="tab-pane fade">
            {if !empty($new_modules)}
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
                        {foreach $new_modules as $row}
                            <tr>
                                <td>{lang t="`$row.name`|`$row.name`"}</td>
                                <td>{lang t="`$row.name`|mod_description"}</td>
                                <td>{$row.version}</td>
                                <td>{$row.author}</td>
                                <td class="text-center">
                                    <a href="{uri args="acp/system/extensions/modules/dir_`$row.dir`/action_install"}"
                                       class="btn btn-block btn-success btn-sm"
                                       title="{lang t="system|install_module"}"
                                       data-ajax-form="true"
                                       data-ajax-form-loading-text="{lang t="system|loading_please_wait"}"
                                       data-hash-change="#tab-2">
                                        <i class="fas fa-power-off"></i>
                                        {lang t="system|install"}
                                    </a>
                                </td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
                </div>
            {else}
                {include file="asset:System/Partials/no_results.tpl" alert_type="success" no_results_text={lang t="system|no_modules_available_for_installation"}}
            {/if}
        </div>
    </div>
    {javascripts}
        {include_js module="system" file="partials/hash-change"}
    {/javascripts}
{/block}
