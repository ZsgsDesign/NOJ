<?php
namespace App\Http\Controllers\VirtualJudge\NOJ;

use App\Http\Controllers\VirtualJudge\JudgeClient;

class NOJ
{
    public static function submit($submitURL)
    {
        $token = 'abcdefg';
        $cSrc = "
            #include <stdio.h>
            int main(){
                int a, b;
                scanf(\"%d%d\", &a, &b);
                printf(\"%d\\n\", a+b);
                return 0;
            }
        ";
        $judgeClient = new JudgeClient($token, $submitURL);
        return $judgeClient->judge($cSrc, 'c', 'normal', [
            'output' => true
        ]);
    }
}
