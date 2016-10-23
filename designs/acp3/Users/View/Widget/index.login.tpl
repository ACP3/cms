<nav class="navbar navbar-default navbar-static-top" id="nav-user-login" role="navigation">
    <div class="container">
        <form class="navbar-form navbar-right" action="{uri args="users/index/login/redirect_`$redirect_uri`"}" method="post" accept-charset="UTF-8">
            <input class="form-control input-sm" type="text" name="nickname" id="nav-nickname" maxlength="30" placeholder="{lang t="users|nickname"}" required>
            <input class="form-control input-sm" type="password" name="pwd" id="nav-pwd" placeholder="{lang t="users|pwd"}" required>
            <button type="submit" name="submit" class="btn btn-primary btn-sm" title="{lang t="users|log_in"}">
                <i class="glyphicon glyphicon-log-in"></i>
            </button>
        </form>
    </div>
</nav>
