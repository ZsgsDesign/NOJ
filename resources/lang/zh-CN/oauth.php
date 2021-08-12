<?php

return [
    "title" => [
        "plain"         => "社交账号",
        "platform"      => ":platform 社交账号",
    ],
    "action" => [
        "unbind"        => "解绑",
        "home"          => "主页",
        "retry"         => "重试登录",
        "login"         => "登录",
        "register"      => "注册",
        "confirm"       => "确定",
    ],
    "operation"         => "您已绑定 :platform 账号： <span class=\"text-info\"> :oauthaccount </span><br /> 您可以选择解绑或返回首页。",
    "duplicate"         => "此 :platform 账号目前绑定了另一个 :appname 账号： <span class=\"text-danger\"> :nojaccount </span><br /> 您可尝试使用 :platform 登录。",
    "success"           => "您已成功绑定了 :platform 账号： <span class=\"text-info\"> :oauthaccount </span><br /> 您可在稍后使用该账号登录 :appname 。",
    "unknownerror"      => "在账号创建过程中发生了未知错误，请联系站点管理员或更简单的，再次尝试。",
    "accountnotfound"   => "此 :platform 账号并未绑定任何 :appname 账号，请在设置中首先绑定社交平台到您的账号。",
    "unbindconfirm"     => "您正在尝试解绑下列两个账号： <br /> 您的 :appname 账号： <span class=\"text-info\"> :nojaccount </span><br /> 此 :platform 账号： <span class=\"text-info\"> :oauthaccount </span><br /> 请谨慎选择，尽管稍后您可再次绑定账号。",
    "alreadyunbind"     => "您当前未绑定 :platform 账号。",
    "unbindsuccess"     => "您已经成功解绑您的 :platform 账号与您的 :appname 账号之间的关联。",
];
