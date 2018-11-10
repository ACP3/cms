{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    <div class="modal modal-static" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header justify-content-center">
                    <h5 class="modal-title">{lang t="users|successfully_logged_out"}</h5>
                </div>
                <div class="modal-footer justify-content-center">
                    <a href="{$url_previous_page}" class="btn btn-primary">{lang t="users|go_to_previous_page"}</a>
                    <a href="{$url_homepage}" class="btn btn-light">{lang t="users|go_to_homepage"}</a>
                </div>
            </div>
        </div>
    </div>
{/block}
