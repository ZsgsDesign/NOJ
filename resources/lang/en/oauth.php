<?php

return [
    "title" => [
        "plain"         => "Social Account",
        "platform"      => ":platform Social Account",
    ],
    "action" => [
        "unbind"        => "Unbind",
        "home"          => "Home",
        "retry"         => "Retry Login",
        "login"         => "Login",
        "register"      => "Register",
        "confirm"       => "Confirm",
    ],
    "operation"         => "You already tied to the :platform account: <span class=\"text-info\"> :oauthaccount </span><br /> You can choose to unbind or go back to the homepage.",
    "duplicate"         => "The :platform account is now tied to another :appname account: <span class=\"text-danger\"> :nojaccount </span><br /> You can try logging in using :platform.",
    "success"           => "You have successfully tied up the :platform account: <span class=\"text-info\"> :oauthaccount </span><br /> You can log in to :appname later using this account.",
    "unknownerror"      => "Some weird things happened when registering your account, please contact site admin or simply retry again.",
    "accountnotfound"   => "This :platform account doesn't seem to have a :appname account, please have your account binded at first place.",
    "unbindconfirm"     => "You are trying to unbind the following two: <br /> Your :appname account: <span class=\"text-info\"> :nojaccount </span><br /> This :platform account: <span class=\"text-info\"> :oauthaccount </span><br /> Make your decision carefully, although you can later establish the binding again.",
    "alreadyunbind"     => "You are not tied to :platform anymore.",
    "unbindsuccess"     => "You have successfully unbind your :platform account from your :appname account.",
];
