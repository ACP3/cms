{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    <div class="modal static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h4 class="modal-title">{lang t="users|successfully_logged_out"}</h4>
                </div>
                <div class="modal-footer">
                    <div class="text-center">
                        <a href="{$url_previous_page}" class="btn btn-primary">{lang t="users|go_to_previous_page"}</a>
                        <a href="{$url_homepage}" class="btn btn-light">{lang t="users|go_to_homepage"}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
{/block}
