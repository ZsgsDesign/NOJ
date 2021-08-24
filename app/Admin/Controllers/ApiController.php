<?php

namespace App\Admin\Controllers;

use App\Models\Eloquent\Problem;
use Encore\Admin\Controllers\AdminController;

class ApiController extends AdminController
{
    public function problems()
    {
        $q = request()->q;
        return Problem::where('pcode', 'like', "%$q%")->paginate(null, ['pid as id', 'pcode as text']);
    }
}
