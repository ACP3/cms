<nav class="navbar sticky-top navbar-expand-lg navbar-light bg-light shadow-sm" id="nav-user-login">
    <div class="container">
        <a class="navbar-brand d-md-none" href="#">{lang t="users|user_menu"}</a>
        <button class="navbar-toggler"
                type="button"
                data-toggle="collapse"
                data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent"
                aria-expanded="false"
                aria-label="{lang t="system|toggle_navigation"}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <form class="form-inline ml-auto" action="{uri args="users/index/login/redirect_`$redirect_uri`"}" method="post" accept-charset="UTF-8">
                <input class="form-control my-2 my-sm-0 mr-sm-2" type="text" name="nickname" id="nav-nickname" maxlength="30" placeholder="{lang t="users|nickname"}" required>
                <input class="form-control mb-2 mb-sm-0 mr-sm-2" type="password" name="pwd" id="nav-pwd" placeholder="{lang t="users|pwd"}" required>
                <button type="submit" name="submit" class="btn btn-primary" title="{lang t="users|log_in"}">
                    <i class="fas fa-sign-in-alt"></i>
                </button>
            </form>
        </div>
    </div>
</nav>
