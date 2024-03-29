{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_BEFORE_AJAX_FORM}
    {redirect_message}
{/block}
{block CONTENT_AJAX_FORM}
    {tabset identifier="system-admin-settings-form"}
        {tab title={lang t="system|general"}}
            {include file="asset:System/Partials/form_group.input_text.tpl" name="homepage" value=$form.homepage required=true label={lang t="system|homepage"} help={lang t="system|homepage_description"}}
            {include file="asset:System/Partials/form_group.select.tpl" options=$entries required=true label={lang t="system|records_per_page"}}
            {include file="asset:System/Partials/form_group.input_number.tpl" name="flood" value=$form.flood required=true label={lang t="system|flood_barrier"} help={lang t="system|flood_barrier_description"}}
            {include file="asset:System/Partials/form_group.select.tpl" options=$wysiwyg required=true label={lang t="system|editor"}}
            {include file="asset:System/Partials/form_group.button_group.tpl" options=$mod_rewrite required=true label={lang t="system|mod_rewrite"} help={lang t="system|mod_rewrite_description"}}
        {/tab}
        {tab title={lang t="system|site_title"}}
            {include file="asset:System/Partials/form_group.input_text.tpl" name="site_title" value=$form.site_title required=true label={lang t="system|site_title"}}
            {include file="asset:System/Partials/form_group.input_text.tpl" name="site_subtitle" value=$form.site_subtitle label={lang t="system|site_subtitle"}}
            {include file="asset:System/Partials/form_group.button_group.tpl" options=$site_subtitle_mode required=true label={lang t="system|site_subtitle_mode"}}
            {include file="asset:System/Partials/form_group.button_group.tpl" options=$site_subtitle_homepage_mode formGroupId="site-subtitle-homepage-home-container" required=true label={lang t="system|site_subtitle_homepage_mode"}}
        {/tab}
        {tab title={lang t="system|localization"}}
            {include file="asset:System/Partials/form_group.select.tpl" options=$languages required=true label={lang t="system|language"}}
            {include file="asset:System/Partials/form_group.input_text.tpl" name="date_format_long" value=$form.date_format_long required=true maxlength=20 label={lang t="system|date_format_long"} help={lang t="system|php_date_function"}}
            {include file="asset:System/Partials/form_group.input_text.tpl" name="date_format_short" value=$form.date_format_short required=true maxlength=20 label={lang t="system|date_format_short"} help={lang t="system|php_date_function"}}
            {include file="asset:System/Partials/form_group.time_zone.tpl" name="date_time_zone" time_zones=$time_zones required=true label={lang t="system|time_zone"}}
        {/tab}
        {tab title={lang t="system|maintenance"}}
            {include file="asset:System/Partials/form_group.button_group.tpl" options=$maintenance required=true label={lang t="system|maintenance_mode"}}
            {include file="asset:System/Partials/form_group.textarea.tpl" name="maintenance_message" formGroupId="maintenance-message-container" value=$form.maintenance_message required=true label={lang t="system|maintenance_msg"}}
        {/tab}
        {tab title={lang t="system|performance"}}
            {include file="asset:System/Partials/form_group.button_group.tpl" options=$page_cache_purge_mode required=true label={lang t="system|page_cache_purge_mode"}}
            {include file="asset:System/Partials/form_group.button_group.tpl" options=$cache_images required=true label={lang t="system|cache_images"}}
            {include file="asset:System/Partials/form_group.input_number.tpl" name="cache_lifetime" value=$form.cache_lifetime required=true label={lang t="system|cache_lifetime"} input_group_after={lang t="system|seconds"}}
        {/tab}
        {tab title={lang t="system|email"}}
            {include file="asset:System/Partials/form_group.select.tpl" options=$mailer_type required=true label={lang t="system|mailer_type"}}
            <div id="mailer-smtp-1">
                {include file="asset:System/Partials/form_group.input_text.tpl" name="mailer_smtp_host" value=$form.mailer_smtp_host labelRequired=true label={lang t="system|mailer_smtp_hostname"}}
                {include file="asset:System/Partials/form_group.input_number.tpl" name="mailer_smtp_port" value=$form.mailer_smtp_port labelRequired=true label={lang t="system|mailer_smtp_port"}}
                {include file="asset:System/Partials/form_group.select.tpl" options=$mailer_smtp_security required=true label={lang t="system|mailer_smtp_security"}}
                {include file="asset:System/Partials/form_group.button_group.tpl" options=$mailer_smtp_auth required=true label={lang t="system|mailer_smtp_auth"}}
                <div id="mailer-smtp-2">
                    {include file="asset:System/Partials/form_group.input_text.tpl" name="mailer_smtp_user" value=$form.mailer_smtp_user labelRequired=true label={lang t="system|mailer_smtp_username"}}
                    {include file="asset:System/Partials/form_group.input_password.tpl" name="mailer_smtp_password" value=$form.mailer_smtp_password label={lang t="system|mailer_smtp_password"}}
                </div>
            </div>
        {/tab}
    {/tabset}
    {include file="asset:System/Partials/form_group.submit_split.tpl" form_token=$form_token}
    {javascripts}
        {include_js module="system" file="admin/index.settings"}
    {/javascripts}
{/block}
