<!DOCTYPE html>
<html>
<head>
    <title>Exception: {$EXCEPTION->getMessage()} | {site_title}</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="{$ROOT_DIR}ACP3/Modules/ACP3/System/Resources/Assets/css/bootstrap.min.css">
    <style type="text/css">
        .alert-danger {
            width: 70%;
            height: 320px;
            margin: -160px auto;
            padding: 0 20px;
            text-align: center;
            position: absolute;
            top: 50%;
            left: 15%;
        }

        .alert-danger h4 {
            margin-top: 20px;
        }

        .alert-danger .stacktrace {
            height: 190px;
            overflow: auto;
        }
    </style>
</head>

<body>
<div class="alert alert-danger">
    <h4>{$EXCEPTION->getMessage()}</h4>
    <hr>
    <h4 class="text-left">Stacktrace:</h4>
    <pre class="alert alert-warning stacktrace text-left">{$EXCEPTION->getTraceAsString()}</pre>
</div>
</body>
</html>