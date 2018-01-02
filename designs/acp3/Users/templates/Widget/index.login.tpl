<nav class="navbar navbar-default navbar-static-top" id="nav-user-login">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#nav-user-login-collapse">
                <span class="sr-only">{lang t="system|toggle_navigation"}</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <span class="navbar-brand hidden-md hidden-lg">
                {lang t="users|user_menu"}
            </span>
        </div>
        <div id="nav-user-login-collapse" class="collapse navbar-collapse">
            <form class="navbar-form navbar-right" action="{uri args="users/index/login"}" method="post" accept-charset="UTF-8">
                <div class="form-group">
                    <input class="form-control input-sm" type="text" name="nickname" id="nav-nickname" maxlength="30" placeholder="{lang t="users|nickname"}" required>
                </div>
                <div class="form-group">
                    <input class="form-control input-sm" type="password" name="pwd" id="nav-pwd" placeholder="{lang t="users|pwd"}" required>
                </div>
                <button type="submit" name="submit" class="btn btn-primary btn-sm" title="{lang t="users|log_in"}">
                    <i class="fa fa-sign-in" aria-hidden="true"></i>
                </button>
            </form>
        </div>
    </div>
</nav>
