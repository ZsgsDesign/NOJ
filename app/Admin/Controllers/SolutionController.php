<?php

namespace App\Admin\Controllers;

use App\Models\Eloquent\ProblemSolution;
use App\Models\Eloquent\Problem;
use App\Models\Eloquent\User;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class SolutionController extends Controller
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
            ->header('Solutions')
            ->description('all solutions')
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
            ->header('Solution Detail')
            ->description('the detail of solutions')
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
            ->header('Edit Solutions')
            ->description('edit the detail of solutions')
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
            ->header('Create New Solution')
            ->description('create a new solution')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid=new Grid(new ProblemSolution);
        $grid->psoid("ID")->sortable();
        $grid->uid("Uid")->editable();
        $grid->pid("Pid")->editable();
        $grid->content("Content")->display(function($content) {
            $contentParsed=clean(convertMarkdownToHtml($content));
            return "$contentParsed";
        });
        $grid->audit("Audit")->editable('select', [0 => 'Waiting', 1 => 'Accepted', 2 => 'Declined']);
        $grid->votes("Votes");
        $grid->filter(function(Grid\Filter $filter) {
            $filter->disableIdFilter();
            $filter->equal('uid');
            $filter->equal('pid');
            $filter->equal('audit')->radio([
                ''    => 'All',
                '0'   => 'Waiting',
                '1'   => 'Accepted',
                '2'   => 'Declined',
            ]);
        });
        $grid->expandFilter();
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
        $show=new Show(ProblemSolution::findOrFail($id));
        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form=new Form(new ProblemSolution);
        $form->model()->makeVisible('password');
        $form->tab('Basic', function(Form $form) {
            $form->display("psoid");
            $form->select('uid', 'Author')->options(function($id) {
                $user=User::find($id);
                if ($user) {
                    return [$user->id => $user->readable_name];
                }
            })->config('minimumInputLength', 4)->ajax(route('admin.api.users'))->required();
            $form->select('pid', 'Problem')->options(function($pid) {
                $problem=Problem::find($pid);
                if ($problem) {
                    return [$problem->pid => $problem->readable_name];
                }
            })->config('minimumInputLength', 4)->ajax(route('admin.api.problems'))->required();
            $form->simplemde("content")->rules('required');
            $form->select("audit")->options([
                '0'   => 'Waiting',
                '1'   => 'Accepted',
                '2'   => 'Declined',
            ])->default(1)->required();
            $form->number("votes")->rules('required');
            $form->display("created_at");
            $form->display("updated_at");
        });
        return $form;
    }
}
