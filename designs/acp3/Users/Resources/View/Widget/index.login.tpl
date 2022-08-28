<nav id="nav-user-login" class="navbar navbar-expand-lg sticky-top navbar-light bg-light">
    <div class="container">
        <span class="navbar-brand d-lg-none">{lang t="users|user_menu"}</span>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav-user-login-content" aria-controls="nav-user-login-content" aria-expanded="false" aria-label="{lang t="system|toggle_navigation"}">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="nav-user-login-content">
            <form class="d-flex ms-auto" action="{uri args="users/index/login/redirect_`$redirect_uri`"}" method="post" accept-charset="UTF-8">
                <input class="form-control me-2" type="text" name="nickname" id="nav-nickname" maxlength="30" aria-label="{lang t="users|nickname"}" placeholder="{lang t="users|nickname"}" required>
                <input class="form-control me-2" type="password" name="pwd" id="nav-pwd" aria-label="{lang t="users|pwd"}" placeholder="{lang t="users|pwd"}" required>
                <button type="submit" name="submit" class="btn btn-outline-primary" title="{lang t="users|log_in"}">
                    {icon iconSet="solid" icon="right-to-bracket"}
                </button>
            </form>
        </div>
    </div>
</nav>
