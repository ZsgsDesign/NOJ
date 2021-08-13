<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Layout\Content;
use App\Models\Eloquent\OJ;
use App\Models\Eloquent\Problem;
use App\Models\Eloquent\Compiler;
use Encore\Admin\Widgets\Form;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Table;
use App\Babel\Babel;
use Arr;

class CodeTesterController extends Controller
{
    /**
     * Show the Testing Page.
     *
     * @return Response
     */
    public function tester(Content $content)
    {
        $content=$content->header(__('admin.tester.tester.header'));
        $content=$content->description(__('admin.tester.tester.description'));
        if (request()->isMethod('post')) {
            $content=$content->body($this->run());
        }
        return $content->body($this->form());
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $OJ=OJ::where(["ocode"=>"noj"])->get();
        $box=new Box(__('admin.tester.tester.title'));
        if (blank($OJ)) {
            $box->style('danger');
            $box->content(__('admin.tester.help.installfirst'));
            return $box;
        }
        $oid=$OJ->first()->oid;
        $box->style('success');
        $form=new Form();
        $form->select('oid', __('admin.tester.oj'))->options($OJ->pluck('name', 'oid'))->help(__('admin.tester.help.onlinejudge'))->rules('required');
        $form->select('pid', __('admin.tester.pid'))->options(Problem::where(["OJ"=>$oid])->get()->sortBy('readable_name')->pluck('readable_name', 'pid'))->rules('required');
        $form->select('coid', __('admin.tester.coid'))->options(Compiler::where(["oid"=>$oid])->get()->pluck('display_name', 'coid'))->rules('required');
        $form->textarea('solution', __('admin.tester.solution'))->rows(20)->rules('required');
        $form->action(route('admin.codetester.tester'));
        $form->fill([
            'oid'=>request()->oid,
            'pid'=>request()->pid,
            'coid'=>request()->coid,
            'solution'=>request()->solution,
        ]);
        $form->method('POST');
        $box->content($form);
        return $box;
    }

    /**
     * Running Test.
     *
     * @return Response
     */
    protected function run()
    {
        $babel=new Babel();
        request()->validate([
            'oid' => 'required|integer',
            'pid' => 'required|integer',
            'coid' => 'required|integer',
            'solution' => 'required',
        ]);
        $runner=$babel->testrun([
            'name' => 'noj',
            'pid' => request()->pid,
            'coid' => request()->coid,
            'solution' => request()->solution,
        ]);
        $verdict=$runner->verdict;
        $boxRun=new Box(__('admin.tester.tester.run'));
        $boxRun->style('info');
        $verdictData=[];
        foreach ($verdict['data'] as $v) {
            $verdictData[]=[
                $v["test_case"],
                $v["cpu_time"],
                $v["real_time"],
                $v["memory"],
                $v["signal"],
                $v["exit_code"],
                $v["error"],
                $v["result"],
            ];
        }
        $table=new Table(['Test Case', 'CPU Time(ms)', 'Real Time(ms)', 'Memory(byte)', 'Signal', 'Exit Code', 'Error', 'Result'], $verdictData);
        $output="<p>Verdict: {$verdict['verdict']}</p>";
        if (!blank($verdict['compile_info'])) {
            $output.="<p>Compiler Info:</p><pre>".htmlspecialchars($verdict['compile_info'])."</pre>";
        }
        $output.=$table->render();
        $boxRun->content($output);
        return $boxRun;
    }
}
