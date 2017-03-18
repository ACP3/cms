<html>
<head>
    {block EMAIL_HEAD}{/block}
    <meta name="viewport" content="width=device-width"/>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name=”x-apple-disable-message-reformatting”>
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
                font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
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
            .text-left {
                text-align: left;
            }
            .text-right {
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
