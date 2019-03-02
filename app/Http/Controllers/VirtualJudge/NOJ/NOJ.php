<?php
namespace App\Http\Controllers\VirtualJudge\NOJ;

use App\Http\Controllers\VirtualJudge\JudgeClient;

class NOJ
{
    public static function submit()
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
        $judgeClient = new JudgeClient($token, 'http://127.0.0.1:8090');
        return $judgeClient->judge($cSrc, 'c', 'normal', [
            'output' => true
        ]);
    }
}
