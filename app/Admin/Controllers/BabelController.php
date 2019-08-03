<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Babel\ExtensionModel;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Illuminate\Support\Facades\Redirect;

class BabelController extends Controller
{
    /**
     * Show the Status Page.
     *
     * @return Response
     */
    public function index(Content $content)
    {
        return $content
            ->header('Babel Marketspace')
            ->description('Download and Manage your Babel Extension')
            ->row(function(Row $row) {
                $row->column(12, function(Column $column) {
                    $column->append(Self::marketspace());
                });
            });
    }

    private static function marketspace()
    {
        $extensionList=ExtensionModel::list();

        if(empty($extensionList)){
            return redirect('/admin');
        }

        return view('admin::babel.marketspace', [
            'extensionList'=>$extensionList
        ]);
    }
}
