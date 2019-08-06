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
     * Show the MarketSpace Page.
     *
     * @return Response
     */
    public function index(Content $content)
    {
        return redirect()->route('admin.babel.installed');
    }

    /**
     * Show the Installed Page.
     *
     * @return Response
     */
    public function installed(Content $content)
    {
        return $content
            ->header('Installed Babel Extension')
            ->description('Manage your installed Babel Extension')
            ->row(function(Row $row) {
                $row->column(12, function(Column $column) {
                    $column->append(Self::installedView());
                });
            });
    }

    /**
     * Show the MarketSpace Page.
     *
     * @return Response
     */
    public function marketspace(Content $content)
    {
        return $content
            ->header('Babel Marketspace')
            ->description('Find extensions from marketspace')
            ->row(function(Row $row) {
                $row->column(12, function(Column $column) {
                    $column->append(Self::marketspaceView());
                });
            });
    }

    /**
     * Show the MarketSpace Detail Page.
     *
     * @return Response
     */
    public function detail($code, Content $content)
    {
        return $content
            ->header("Extension: $code")
            ->description('Details about this extension')
            ->row(function(Row $row) use ($code) {
                $row->column(12, function(Column $column) use ($code) {
                    $column->append(Self::marketspaceDetailView($code));
                });
            });
    }

    private static function installedView()
    {
        $installedExtensionList=ExtensionModel::localList();

        return view('admin::babel.installed', [
            'installedExtensionList'=>$installedExtensionList
        ]);
    }

    private static function marketspaceView()
    {
        $extensionList=ExtensionModel::list();

        if(empty($extensionList)){
            return view('admin::babel.empty');
        }

        return view('admin::babel.marketspace', [
            'extensionList'=>$extensionList
        ]);
    }

    private static function marketspaceDetailView($code)
    {
        $details=ExtensionModel::remoteDetail($code);

        if(empty($details)){
            return view('admin::babel.empty');
        }

        return view('admin::babel.detail', [
            'details'=>$details
        ]);
    }
}
