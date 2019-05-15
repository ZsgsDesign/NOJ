<?php
/**
 * File of Main Controller of NOJ
 * php version 7.2.10
 *
 * @category NOJ
 * @package  MainController
 * @author   John Zhang <zsgsdesign@gmail.com>
 * @license  https://github.com/ZsgsDesign/NOJ/blob/master/LICENSE MIT
 * @link     https://github.com/ZsgsDesign/NOJ/ GitHub
 */
namespace App\Http\Controllers;

use App\Models\GroupModel;
use App\Models\ProblemModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Log;

/**
 * Main Controller of NOJ
 *
 * @category NOJ
 * @package  MainController
 * @author   John Zhang <zsgsdesign@gmail.com>
 * @license  https://github.com/ZsgsDesign/NOJ/blob/master/LICENSE MIT
 * @link     https://github.com/ZsgsDesign/NOJ/ GitHub
 */
class MainController extends Controller
{
    /**
     * Show the Home Page.
     *
     * @param Request $request your web request
     *
     * @return Response
     */
    public function home(Request $request)
    {
        $groupModel=new GroupModel();
        $group_notice=$groupModel->groupNotice(1);
        $problem=new ProblemModel();
        $ojs=$problem->ojs();
        Log::debug("User Viewed Home!");
        return view('home', [
                'page_title'=>"Home",
                'site_title'=>"NOJ",
                'navigation' => "Home",
                'group_notice' => $group_notice,
                'ojs' => $ojs
            ]);
    }
}
