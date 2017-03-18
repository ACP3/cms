<html>
<head>
    {block EMAIL_HEAD}{/block}
    <meta name="viewport" content="width=device-width"/>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name=”x-apple-disable-message-reformatting”>
    {block EMAIL_STYLESHEET}
        <style type="text/css">
            body {
                font-family: Arial, Helvetica, sans-serif;
                font-size: 12px;
            }

            .email-content-wrapper {
                width: 600px;
                margin: 0 auto;
            }
        </style>
    {/block}
</head>
<body>
    {block EMAIL_CONTENT_BEFORE}{/block}
    <center>
        <table class="email-content-wrapper">
            <tr>
                <td>
                    {block EMAIL_CONTENT}{/block}
                </td>
            </tr>
        </table>
    </center>
    {block EMAIL_CONTENT_AFTER}{/block}
</body>
</html>
