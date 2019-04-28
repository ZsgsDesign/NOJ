<?php

namespace App\Http\Controllers\Tool\Ajax;

use App\Models\Tool\PastebinModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\ResponseModel;
use Auth,Rule;

class PastebinController extends Controller
{
    /**
     * Generate a new pastebin.
     *
     * @return Response
     */
    public function generate()
    {
        $aval_lang=["plaintext","json","bat","coffeescript","c","cpp","csharp","csp","css","dockerfile","fsharp","go","handlebars","html","ini","java","javascript","less","lua","markdown","msdax","mysql","objective-c","pgsql","php","postiats","powerquery","powershell","pug","python","r","razor","redis","redshift","ruby","rust","sb","scss","sol","sql","st","swift","typescript","vb","xml","yaml","scheme","clojure","shell","perl","azcli","apex"];

        $request->validate([
            'syntax' => [
                'required',
                'string',
                Rule::in($aval_lang),
            ],
            'expiration' => [
                'required',
                'integer',
                Rule::in([0,1,7,30]),
            ],
            'title' => 'required|string',
            'content' => 'required|string|max:102400',
        ]);

        $all_data=$request->all();

        $lang=$all_data["lang"];
        $expire=intval($all_data["expiration"]);
        $content=$all_data["content"];
        $title=$all_data["title"];

        if($expire==0){
            $expire_time=null;
        }elseif($expire==1){
            $expire_time=date('Y-m-d', strtotime('+1 days'));
        }elseif($expire==7){
            $expire_time=date('Y-m-d', strtotime('+7 days'));
        }elseif($expire==30){
            $expire_time=date('Y-m-d', strtotime('+30 days'));
        }

        $code=generateRandStr(6);
        $ret=$pastebin->find(["code"=>$code]);
        if(empty($ret)){
            $pbid=$pastebin->create([
                "lang"=>$lang,
                "expire"=>$expire_time,
                "uid"=>$detail["uid"],
                "display_author"=>$display_author,
                "content"=>$content,
                "code"=>$code
            ]);
            SUCCESS::Catcher("创建成功",["code"=>$code]);
        }else{
            ERR::Catcher(1000);
        }

    }
}
