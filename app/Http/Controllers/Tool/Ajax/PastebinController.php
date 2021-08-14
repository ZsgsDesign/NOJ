<?php

namespace App\Http\Controllers\Tool\Ajax;

use App\Models\Eloquent\Tool\Pastebin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use App\Models\ResponseModel;
use Auth;

class PastebinController extends Controller
{
    /**
     * Generate a new pastebin.
     *
     * @return Response
     */
    public function generate(Request $request)
    {
        $aval_lang=["plaintext", "json", "bat", "coffeescript", "c", "cpp", "csharp", "csp", "css", "dockerfile", "fsharp", "go", "handlebars", "haskell", "html", "ini", "java", "javascript", "less", "lua", "markdown", "msdax", "mysql", "objective-c", "pgsql", "php", "postiats", "powerquery", "powershell", "pug", "python", "r", "razor", "redis", "redshift", "ruby", "rust", "sb", "scss", "sol", "sql", "st", "swift", "typescript", "vb", "xml", "yaml", "scheme", "clojure", "shell", "perl", "azcli", "apex"];

        $request->validate([
            'syntax' => [
                'required',
                'string',
                Rule::in($aval_lang),
            ],
            'expiration' => [
                'required',
                'integer',
                Rule::in([0, 1, 7, 30]),
            ],
            'title' => 'required|string',
            'content' => 'required|string|max:102400',
        ]);

        $all_data=$request->all();
        $all_data["uid"]=Auth::user()->id;

        $ret=Pastebin::generate($all_data);

        if (is_null($ret)) {
            return ResponseModel::err(1001);
        } else {
            return ResponseModel::success(200, null, [
                "code" => $ret
            ]);
        }

    }
}
