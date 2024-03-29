<html>
<head>
    <title>{$mail.title}</title>
    <meta name="viewport" content="width=device-width"/>
    <meta http-equiv="content-type" content="text/html; charset={$mail.charset}">
    <meta name=”x-apple-disable-message-reformatting”>
    {block EMAIL_HEAD}{/block}
    {block EMAIL_STYLESHEET}
        <style type="text/css">
            html, body {
                margin: 0;
                padding: 0;
            }
            body {
                background-color: #fff;
            }
            h1, h2, h3, h4, h5, h6, p {
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            }
            h1, h2, h3, h4, h5, h6 {
                margin: 0;
                padding: 0 0 10px;
            }
            h1, h2, h3, h4, h5, h6, strong {
                color: #000;
                font-weight: bold;
            }
            h1 {
                font-size: 20px;
            }
            h2 {
                font-size: 18px;
            }
            h3 {
                font-size: 16px;
            }
            h4, h5, h6 {
                font-size: 14px;
            }
            p {
                color: #333;
                font-size: 13px;
            }
            .email-content-wrapper {
                width: 600px;
                margin: 0 auto;
            }
            .logo {
                padding: 15px 0;
            }
            .text-left, .text-start {
                text-align: left;
            }
            .text-right, .text-end {
                text-align: right;
            }
            .text-center {
                text-align: center;
            }
            .text-justify {
                text-align: justify;
            }
        </style>
    {/block}
</head>
<body>
    {block EMAIL_CONTENT_BEFORE}
        {if !empty($mail.url_web_view)}
            <p class="text-center">
                <a href="{$mail.url_web_view}">{lang t="system|use_web_view"}</a>
            </p>
        {/if}
    {/block}
    <center>
        <table border="0" cellpadding="0" cellspacing="0" class="email-content-wrapper">
            <tr>
                <td class="text-center logo">
                    {block EMAIL_HEADER}
                        <img src="{image file="logo.png" module="system" absolute=true}" alt="{site_title}">
                    {/block}
                </td>
            </tr>
            <tr>
                <td>
                    {block EMAIL_CONTENT}
                        {$mail.body}
                        {if !empty($mail.signature)}
                            <hr>
                            {$mail.signature}
                        {/if}
                    {/block}
                </td>
            </tr>
        </table>
    </center>
    {block EMAIL_CONTENT_AFTER}{/block}
</body>
</html>
