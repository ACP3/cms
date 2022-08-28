{extends file="asset:System/layout.confirm_box.tpl"}

{$confirm=['text' => {lang t="users|successfully_logged_out"}]}

{block CONFIRM_BOX_MODAL_FOOTER}
    <a href="{$url_previous_page}" class="btn btn-primary">{lang t="users|go_to_previous_page"}</a>
    <a href="{$url_homepage}" class="btn btn-light">{lang t="users|go_to_homepage"}</a>
{/block}
