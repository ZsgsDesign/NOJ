<?php

namespace App\Admin\Controllers;

use App\Models\ContestModel;
use App\Models\Eloquent\Contest as EloquentContestModel;
use App\Http\Controllers\Controller;
use App\Models\Eloquent\Problem;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class ContestController extends Controller
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
            ->header('Contests')
            ->description('all contests')
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
            ->header('Contest Detail')
            ->description('the detail of contests')
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
            ->header('Edit Contest')
            ->description('edit the detail of contests')
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
            ->header('Create New Contest')
            ->description('create a new contest')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid=new Grid(new EloquentContestModel);
        $grid->column('cid', "ID")->sortable();
        $grid->column("gid", "Group");
        $grid->name("Name")->editable();
        $grid->verified("Verified")->display(function($verified) {
            return $verified ? "Yes" : "No";
        });
        $grid->rated("Rated")->display(function($rated) {
            return $rated ? "Yes" : "No";
        });
        $grid->anticheated("AntiCheated")->display(function($anticheated) {
            return $anticheated ? "Yes" : "No";
        });
        $grid->featured("Featured")->display(function($featured) {
            return $featured ? "Yes" : "No";
        });
        $grid->column("rule", "Rule");
        $grid->begin_time("Begins");
        $grid->end_time("Ends");
        $grid->public("Public")->display(function($public) {
            return $public ? "Yes" : "No";
        });
        $grid->column("registration", "Registration")->display(function($registration) {
            return $registration ? "Required" : "Free";
        });
        $grid->registration_due("Registration Due");
        $grid->filter(function(Grid\Filter $filter) {
            $filter->equal('gid');
            $filter->like('name');
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
        $show=new Show(EloquentContestModel::findOrFail($id));
        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form=new Form(new EloquentContestModel);
//        $form->model()->makeVisible('password');
        $form->tab('Basic', function(Form $form) {
//            $form->display('cid');
            // $form->number('gid')->rules('required');
            $form->text("gid","group_id")->required();
            $form->text("assign_uid","比赛管理员Id");
            $form->text('name', '考试名称')->required();
            $form->hidden('verified', '官方考试')->default(true);
            $form->hidden('rated', '评级考试')->default(0);
            $form->hidden('is_rated',"是否是评级考试")->default(0);
            $form->hidden('anticheated', '反作弊')->default(true);
            $form->hidden('practice', '训练赛')->default(0);
            $form->switch('featured', '重点考试')->default(true);
            $form->switch('desktop', '使用NOJ Desktop桌面客户端（实验性）')->default(false);
            $form->hidden('pdf', '提供PDF试题档')->default(0);
            $form->simplemde('description', '比赛简介')->required();
            $form->select('rule', '赛制')->options([
                5 => "机试"
            ])->default(5)->required();
            $form->datetimeRange('begin_time', 'end_time', '比赛时间段')->required();
            $form->hidden('public', '公开比赛')->default(true);
            $form->switch('registration', '限制考试参与者')->default(true);
            $form->datetime('registration_due', '限制考试报名截止时间')->default('1970-01-01 00:00:00');
            $form->select('registant_type', '限制考试方式')->options([
                0 => "不限制任何人报名",
                2 => "不允许任何人报名"
            ])->default(2);
            $form->hidden('froze_length', '封榜时间（秒）')->default(0)->required();
            $form->select('status_visibility', '状态可见性')->options([
                0 => "评测状态将会保持不可见"
            ])->default(0)->required();
            $form->hidden('audit_status', '审核状态')->default(true);
            $form->text('custom_title', '自定义考试导航标题');
            $form->image('custom_icon', '自定义考试导航图标')->uniqueName()->move("static/img/contest");
            $form->image('img', '考试封面图')->uniqueName()->move("static/img/contest");
            $form->hasMany('problems', '考试题目', function (Form\NestedForm $form) {
                $form->number('number', '编号')->default(1)->required();
                $ncodeArr=[];
                foreach(range('A', 'Z') as $alpha){
                    $ncodeArr[$alpha]=$alpha;
                }
                $form->select('ncode', '字母题号')->options($ncodeArr)->default("A")->required();
                $form->select('pid', '题目')->options(Problem::all()->pluck('pcode', 'pid'))->required();
                $form->text('alias', '题目别名');
                $form->number('points', '题目分值')->default(100)->required();
            });
        });
        return $form;
    }
}
