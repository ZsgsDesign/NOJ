<?php

namespace App\Admin\Controllers;

use App\Models\Eloquent\Submission;
use App\Models\Eloquent\Contest;
use App\Models\Eloquent\Judger;
use App\Models\Eloquent\Compiler;
use App\Models\Eloquent\User;
use App\Models\Eloquent\Problem;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class SubmissionController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header(__('admin.submissions.index.header'))
            ->description(__('admin.submissions.index.description'))
            ->body($this->grid()->render());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header(__('admin.submissions.show.header'))
            ->description(__('admin.submissions.show.description'))
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header(__('admin.submissions.edit.header'))
            ->description(__('admin.submissions.edit.description'))
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header(__('admin.submissions.create.header'))
            ->description(__('admin.submissions.create.description'))
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid=new Grid(new Submission);
        $grid->column('sid', "ID")->sortable();
        $grid->column("time", __('admin.submissions.time'))->display(function($time) {
            return __('admin.submissions.timeFormat', ['time'=>$time]);
        });
        $grid->column("memory", __('admin.submissions.memory'))->display(function($memory) {
            return __('admin.submissions.memoryFormat', ['memory'=>$memory]);
        });
        $grid->column('verdict', __('admin.submissions.verdict'))->display(function($verdict) {
            return '<i class="fa fa-circle '.$this->color.'"></i> '.$verdict;
        });
        $grid->column("language", __('admin.submissions.language'));
        $grid->column("submission_date", __('admin.submissions.submission_date'))->display(function($submission_date) {
            return date("Y-m-d H:i:s", $submission_date);
        });
        $grid->column("user_name", __('admin.submissions.user_name'))->display(function() {
            return $this->user->readable_name;
        });
        $grid->column("contest_name", __('admin.submissions.contest_name'))->display(function() {
            if (!is_null($this->contest)) {
                return $this->contest->name;
            }
        });
        $grid->column("readable_name", __('admin.submissions.readable_name'))->display(function() {
            return $this->problem->readable_name;
        });
        $grid->column("judger_name", __('admin.submissions.judger_name'))->display(function() {
            return $this->judger_name;
        });
        $grid->column("share", __('admin.submissions.share'))->switch();
        $grid->column("parsed_score", __('admin.submissions.parsed_score'))->display(function() {
            return $this->parsed_score;
        });
        $grid->filter(function(Grid\Filter $filter) {
            $filter->column(6, function($filter) {
                $filter->like('verdict', __('admin.submissions.verdict'));
            });
            $filter->column(6, function($filter) {
                $filter->equal('cid', __('admin.submissions.cid'))->select(Contest::all()->pluck('name', 'cid'));
                $filter->equal('uid', __('admin.submissions.uid'))->select(function($id) {
                    $user=User::find($id);
                    if ($user) {
                        return [$user->id => $user->readable_name];
                    }
                })->config('minimumInputLength', 4)->ajax(route('admin.api.users'));
                $filter->equal('pid', __('admin.submissions.pid'))->select(function($pid) {
                    $problem=Problem::find($pid);
                    if ($problem) {
                        return [$problem->pid => $problem->readable_name];
                    }
                })->config('minimumInputLength', 4)->ajax(route('admin.api.problems'));
                $filter->equal('share', __('admin.submissions.share'))->select([
                    0 => __('admin.submissions.disableshare'),
                    1 => __('admin.submissions.enableshare')
                ]);
            });
        });
        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show=new Show(Submission::findOrFail($id));
        $show->sid('SID');
        $show->time(__('admin.submissions.time'));
        $show->memory(__('admin.submissions.memory'));
        $show->verdict(__('admin.submissions.verdict'));
        $show->language(__('admin.submissions.language'));
        $show->submission_date(__('admin.submissions.submission_date'));
        $show->remote_id(__('admin.submissions.remote_id'));
        $this->codify($show->solution(__('admin.submissions.solution')), $show->getModel()->compiler->lang);
        if (!blank($show->getModel()->compile_info)) {
            $this->codify($show->compile_info());
        }
        $show->uid(__('admin.submissions.uid'));
        $show->pid(__('admin.submissions.pid'));
        $show->cid(__('admin.submissions.cid'));
        $show->jid(__('admin.submissions.jid'));
        $show->coid(__('admin.submissions.coid'));
        $show->vcid(__('admin.submissions.vcid'));
        $show->score(__('admin.submissions.parsed_score'));
        $show->share(__('admin.submissions.share'))->using([__('admin.submissions.disableshare'), __('admin.submissions.enableshare')]);
        return $show;
    }

    private function codify($field, $lang=null)
    {
        $field->unescape()->as(function($value) use ($field, $lang) {
            $field->border=false;
            $hash=md5($value);
            if (blank($value)) {
                $value=" ";
            }
            return "
                <style>
                #x$hash {
                    background: #ffffff;
                    border-top-left-radius: 0;
                    border-top-right-radius: 0;
                    border-bottom-right-radius: 3px;
                    border-bottom-left-radius: 3px;
                    padding: 10px;
                    border: 1px solid #d2d6de;
                }
                #x$hash code {
                    background: #ffffff;
                }
                </style>
                <pre id='x$hash'><code class='$lang'>".htmlspecialchars($value)."</code></pre>
                <script>
                    try{
                        hljs.highlightElement(document.querySelector('#x$hash code'));
                    }catch(err){
                        window.addEventListener('load', function(){hljs.highlightElement(document.querySelector('#x$hash code'));});
                    }
                </script>
            ";
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form=new Form(new Submission);
        $form->tab('Basic', function(Form $form) {
            $form->display('sid', 'SID');
            $form->text('time', __('admin.submissions.time'))->rules('required');
            $form->text('memory', __('admin.submissions.memory'))->rules('required');
            $form->text('verdict', __('admin.submissions.verdict'))->rules('required');
            $form->text('color', __('admin.submissions.color'))->rules('required');
            $form->textarea('language', __('admin.submissions.language'))->rules('required');
            $form->display('submission_date', __('admin.submissions.submission_date'));
            $form->select('uid', __('admin.submissions.uid'))->options(function($id) {
                $user=User::find($id);
                if ($user) {
                    return [$user->id => $user->readable_name];
                }
            })->config('minimumInputLength', 4)->ajax(route('admin.api.users'))->required();
            $form->select('cid', __('admin.submissions.cid'))->options(Contest::all()->pluck('name', 'cid'));
            $form->select('pid', __('admin.submissions.pid'))->options(function($pid) {
                $problem=Problem::find($pid);
                if ($problem) {
                    return [$problem->pid => $problem->readable_name];
                }
            })->config('minimumInputLength', 4)->ajax(route('admin.api.problems'))->required();
            $form->select('jid', __('admin.submissions.jid'))->options(Judger::all()->pluck('readable_name', 'jid'));
            $form->select('coid', __('admin.submissions.coid'))->options(Compiler::all()->pluck('readable_name', 'coid'))->rules('required');
            $form->number('score', __('admin.submissions.rawscore'))->rules('required');
            $form->select('share', __('admin.submissions.share'))->options([
                0 => __('admin.submissions.disableshare'),
                1 => __('admin.submissions.enableshare')
            ])->default(0)->rules('required');
        });
        return $form;
    }
}
