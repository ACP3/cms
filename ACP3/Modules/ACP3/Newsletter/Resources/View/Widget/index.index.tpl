<div class="card mb-3">
    <div class="card-header">
        {lang t="newsletter|subscribe_newsletter"}
    </div>
    <div class="card-body">
        <form action="{uri args="newsletter"}" method="post" accept-charset="UTF-8">
            <div class="form-group">
                <input class="form-control" type="email" name="mail" maxlength="120" placeholder="{lang t="system|email_address"}" required>
            </div>
            {event name="captcha.event.display_captcha" length=3 input_only=true path='newsletter' input_id='captcha-newsletter-widget'}
            <div class="form-group" style="margin: 10px 0 0">
                <button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
                <input type="hidden" name="salutation" value="0">
                <input type="hidden" name="first_name" value="">
                <input type="hidden" name="last_name" value="">
                {$form_token}
            </div>
        </form>
    </div>
</div>
