<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Http\Controllers\Controller;

class SubmitController extends Controller
{
    public $ret=[];

    /**
     * Initial
     *
     * @return Response
     */
    public function __construct()
    {
        $this->ret['statue']='OK';
        $this->ret['solution']="";
        $this->validate_solution();
        if($this->ret['statue']=='OK')
        {
            set_time_limit(0);

            $sub=array(
            'TIME'=>'0',
            'Verdict'=>'Waiting',
            'Soultion'=>'',
            'Language'=>'',
            'submission_date'=>time(),
            'memory'=>'0',
            'user_Handle'=>$_COOKIE['user_handle'],
            'Problem_id'=>'',
            );
            $curl =new postsoutionController($sub,$_POST['oj']);

            // insert submission

            $submission = new Submission();


        }
        echo json_encode($this->ret);
    }

    /**
     * Validate whether the solution is legal.
     *
     * @return Response
     */
    private function validate_solution()
    {
        if(!isset($_POST['solution']))
        {$this->ret['statue']='NOT';return;}
        $solution=trim($_POST['solution']);
        if(strlen($solution)==0)
        {
            $this->ret['statue']='NOT';
            $this->ret['solution']="solution must be filled";
            return;
        }
        $f = fopen("cookie/file.txt", "w") or die("Unable to open file!");
        fwrite($f,$solution);
        fclose($f);
        $size=filesize('cookie/file.txt');
        if($size>100*1000)
        {
            $this->ret['statue']='NOT';
            $this->ret['solution']="solution length is too big";
            return;
        }
    }
}
