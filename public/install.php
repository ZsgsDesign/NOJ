<?php
    if(file_exists('installed')){
        die('Sorry, NOJ has installed yet. <a href="/">Visit</a>');
    }

    if (!file_exists("../vendor/autoload.php")){
        $autoload_not_exist = true;
        $page = 0;
    }

    $ver = explode(".",phpversion());
    $too_low_version = intval($ver[0].$ver[1].$ver[2]) < 730;

    if(isset($_GET['finish'])){
        $installed = fopen('./installed','w');
        fclose($installed);
        header("Location: /");
        die();
    }

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>Installer | NOJ</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <meta name="format-detection" content="telephone=no">
        <meta name="renderer" content="webkit">
        <meta http-equiv="Cache-Control" content="no-siteapp" />
        <link rel="alternate icon" type="image/png" href="favicon.png">
        <link rel="stylesheet" href="static/css/install.min.css">
    </head>

    <body>
    <nav class="noj-navbar">
        <div class="noj-brand">
            <img src="static/img/njupt.png" height="30">
            <p>NJUPT Online Judge Installer</p>
        </div>
    </nav>

    <div class="container">
        <?php if($too_low_version) { ?>
            <paper-card class="warning-text">
                <h3>Warning</h3>
                <p>
                    Your PHP version is lower than 7.3(Recommend 7.3.6), please update. (Currently <?php echo phpversion() ?>)
                </p>
            </paper-card>
        <?php } else { ?>
            <paper-card>
                <img class="title-pic" src="static/img/noj.png">
                <div class="text">
                    Welcome to NOJ. Before getting started, we need to configure environment.<br><br>
                    You can visit <a href="https://njuptaaa.github.io/docs/#/noj/guide/deploy">NOJ Docs</a> to deploy NOJ.
                    <br> Finally, click 'finish' to enjoy.
                    <?php if(isset($autoload_not_exist)) { ?>
                        <strong style="display:block;color:red;">Sorry, ./vendor/autoload.php is not existed. Are you sure all dependencies have installed yet?</strong>
                    <?php } ?>
                    <div class="text-right p-3">
                    <a class="btn btn-primary" href="?finish=1">finish</a>
                    </div>
                </div>
            </paper-card>
        <?php } ?>
    </div>

</body>
</html>
