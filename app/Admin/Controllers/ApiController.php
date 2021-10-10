<?php

namespace App\Admin\Controllers;

use App\Models\Eloquent\User;
use App\Models\Eloquent\Problem;
use Encore\Admin\Controllers\AdminController;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class ApiController extends AdminController
{
    protected function paginate($items, $perPage = 15)
    {
        $currentPage = Paginator::resolveCurrentPage();
        $offSet = ($currentPage * $perPage) - $perPage;
        $itemsForCurrentPage = array_slice($items, $offSet, $perPage, true);
        $paginator = new LengthAwarePaginator($itemsForCurrentPage, count($items), $perPage, $currentPage, ['path' => Paginator::resolveCurrentPath()]);
        return $paginator;
    }

    public function problems()
    {
        $q = request()->q;

        return $this->paginate(Problem::like('pcode', $q)->orLike('title', $q)->orderBy('pcode', 'asc')->get()->values()->transform(function ($problem) {
            return [
                'id' => $problem->pid,
                'text' => $problem->readable_name,
            ];
        })->toArray());
    }

    public function users()
    {
        $q = request()->q;

        return $this->paginate(User::like('name', $q)->orLike('email', $q)->orderBy('id', 'asc')->get()->values()->transform(function ($user) {
            return [
                'id' => $user->id,
                'text' => $user->readable_name,
            ];
        })->toArray());
    }
}
