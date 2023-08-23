<?php
if (isset($_GET["phpinfo"])) {
    phpinfo();
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Grupo V2 - Server Requirements</title>
    <style>
        body {
            padding-top: 15px;
            font-family: Tahoma, Geneva, sans-serif;
            background: #f9fafb;
            font-size: 14px;
        }
        #wrapper {
            width: 600px;
            margin: 0 auto;
            background: #fff;
            border-radius: 10px;
            padding: 15px;
            border: 2px solid #f0f0f0;
            -webkit-box-shadow: 0px 1px 15px 1px rgba(90, 90, 90, 0.08);
            box-shadow: 0px 1px 15px 1px rgba(90, 90, 90, 0.08);
        }
        a {
            text-decoration: none;
            color: #276bb3;
        }

        h1 {
            text-align: center;
            color: #424242;
            border-bottom: 1px solid #e4e4e4;
            padding-bottom: 25px;
            font-size: 22px;
            font-weight: normal;
        }
        table {
            width: 100%;
            padding: 10px;
            border-radius: 3px;
        }
        table thead th {
            text-align: left;
            padding: 5px 0px 5px 0px;
        }
        table tbody td {
            padding: 5px 0px;
        }

        table tbody td:last-child,table thead th:last-child {
            text-align: right;
        }
        .label {
            padding: 3px 10px;
            border-radius: 4px;
            color: #fff;

        }
        .label.label-success {
            background: #4ac700;
        }
        .label.label-warning {
            background: #dc2020;
        }
        #loader {
            position: relative;
            width: 44px;
            height: 8px;
            margin: 5px auto;
            padding-top: 35px;
            padding-bottom: 30px;
        }
        .dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 4px;
            background: #ccc;
            position: absolute;
        }

        .dot_1 {
            animation: animateDot11.5s linear infinite;
            left: 12px;
            background: #e579b8;
        }

        .dot_2 {
            animation: animateDot21.5s linear infinite;
            animation-delay: 0.5s;
            left: 24px;
        }

        .dot_3 {
            animation: animateDot31.5s linear infinite;
            left: 12px;
        }

        .dot_4 {
            animation: animateDot41.5s linear infinite;
            animation-delay: 0.5s;
            left: 24px;
        }
        .logo {
            margin-bottom: 35px;
            margin-top: 20px;
            display: block;
        }
        .logo img {
            margin: 0 auto;
            display: block;
        }
        .installbtn {
            display: none;
            text-align: center;
            margin-top: -25px;
        }

        .installbtn > a {
            display: INLINE-BLOCK;
            background: #F44336;
            color: white;
            text-decoration: none;
            padding: 6px 43px;
            border-radius: 5px;
            box-shadow: 0px 6px 0px #94170e;
        }
@keyframes animateDot1 {
            0% {
                transform: rotate(0deg) translateX(-12px);
            }
            25% {
                transform: rotate(180deg) translateX(-12px);
            }
            75% {
                transform: rotate(180deg) translateX(-12px);
            }
            100% {
                transform: rotate(360deg) translateX(-12px);
            }
        }
@keyframes animateDot2 {
            0% {
                transform: rotate(0deg) translateX(-12px);
            }
            25% {
                transform: rotate(-180deg) translateX(-12px);
            }
            75% {
                transform: rotate(-180deg) translateX(-12px);
            }
            100% {
                transform: rotate(-360deg) translateX(-12px);
            }
        }
@keyframes animateDot3 {
            0% {
                transform: rotate(0deg) translateX(12px);
            }
            25% {
                transform: rotate(180deg) translateX(12px);
            }
            75% {
                transform: rotate(180deg) translateX(12px);
            }
            100% {
                transform: rotate(360deg) translateX(12px);
            }
        }
@keyframes animateDot4 {
            0% {
                transform: rotate(0deg) translateX(12px);
            }
            25% {
                transform: rotate(-180deg) translateX(12px);
            }
            75% {
                transform: rotate(-180deg) translateX(12px);
            }
            100% {
                transform: rotate(-360deg) translateX(12px);
            }
        }

    </style>
</head>
<body>
    <?php
    $error = false;
    if (version_compare(PHP_VERSION, '7.0') >= 0) {
        $requirement1 = "<span class='label label-success'>v." . PHP_VERSION . '</span>';
    } else {
        $error = true;
        $requirement1 = "<span class='label label-warning'>Your PHP version is " . PHP_VERSION . '</span>';
    }

    if (!extension_loaded('pdo')) {
        $error = true;
        $requirement3 = "<span class='label label-warning'>Not enabled</span>";
    } else {
        $requirement3 = "<span class='label label-success'>Enabled</span>";
    }

    if (!extension_loaded('curl')) {
        $error = true;
        $requirement4 = "<span class='label label-warning'>Not enabled</span>";
    } else {
        $requirement4 = "<span class='label label-success'>Enabled</span>";
    }

    if (!extension_loaded('openssl')) {
        $error = true;
        $requirement5 = "<span class='label label-warning'>Not enabled</span>";
    } else {
        $requirement5 = "<span class='label label-success'>Enabled</span>";
    }

    if (!extension_loaded('mbstring')) {
        $error = true;
        $requirement6 = "<span class='label label-warning'>Not enabled</span>";
    } else {
        $requirement6 = "<span class='label label-success'>Enabled</span>";
    }



    if (!extension_loaded('imap')) {
        $error = true;
        $requirement8 = "<span class='label label-warning'>Not enabled</span>";
    } else {
        $requirement8 = "<span class='label label-success'>Enabled</span>";
    }

    if (!extension_loaded('gd')) {
        $error = true;
        $requirement9 = "<span class='label label-warning'>Not enabled</span>";
    } else {
        $requirement9 = "<span class='label label-success'>Enabled</span>";
    }

    if (!extension_loaded('zip')) {
        $error = true;
        $requirement10 = "<span class='label label-warning'>Zip Extension is not enabled</span>";
    } else {
        $requirement10 = "<span class='label label-success'>Enabled</span>";
    }
    if (!ini_get('output_buffering')) {
        $error = true;
        $requirement14 = "<span class='label label-warning'>Not enabled</span>";
    } else {
        $requirement14 = "<span class='label label-success'>Enabled</span>";
    }

    if (!extension_loaded('fileinfo') OR !function_exists('mime_content_type')) {
        $error = true;
        $requirement11 = "<span class='label label-warning'>fileinfo Extension is not enabled!</span>";
    } else {
        $requirement11 = "<span class='label label-success'>Enabled</span>";
    }
    if (!extension_loaded('json')) {
        $error = true;
        $requirement12 = "<span class='label label-warning'>Json Extension is not enabled</span>";
    } else {
        $requirement12 = "<span class='label label-success'>Enabled</span>";
    }    

if(!ini_get('allow_url_fopen')){
        $error = true;
        $requirement16 = "<span class='label label-warning'>Not enabled</span>";
    } else {
        $requirement16 = "<span class='label label-success'>Enabled</span>";
    }


    if (!extension_loaded('imagick')) {
        $error = true;
        $requirement15 = "<span class='label label-warning'>Imagick Extension is not enabled</span>";
    } else {
        $requirement15 = "<span class='label label-success'>Enabled</span>";
    }

    ob_start();
    phpinfo(INFO_MODULES);
    $contents = ob_get_contents();
    ob_end_clean();
    if (strpos($contents, 'mod_rewrite') !== false && !in_array('mod_rewrite', apache_get_modules())) {
        $error = true;
        $requirement13 = "<span class='label label-warning'>mod_rewrite is not enabled!</span>";
    } else {
        $requirement13 = "<span class='label label-success'>Enabled</span>";
    }
    ?>
    <div id="wrapper">
        <div class="logo">
            <a href="#"><img src="https://s3.envato.com/files/277163266/Thumbnail.png" width="80px"></a>
        </div>
        <h1><a href="#">Grupo Pro V2 Chatroom</a> - Server Requirements</h1>
        <div id="loader">
            <span class="dot dot_1"></span>
            <span class="dot dot_2"></span>
            <span class="dot dot_3"></span>
            <span class="dot dot_4"></span>
        </div>
        <table class="table table-hover" id="requirements" style="display:none;">
            <thead>
                <tr>
                    <th>Requirements</th>
                    <th>Result</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>PHP 7.0+ </td>
                    <td><?php echo $requirement1; ?></td>
                </tr>
                <tr>
                    <td>Server Software</td>
                    <td><span class='label label-success'><?php echo $_SERVER["SERVER_SOFTWARE"]; ?></span></td>
                </tr>
                <tr>
                    <td>PDO PHP Extension</td>
                    <td><?php echo $requirement3; ?></td>
                </tr>
                <tr>
                    <td>cURL PHP Extension</td>
                    <td><?php echo $requirement4; ?></td>
                </tr>
                <tr>
                    <td>OpenSSL PHP Extension</td>
                    <td><?php echo $requirement5; ?></td>
                </tr>
                <tr>
                    <td>MBString PHP Extension</td>
                    <td><?php echo $requirement6; ?></td>
                </tr>
                <tr>
                    <td>IMAP PHP Extension</td>
                    <td><?php echo $requirement8; ?></td>
                </tr>
                <tr>
                    <td>GD PHP Extension</td>
                    <td><?php echo $requirement9; ?></td>
                </tr>
                <tr>
                    <td>Zip PHP Extension</td>
                    <td><?php echo $requirement10; ?></td>
                </tr>
                <tr>
                    <td>FileInfo PHP Extension</td>
                    <td><?php echo $requirement11; ?></td>
                </tr>
                <tr>
                    <td>Json PHP Extension</td>
                    <td><?php echo $requirement12; ?></td>
                </tr>
                <?php if (strpos(strtolower($_SERVER["SERVER_SOFTWARE"]), "apache") !== false) {
                    ?>
                    <tr>
                        <td>mod_rewrite Extension</td>
                        <td><?php echo $requirement13; ?></td>
                    </tr>
                    <tr>
                        <?php if (!file_exists(".htaccess") && file_exists("knob")) {
                            ?>
                            <td>.htaccess File</td>
                            <td><span class='label label-warning'>Not Found</span></td>
                        </tr>

                        <?php
                    }
                } ?>
                <tr>
                    <td>Imagick Extension (Optional)</td>
                    <td><?php echo $requirement15; ?></td>
                </tr>

                <tr>
                    <td>allow_url_fopen</td>
                    <td><?php echo $requirement16; ?></td>
                </tr>

                <tr>
                    <td>Output buffering</td>
                    <td><?php echo $requirement14; ?></td>
                </tr>

            </tbody>
            <tfoot>
                <tr>
                </tr>
            </tfoot>
        </table>
        <br />

    </div>

    <script>
        var loading = {
            complete: function () {
                var loading = document.getElementById("loader");
                loading.remove(loading);
            }
        };
        document.addEventListener("readystatechange", function () {
            if (document.readyState === "complete") {
                setTimeout(function() {
                    loading.complete();
                    var requirements = document.getElementById("requirements");
                    requirements.style['display'] = null;
                }, 100);
            }
        });
    </script>

</body>
</html>