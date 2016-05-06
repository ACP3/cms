{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {redirect_message}
    <div class="tabbable">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-1" data-toggle="tab">{lang t="system|installed_modules"}</a></li>
            <li><a href="#tab-2" data-toggle="tab">{lang t="system|installable_modules"}</a></li>
        </ul>
        <div class="tab-content">
            <div id="tab-1" class="tab-pane fade in active">
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
                    {foreach $installed_modules as $row}
                        <tr>
                            <td>{$row.name}</td>
                            <td>{$row.description}</td>
                            <td>{$row.version}</td>
                            <td>{$row.author}</td>
                            <td>
                                {if $row.protected === true}
                                    <i class="glyphicon glyphicon-remove-circle text-danger" title="{lang t="system|protected_module_description"}"></i>
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
            <div id="tab-2" class="tab-pane fade">
                {if !empty($new_modules)}
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
                                <td>{$row.name}</td>
                                <td>{$row.description}</td>
                                <td>{$row.version}</td>
                                <td>{$row.author}</td>
                                <td>
                                    <a href="{uri args="acp/system/extensions/modules/dir_`$row.dir`/action_install"}"
                                       class="btn btn-block btn-success btn-xs"
                                       title="{lang t="system|install_module"}"
                                       data-ajax-form="true"
                                       data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
                                        <i class="glyphicon glyphicon-off"></i>
                                        {lang t="system|install"}
                                    </a>
                                </td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
                {else}
                    <div class="alert alert-warning text-center">
                        <strong>{lang t="system|no_modules_available_for_installation"}</strong>
                    </div>
                {/if}
            </div>
        </div>
    </div>
    {javascripts}
        {include_js module="system" file="ajax-form"}
    {/javascripts}
{/block}
