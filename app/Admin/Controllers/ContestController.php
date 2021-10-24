<?php

namespace App\Admin\Controllers;

use App\Models\Eloquent\Contest;
use App\Models\Eloquent\Problem;
use App\Models\Eloquent\Group;
use App\Models\Eloquent\User;
use App\Http\Controllers\Controller;
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
        $grid=new Grid(new Contest);
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
        $show=new Show(Contest::findOrFail($id));
        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form=new Form(new Contest);
        $form->tab('Basic', function(Form $form) {
            $form->select('gid', 'Contest Creator Group')->options(Group::all()->pluck('name', 'gid'))->required();
            $form->select('assign_uid', 'Contest Assign User')->options(function($id) {
                $user=User::find($id);
                if ($user) {
                    return [$user->id => $user->readable_name];
                }
            })->config('minimumInputLength', 4)->ajax(route('admin.api.users'))->required();
            $form->text('name', 'Contest Name')->required();

            $form->divider();

            $form->select('public', 'Contest Publicity')->options([
                0 => "Private",
                1 => "Public"
            ])->when(0, function(Form $form) {
                $form->switch('practice', 'Practice Contest')->default(false);
            })->when(1, function(Form $form) {
                $form->switch('verified', 'Verified Contest')->default(true);
                $form->switch('rated', 'Rated Contest')->default(false);
                $form->switch('anticheated', 'AntiCheated Contest')->default(true);
                $form->switch('featured', 'Featured Contest')->default(true);
            })->default(0)->required();
            $form->switch('desktop', 'Enable NOJ Desktop (Experimental)')->default(false);
            $form->hidden('is_rated', "is_rated")->default(0);
            $form->switch('pdf', 'Provide PDF')->default(false);

            $form->divider();

            $form->simplemde('description', 'Contest Description')->required();
            $form->select('rule', 'Contest Rule')->options([
                1 => "ICPC",
                2 => "IOI"
            ])->default(0)->required();
            $form->datetimeRange('begin_time', 'end_time', 'Contest Time Arrangement')->required();

            $form->divider();

            $form->select('registration', 'Require Registration')->options([
                0 => "No",
                1 => "Yes"
            ])->default(0)->required();
            $form->datetime('registration_due', 'Registration Deadline')->default('1970-01-01 00:00:00');
            $form->select('registant_type', 'Registrant Type')->options([
                0 => "Don't Allow Anyone to Register",
                1 => "Only Same Group Can Register",
                2 => "Everyone Can Register",
            ])->default(2);

            $form->divider();

            $form->number('froze_length', 'Forzen Time (Seconds)')->default(0)->required();
            $form->select('status_visibility', 'Status Visibility')->options([
                0 => "Cannot View Any Status",
                1 => "Can Only View Own Status",
                2 => "Can View Everyone Status"
            ])->default(1)->required();
            $form->switch('audit_status', 'Audit Status')->default(true);
            $form->text('custom_title', 'Custom Navigation Title');
            $form->image('custom_icon', 'Custom Navigation Icon')->uniqueName()->move("static/img/contest");
            $form->image('img', 'Contest Focus Image')->uniqueName()->move("static/img/contest");
            $form->hasMany('problems', 'Contest Problems', function(Form\NestedForm $form) {
                $form->number('number', 'Problem Numerical Index')->default(1)->required();
                $ncodeArr=[];
                foreach (range('A', 'Z') as $alpha) {
                    $ncodeArr[$alpha]=$alpha;
                }
                $form->select('ncode', 'Problem Alphabetical Index')->options($ncodeArr)->default("A")->required();
                $form->select('pid', 'Problem')->options(function($pid) {
                    $problem=Problem::find($pid);
                    if ($problem) {
                        return [$problem->pid => $problem->readable_name];
                    }
                })->config('minimumInputLength', 4)->ajax(route('admin.api.problems'))->required();
                $form->text('alias', 'Problem Alias Title');
                $form->number('points', 'Points Value')->default(100)->required();
            });
        });

        $form->saving(function (Form $form) {
            if($form->public) {
                $form->practice = 0;
            } else {
                $form->verified = 0;
                $form->rated = 0;
                $form->anticheated = 0;
                $form->featured = 0;
            }
        });

        return $form;
    }
}
