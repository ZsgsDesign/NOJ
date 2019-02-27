<?php
/**
 * File of Main Controller of CodeMaster
 * php version 7.2.10
 *
 * @category CodeMaster
 * @package  MainController
 * @author   John Zhang <zsgsdesign@gmail.com>
 * @license  https://github.com/ZsgsDesign/CodeMaster/blob/master/LICENSE MIT
 * @link     https://github.com/ZsgsDesign/CodeMaster/ GitHub
 */
namespace App\Http\Controllers;

use App\Models\GroupModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

/**
 * Main Controller of CodeMaster
 *
 * @category CodeMaster
 * @package  MainController
 * @author   John Zhang <zsgsdesign@gmail.com>
 * @license  https://github.com/ZsgsDesign/CodeMaster/blob/master/LICENSE MIT
 * @link     https://github.com/ZsgsDesign/CodeMaster/ GitHub
 */
class MainController extends Controller
{
    /**
     * Show the Account Login and Register Page.
     *
     * @param Request $request your web request
     *
     * @return Response
     */
    public function account(Request $request)
    {
        return Auth::check() ? redirect("/account/dashboard") : redirect("/login");
    }
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
        return view('home', [
                'page_title'=>"Home",
                'site_title'=>"CodeMaster",
                'navigation' => "Home",
                'group_notice' => $group_notice
            ]);
    }
}
