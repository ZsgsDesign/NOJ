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

use App\Models\AnnouncementModel;
use App\Models\ProblemModel;
use App\Models\CarouselModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Log;
use Redirect;
use Cache;
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
        $announcementModel=new AnnouncementModel();
        $announcements=$announcementModel->fetch();
        $problem=new ProblemModel();
        $ojs=$problem->ojs();
        // Log::debug(["info"=>"User Viewed Home!"]);
        return view('home', [
            'page_title'=>"Home",
            'site_title'=>config("app.name"),
            'navigation' => "Home",
            'announcements' => $announcements,
            'ojs' => $ojs,
            'carousel' => CarouselModel::list()
        ]);
    }


    public function oldRedirect(Request $request)
    {
        $all_data=$request->all();
        $method=isset($all_data["method"])?$all_data["method"]:null;
        $id=isset($all_data["id"])?$all_data["id"]:null;
        if($method=="showdetail" && !is_null($id)){
            $problemModel=new ProblemModel();
            return ($problemModel->existPCode("NOJ$id"))?Redirect::route('problem.detail', ['pcode' => "NOJ$id"]):Redirect::route('problem_index');
        }
        return Redirect::route('home');
    }
}
