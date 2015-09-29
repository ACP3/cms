<!DOCTYPE html>
<html>
<head>
    <title>{$mail.title}</title>
    <meta charset="{$mail.charset}">
    <style type="text/css">
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <a href="{$mail.url_web_view}">{lang t="newsletter|use_web_view"}</a>
    {$mail.body}
    <hr>
    {$mail.signature}
</body>
</html>