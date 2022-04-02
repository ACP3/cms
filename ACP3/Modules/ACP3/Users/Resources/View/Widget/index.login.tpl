<div class="card mb-3">
    <div class="card-header">
        <h3 class="panel-title">{lang t="users|login"}</h3>
    </div>
    <div class="card-body">
        <form action="{uri args="users/index/login/redirect_`$redirect_uri`"}" method="post" accept-charset="UTF-8">
            <div class="mb-3">
                <input class="form-control" type="text" name="nickname" id="nav-nickname" maxlength="30" placeholder="{lang t="users|nickname"}" required>
            </div>
            <div class="mb-3">
                <input class="form-control" type="password" name="pwd" id="nav-pwd" placeholder="{lang t="users|pwd"}" required>
            </div>
            <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" name="remember" id="nav-remember" value="1">
                <label for="nav-remember" class="form-check-label">
                    {lang t="users|remember_me"}
                </label>
            </div>
            <button type="submit" name="submit" class="btn btn-outline-primary">
                {icon iconSet="solid" icon="right-to-bracket"} {lang t="users|log_in"}
            </button>
        </form>
    </div>
    <div class="list-group list-group-flush">
        <a href="{uri args="users/index/forgot_pwd"}" class="list-group-item list-group-item-action">
            {icon iconSet="solid" icon="question"}
            {lang t="users|forgot_pwd"}
        </a>
        {if $enable_registration == 1}
            <a href="{uri args="users/index/register"}" class="list-group-item list-group-item-action">
                {icon iconSet="solid" icon="star"}
                {lang t="users|register"}
            </a>
        {/if}
    </div>
</div>
