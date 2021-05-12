{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {redirect_message}
    {tabset identifier="system-admin-modules"}
        {tab title={lang t="system|installed_modules"}}
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
                    {foreach $installed_modules as $translatedModuleName => $row}
                        <tr>
                            <td>{$translatedModuleName}</td>
                            <td>{$row.description}</td>
                            <td>{$row.version}</td>
                            <td>{$row.author}</td>
                            <td class="text-center">
                                {if $row.installable === false}
                                    {icon iconSet="solid" icon="info-circle" cssSelectors="text-info" title={lang t="system|not_installable_module_description"}}
                                {else}
                                    <a href="{uri args="acp/system/extensions/modules/dir_`$row.name`/action_uninstall"}"
                                       class="btn btn-block btn-danger btn-xs"
                                       title="{lang t="system|uninstall_module"}"
                                       data-ajax-form="true"
                                       data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
                                        {icon iconSet="solid" icon="power-off"}
                                        {lang t="system|uninstall"}
                                    </a>
                                {/if}
                            </td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
            </div>
        {/tab}
        {if !empty($new_modules)}
            {tab title={lang t="system|installable_modules"}}
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
                        {foreach $new_modules as $translatedModuleName => $row}
                            <tr>
                                <td>{$translatedModuleName}</td>
                                <td>{lang t="`$row.name`|mod_description"}</td>
                                <td>{$row.version}</td>
                                <td>{$row.author}</td>
                                <td class="text-center">
                                    <a href="{uri args="acp/system/extensions/modules/dir_`$row.name`/action_install"}"
                                       class="btn btn-block btn-success btn-xs"
                                       title="{lang t="system|install_module"}"
                                       data-ajax-form="true"
                                       data-ajax-form-loading-text="{lang t="system|loading_please_wait"}"
                                       data-hash-change="#tab-2">
                                        {icon iconSet="solid" icon="power-off"}
                                        {lang t="system|install"}
                                    </a>
                                </td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
                </div>
            {/tab}
        {/if}
    {/tabset}
    {javascripts}
        {js_libraries enable="ajax-form"}
        {include_js module="system" file="partials/hash-change"}
    {/javascripts}
{/block}
