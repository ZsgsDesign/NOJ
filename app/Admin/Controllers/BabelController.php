<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Babel\ExtensionModel;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

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

    /**
     * Show the Extension Update Page.
     *
     * @return Response
     */
    public function update($extension, Content $content)
    {
        return $this->execute('update', $extension, $content);
    }

    /**
     * Show the Extension Install Page.
     *
     * @return Response
     */
    public function install($extension, Content $content)
    {
        return $this->execute('install', $extension, $content);
    }

    public function execute($command, $extension, Content $content)
    {
        return $content
            ->header(Str::title($command)." $extension")
            ->row(function(Row $row) use ($extension) {
                $row->column(12, function(Column $column) use ($extension) {
                    $column->append(Self::executingView($extension));
                });
            });
    }

    public function updateExtension($extension, Content $content)
    {
        $this->operateExtension('update', $extension, $content);
    }

    public function installExtension($extension, Content $content)
    {
        $this->operateExtension('install', $extension, $content);
    }

    public function operateExtension($command, $extension, Content $content)
    {
        self::executeArtisan("babel:$command $extension --no-interaction");
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

        if (empty($extensionList)) {
            return view('admin::babel.empty');
        }

        return view('admin::babel.marketspace', [
            'extensionList'=>$extensionList
        ]);
    }

    private static function marketspaceDetailView($code)
    {
        $details=ExtensionModel::remoteDetail($code);

        if (empty($details)) {
            return view('admin::babel.empty');
        }

        return view('admin::babel.detail', [
            'details'=>$details
        ]);
    }

    private static function executingView($extension)
    {
        $details=ExtensionModel::remoteDetail($extension);

        if (empty($details)) {
            return view('admin::babel.empty');
        }

        return view('admin::babel.execute', [
            'extension'=>$extension
        ]);
    }

    private static function executeArtisan($command)
    {
        $fp=popen('php "'.base_path('artisan').'" '.$command, "r");
        while ($b=fgets($fp, 2048)) {
            echo str_pad(json_encode([
                "ret"=>200,
                "desc"=>"Succeed",
                "data"=>[
                    "message"=>$b
                ]
            ])."\n", 4096);
            @ob_flush();
            flush();
        }

        pclose($fp);
    }
}
