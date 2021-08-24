<?php

namespace App\Admin\Controllers;

use App\Models\Eloquent\User;
use App\Models\Eloquent\Problem;
use Encore\Admin\Controllers\AdminController;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class ApiController extends AdminController
{
    protected function paginate($items, $perPage = 15, $pageStart = 1)
    {
        $offSet = ($pageStart * $perPage) - $perPage;
        $itemsForCurrentPage = array_slice($items, $offSet, $perPage, true);
        $paginator = new LengthAwarePaginator($itemsForCurrentPage, count($items), $perPage, Paginator::resolveCurrentPage(), ['path' => Paginator::resolveCurrentPath()]);
        return $paginator;
    }

    public function problems()
    {
        $q = request()->q;

        return $this->paginate(Problem::orderBy('pcode', 'asc')->get()->filter(function ($problem) use ($q) {
            return stripos($problem->readable_name, $q) !== false;
        })->values()->transform(function ($problem) {
            return [
                'id' => $problem->pid,
                'text' => $problem->readable_name,
            ];
        })->toArray());
    }

    public function users()
    {
        $q = request()->q;

        return $this->paginate(User::get()->filter(function ($user) use ($q) {
            return stripos($user->readable_name, $q) !== false;
        })->values()->transform(function ($user) {
            return [
                'id' => $user->id,
                'text' => $user->readable_name,
            ];
        })->toArray());
    }
}
