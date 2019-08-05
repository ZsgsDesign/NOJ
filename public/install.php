<?php
    if(file_exists('installed')){
        die('Sorry, NOJ has installed yet. <a href="/">Visit</a>');
    }

    $ver = explode(".",phpversion());
    $too_low_version = intval($ver[0].$ver[1].$ver[2]) < 730;

    if(!file_exists('../.env')){
        copy('../.env.example','../.env');
    }
    $env = new env('../.env');

    $page = isset($_GET['page']) ? $_GET['page'] : 0;

    if($page == 1){
        if (!file_exists("../vendor/autoload.php")){
            $autoload_not_exist = true;
            $page = 0;
        }
    }

    if($page == 1 && isset($_POST['DB_HOST'])){
        $env->set('DB_HOST',$_POST['DB_HOST'])
            ->set('DB_USERNAME',$_POST['DB_USERNAME'])
            ->set('DB_DATABASE',$_POST['DB_DATABASE'])
            ->set('DB_PORT',$_POST['DB_PORT']);
        $mysqli = @mysqli_connect($_POST['DB_HOST'], $_POST['DB_USERNAME'],
            $_POST['DB_PASSWORD'], $_POST['DB_DATABASE'], $_POST['DB_PORT']);
        if($mysqli){
            $connect_test_success = true;
            @$mysqli->close();
            $env->save();
        }else{
            $connect_test_success = false;
        }
    }

    if($page == 3){
        foreach($_POST as $key => $item){
            $env->set($key,$item);
        }
        $env->save();
    }

    if(isset($_GET['finish'])){
        $installed = fopen('./installed','w');
        fclose($installed);
        header("Location: /");
        die();
    }

    class env{
        private $setting = [];
        private $filename;
        public function __construct($filename){
            $this->filename = $filename;
            $f_env = fopen($this->filename,"r");
            $env_text = fread($f_env, filesize($this->filename));
            fclose($f_env);
            $envs = explode(PHP_EOL ,$env_text);
            foreach($envs as $item){
                if(strlen($item) > 0){
                    $key = explode('=',$item)[0];
                    $value = explode('#',explode('=',$item)[1]);
                    $this->setting[$key] = [
                        'value' => trim($value[0]),
                        'value_parsed' => htmlspecialchars(trim($value[0])),
                        'comment' => count($value) > 1 ? trim($value[1]) : "",
                        'comment_parsed' => count($value) > 1 ? htmlspecialchars(trim($value[1])) : "-",
                    ];
                }else{
                    array_push($this->setting, []);
                }
            }
        }
        public function save(){
            $f_env = fopen($this->filename,"w");
            foreach($this->setting as $key => $item){
                if(count($item) == 0){
                    fwrite($f_env, PHP_EOL);
                }else{
                    $text = $key.'='.$item['value'];
                    if(strlen($item['comment']) > 0){
                        $text = $text.'  #'.$item['comment'];
                    }
                    fwrite($f_env, $text.PHP_EOL);
                }
            }
            fclose($f_env);
        }
        public function set($key,$value){
            $this->setting[$key]['value'] = $value;
            return $this;
        }
        public function get($key){
            return $this->setting[$key]['value'];
        }
        public function getArray(){
            return $this->setting;
        }
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
                    Your PHP version is lower than 7.3(Recommend 7.3.4), please update. (Currently <?php echo phpversion() ?>)
                </p>
            </paper-card>
        <?php } else if($page == 0) { ?>
            <paper-card>
                <img class="title-pic" src="static/img/noj.png">
                <div class="text">
                    Welcome to NOJ. Before getting started, we need to configure environment.<br><br>
                    First of all, download & install dependencies through running this command at the root folder of NOJ.
                    <div class="code">
                        composer install
                    </div>
                    <div class="quote">
                        Notice: you may find this step(or others) fails with message like "func() has been disabled for security reasons", it means you need to remove restrictions on those functions, basically Laravel and Composer require proc_open and proc_get_status to work properly.
                    </div>
                    <br>Then, modify a few folders' permission to allow them write.
                    <div class="code">
                        chmod -R 775 storage/ <br>
                        chmod -R 775 bootstrap/ <br>
                        chmod -R 775 app/Http/Controllers/VirtualCrawler/ <br>
                        chmod -R 775 app/Http/Controllers/VirtualJudge/ <br>
                    </div>
                    <br> After that, click 'next' to configure database.
                    <?php if(isset($autoload_not_exist)) { ?>
                        <strong style="display:block;color:red;">Sorry, ./vendor/autoload.php is not existed. Are you sure all dependencies have installed yet?</strong>
                    <?php } ?>
                    <div class="text-right p-3">
                    <a class="btn btn-primary" href="?page=1">Next</a>
                    </div>
                </div>
            </paper-card>
        <?php } else if ($page == 1) { ?>
            <paper-card>
                <?php if(!isset($connect_test_success) || !$connect_test_success) {?>
                    <div class="text">
                        <form class="m-0" action="?page=1" method="POST">
                            <h3 class="title">Configure MySQL Database</h3>
                            <hr>
                            <div class="form-group">
                                <p style="font-weight:500;margin-bottom: 0.5rem;">Database Name</p>
                                <small id="group-name-tip" style="display:block;font-size:65%;">The name of the database you want to use with NOJ.</small>
                                <input name="DB_DATABASE" type="text" class="form-control" value="<?php echo $env->get('DB_DATABASE') ?>">
                            </div>
                            <div class="form-group">
                                <p style="font-weight:500;margin-bottom: 0.5rem;">MySQL Server Host</p>
                                <input name="DB_HOST" type="text" class="form-control" value="<?php echo $env->get('DB_HOST') ?>">
                            </div>
                            <div class="form-group">
                                <p style="font-weight:500;margin-bottom: 0.5rem;">MySQL Server Port</p>
                                <input name="DB_PORT" type="text" class="form-control" value="<?php echo $env->get('DB_PORT') ?>">
                            </div>
                            <div class="form-group">
                                <p style="font-weight:500;margin-bottom: 0.5rem;">MySQL Server Username</p>
                                <input name="DB_USERNAME" type="text" class="form-control" value="<?php echo $env->get('DB_USERNAME') ?>">
                            </div>
                            <div class="form-group">
                                <p style="font-weight:500;margin-bottom: 0.5rem;">MySQL Server Password</p>
                                <input name="DB_PASSWORD" type="password" class="form-control" value="<?php echo $env->get('DB_PASSWORD') ?>">
                            </div>
                            <?php if(isset($connect_test_success) && !$connect_test_success) { ?>
                                <strong style="display:block;color:red;">There is an error establishing a database connection: <?php echo mysqli_connect_error()?></strong>
                            <?php } ?>
                            <div class="text-right mr-3">
                                <button class="btn btn-primary" onClick="migrate">Next</button>
                            </div>
                        </form>
                    </div>
                <?php } else { ?>
                    <div class="text">
                        <h3 class="title">Database migration</h3>
                        <hr>
                        The information of database connection is valid. Now, we need to configure database, thankfully Laravel have migration already:<br>
                        <div class="code">
                            php artisan migrate
                        </div>
                        <br> After that, click 'next' to configure other misc setting.
                        <div class="text-right p-3">
                        <a class="btn btn-primary" href="?page=2">Next</a>
                        </div>
                    </div>
                <?php } ?>
            </paper-card>
        <?php } else if ($page == 2) { ?>
            <paper-card>
                <div class="text">
                    <form class="m-0" action="?page=3" method="POST">
                        <h3 class="title">Configure Environment</h3>
                        <small>To utilize advantage of NOJ comprehensive, these information is required.</small>
                        <hr>
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th scope="col">Name</th>
                                    <th scope="col">Value</th>
                                    <th scope="col">Comment</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($env->getArray() as $key => $item){ if(count($item) > 0){?>
                                    <tr>
                                        <th scope="row"><?php echo $key ?></th>
                                        <td class="p-0">
                                            <input name="<?php echo $key ?>" class="form-control" type="text" value="<?php echo $item['value_parsed'] ?>">
                                        </td>
                                        <td><?php echo $item['comment_parsed'] ?></td>
                                    </tr>
                                <?php }} ?>
                            </tbody>
                        </table>
                        <div class="text-right mr-3">
                            <button class="btn btn-primary" type="submit">Next</button>
                        </div>
                    </form>
                </div>
            </paper-card>
        <?php } else if ($page == 3) { ?>
            <paper-card>
                <div class="text">
                    Lastly, we need to configure the virtual judger and online judger<br>
                    <div class="code">
                        crontab -e<br>
                        * * * * * php /path-to-noj/artisan schedule:run<br>
                        php artisan queue:work --queue=noj,codeforces,contesthunter,poj,vijos,pta,uva,hdu,uvalive<br>
                    </div>
                    <br>Also, use this to generate a new key:
                    <div class="code">
                        php artisan key:generate
                    </div>
                    <br> NOJ's up-and-running, enjoy!
                    <div class="text-right p-3">
                        <a class="btn btn-primary" href="?finish=1">Finish</a>
                    </div>
                </div>
            </paper-card>
        <?php } ?>
    </div>

</body>
</html>
