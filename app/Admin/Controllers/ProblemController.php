<?php

namespace App\Admin\Controllers;

use App\Models\Eloquent\Problem as EloquentProblemModel;
use App\Models\Eloquent\OJ as EloquentOJModel;
use App\Http\Controllers\Controller;
use App\Admin\Forms\ImportPOEM;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Support\MessageBag;
use ZipArchive;
use Illuminate\Support\Facades\Storage;

class ProblemController extends Controller
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
            ->header('Problems')
            ->description('all problems')
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
            ->header('Problem Detail')
            ->description('the detail of problems')
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
            ->header('Edit Problem')
            ->description('edit the detail of problems')
            ->body($this->form()->edit($id));
    }

    /**
     * Import interface.
     *
     * @param Content $content
     * @return Content
     */
    public function import(Content $content)
    {
        return $content
            ->header('Import New Problem')
            ->description('import a new problem from POEM or POETRY')
            ->body(new ImportPOEM());
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
            ->header('Create New Problem')
            ->description('create a new problem')
            ->body($this->form(true));
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid=new Grid(new EloquentProblemModel);
        $grid->column('pid', "ID")->sortable();
        $grid->column('pcode', "PCode")->editable();
        $grid->title("Title")->editable();
        $grid->solved_count();
        $grid->time_limit("Time/ms")->editable();
        $grid->memory_limit("Memory/kb")->editable();
        $grid->OJ();
        $grid->update_date();
        $grid->tot_score("Score");
        $grid->partial("Partial")->display(function($partial) {
            return $partial ? 'Yes' : 'No';
        });
        $grid->markdown("Markdown")->display(function($markdown) {
            return $markdown ? 'Yes' : 'No';
        });
        $grid->order_index("order")->sortable();
        $grid->filter(function(Grid\Filter $filter) {
            $filter->disableIdFilter();
            $filter->like('pcode');
            $filter->like('title');
        });

        // $grid->disableCreateButton();

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
        $show=new Show(EloquentProblemModel::findOrFail($id));
        return $show;
    }

    /**
     * Make a form builder for create view and edit.
     *
     * @return Form
     */
    protected function form($create = false)
    {
        $form=new Form(new EloquentProblemModel);
        $form->model()->makeVisible('password');
        $form->tab('Basic', function(Form $form){
            $form->text('pid')->readonly();
            $form->text('pcode')->rules('required');
            $form->text('title')->rules('required');
            $form->text('time_limit')->rules('required');
            $form->text('memory_limit')->rules('required');
            $form->simplemde('description')->rules('required');
            $form->simplemde('input');
            $form->simplemde('output');
            $form->simplemde('note');
            $form->hasMany('problemSamples', 'samples', function (Form\NestedForm $form) {
                $form->textarea('sample_input', 'sample input')->rows(3);
                $form->textarea('sample_output', 'sample output')->rows(3);
                $form->textarea('sample_note', 'sample note')->rows(3);
            });
            /* $form->table('samples', function ($table) {
                $table->textarea('sample_input', 'sample input');
                $table->textarea('sample_output', 'sample output');
                $table->textarea('sample_note', 'sample note');
            }); */
            $ojs_temp = EloquentOJModel::select('oid', 'name')->get()->all();
            $ojs = [];
            foreach($ojs_temp as $v){
                $ojs[$v->oid] = $v->name;
            }
            $form->select('oj', 'OJ')->options($ojs)->default(1)->rules('required');
            $form->select('Hide')->options([
                1 => 'yes',
                0 => 'no'
            ])->default(0)->rules('required');
            /* $form->display('update_date'); */
            /* $form->text('tot_score')->rules('required');
            $form->select('partial', 'Partial Score')->options([
                0 => "No",
                1 => "Yes"
            ])->rules('required'); */
            $form->hidden('markdown');
            $form->hidden('input_type');
            $form->hidden('output_type');
            $form->hidden('solved_count');
            $form->hidden('difficulty');
            $form->hidden('file');
            $form->file('test_case')->rules('required');
            $form->ignore(['test_case']);
        });
        /* if($create){
            $form->tools(function (Form\Tools $tools) {
                $tools->append('<a href="/'.config('admin.route.prefix').'/problems/import" class="btn btn-sm btn-success" style="margin-right:1rem"><i class="MDI file-powerpoint-box"></i>&nbsp;&nbsp;Import from file</a>');
            });
        } */
        $form->saving(function (Form $form){
            $err = function ($msg) {
                $error = new MessageBag([
                    'title'   => 'Test case file parse faild.',
                    'message' => $msg,
                ]);
                return back()->with(compact('error'));
            };
            $pcode = $form->pcode;
            $p = EloquentProblemModel::where('pcode',$pcode)->first();
            $pid = $form->pid ?? null;
            if(!empty($p) && $p->pid != $pid){
                $error = new MessageBag([
                    'title'   => 'Error occur.',
                    'message' => 'Pcode has been token',
                ]);
                return back()->with(compact('error'));
            }
            $test_case = \request()->file('test_case');
            if(!empty($test_case)){
                if($test_case->extension() != 'zip'){
                    $err('You must upload a zip file iuclude test case info and content.');
                }
                $path = $test_case->path();
                $zip = new ZipArchive;
                if($zip->open($path) !== true) {
                    $err('You must upload a zip file without encrypt and can open successfully.');
                };
                $info_content = [];
                if(($zip->getFromName('info')) === false){
                    $info_content = [
                        'spj' => false,
                        'test_cases' => []
                    ];
                    $files = [];
                    for ($i = 0; $i < $zip->numFiles; $i++) {
                        $filename = $zip->getNameIndex($i);
                        $files[] = $filename;
                    }
                    $files_in = array_filter($files, function ($filename) {
                        return strpos('.in', $filename) != -1;
                    });
                    sort($files_in);
                    $testcase_index = 1;
                    foreach($files_in as $filename_in){
                        $filename = basename($filename_in, '.in');
                        $filename_out = $filename.'.out';
                        if(($zip->getFromName($filename_out)) === false) {
                            continue;
                        }
                        $test_case_in = $zip->getFromName($filename_in);
                        $test_case_out = $zip->getFromName($filename_out);
                        $info_content['test_cases']["{$testcase_index}"] = [
                            'input_size' => strlen($test_case_in),
                            'input_name' => $filename_in,
                            'output_size' => strlen($test_case_out),
                            'output_name' => $filename_out,
                            'stripped_output_md5' => md5(utf8_encode(rtrim($test_case_out)))
                        ];
                        $testcase_index += 1;
                    }
                    $zip->addFromString('info', json_encode($info_content));
                    $zip->close();
                    //$err('The zip files must include a file named info including info of test cases, and the format can see ZsgsDesign/NOJ wiki.');
                }else{
                    $info_content = json_decode($zip->getFromName('info'),true);
                };
                $zip->open($path);
                $test_cases = $info_content['test_cases'];
                foreach($test_cases as $index => $case) {
                    if(!isset($case['input_name']) || !isset($case['output_name'])) {
                        $err("Test case index {$index}: configuration missing input/output files name.");
                    }
                    if($zip->getFromName($case['input_name']) === false || $zip->getFromName($case['output_name']) === false ) {
                        $err("Test case index {$index}: missing input/output files that record in the configuration.");
                    }
                }
                if(!empty($form->pid)){
                    $problem = EloquentProblemModel::find($form->pid);
                    if(!empty($problem)){
                        $pcode = $problem->pcode;
                    }else{
                        $pcode = $form->pcode;
                    }
                }else{
                    $pcode = $form->pcode;
                }

                if(Storage::exists(base_path().'/storage/test_case/'.$pcode)){
                    Storage::deleteDirectory(base_path().'/storage/test_case/'.$pcode);
                }
                Storage::makeDirectory(base_path().'/storage/test_case/'.$pcode);
                $zip->extractTo(base_path().'/storage/test_case/'.$pcode.'/');

            }
            $form->markdown = true;
            $form->input_type = 'standard input';
            $form->output_type = 'standard output';
            $form->solved_count = 0;
            $form->difficulty = -1;
            $form->file = 0;
        });
        return $form;
    }
}
