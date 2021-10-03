<?php

namespace App\Admin\Controllers;

use App\Models\Eloquent\Problem;
use App\Models\Eloquent\OJ;
use App\Http\Controllers\Controller;
use App\Admin\Forms\ImportPOEM;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Exception;
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
            ->description('all problems, problems managed by babel will not show here')
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
        $problem=Problem::findOrFail($id);
        if (!$problem->markdown || $problem->onlinejudge->ocode!=='noj') {
            return abort('403', 'Problem managed by BABEL cannot be accessed by Admin Portal.');
        }
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
        $problem=Problem::findOrFail($id);
        if (!$problem->markdown || $problem->onlinejudge->ocode!=='noj') {
            return abort('403', 'Problem managed by BABEL cannot be accessed by Admin Portal.');
        }
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
        $grid=new Grid(new Problem);
        $grid->model()->where([
            'markdown'=>1,
            'OJ'=>OJ::where(['ocode'=>'noj'])->first()->oid,
        ])->orderBy('pcode', 'asc');
        $grid->column('pid', "ID")->sortable();
        $grid->column('pcode', "PCode")->editable()->sortable();
        $grid->title("Title")->editable();
        $grid->solved_count();
        $grid->time_limit("Time/ms")->editable();
        $grid->memory_limit("Memory/kb")->editable();
        $grid->OJ("OJ")->display(function() {
            return $this->onlinejudge->name;
        });
        $grid->tot_score("Score");
        $grid->partial("Partial")->display(function($partial) {
            return $partial ? 'Yes' : 'No';
        });
        $grid->markdown("Markdown")->display(function($markdown) {
            return $markdown ? 'Yes' : 'No';
        });
        $grid->column('hide', 'Hide')->switch();
        $grid->update_date('Updated At');
        // $grid->order_index("order")->sortable();
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
        $show=new Show(Problem::findOrFail($id));
        return $show;
    }

    /**
     * Make a form builder for create view and edit.
     *
     * @return Form
     */
    protected function form($create=false)
    {
        $form=new Form(new Problem);
        $form->tab('Basic', function(Form $form) {
            $form->text('pid')->icon('MDI key')->readonly();
            $form->text('pcode')->icon('MDI label-black')->rules('required|alpha_dash|min:3|max:20');
            $form->text('title')->icon('MDI format-title')->rules('required');
            $form->text('time_limit')->icon('MDI timer')->default(1000)->append('MS')->rules('required');
            $form->text('memory_limit')->icon('MDI memory')->default(65536)->append('Kb')->rules('required');
            $form->simplemde('description')->rules('required');
            $form->simplemde('input');
            $form->simplemde('output');
            $form->simplemde('note');
            $form->hasMany('problemSamples', 'samples', function(Form\NestedForm $form) {
                $form->textarea('sample_input', 'sample input')->rows(3);
                $form->textarea('sample_output', 'sample output')->rows(3);
                $form->textarea('sample_note', 'sample note')->rows(3);
            });
            /* $form->table('samples', function ($table) {
                $table->textarea('sample_input', 'sample input');
                $table->textarea('sample_output', 'sample output');
                $table->textarea('sample_note', 'sample note');
            }); */
            $ojs_temp=OJ::select('oid', 'name')->get()->all();
            $ojs=[];
            foreach ($ojs_temp as $v) {
                $ojs[$v->oid]=$v->name;
            }
            $form->select('oj', 'OJ')->options($ojs)->default(1)->rules('required');
            /* $form->display('update_date'); */
            /* $form->text('tot_score')->rules('required');
            $form->select('partial', 'Partial Score')->options([
                0 => "No",
                1 => "Yes"
            ])->rules('required'); */
            $form->radio('hide', 'Hide')
                ->options([
                    0 => 'NO',
                    1 => 'YES'
                ])->default(0)->rules('required');
            $form->radio('spj', 'Use SPJ')
                ->options([
                    0 => 'NO',
                    1 => 'YES',
                ])->default(0)->rules('required');
            $form->clang('spj_src', 'SPJ Source Code');
            if ($form->isCreating()) {
                $form->chunk_file('test_case')->rules('required');
            } else {
                $form->chunk_file('test_case');
            }

            $form->ignore(['test_case']);

            //Hidden parameters

            $form->hidden('markdown');
            $form->hidden('input_type');
            $form->hidden('output_type');
            $form->hidden('solved_count');
            $form->hidden('difficulty');
            $form->hidden('partial');
            $form->hidden('tot_score');
            $form->hidden('file');
            $form->hidden('spj_lang');
            $form->hidden('spj_version');
        });
        /* if($create){
            $form->tools(function (Form\Tools $tools) {
                $tools->append('<a href="/'.config('admin.route.prefix').'/problems/import" class="btn btn-sm btn-success" style="margin-right:1rem"><i class="MDI file-powerpoint-box"></i>&nbsp;&nbsp;Import from file</a>');
            });
        } */
        $form->saving(function(Form $form) {
            $err=function($msg, $title='Test case file parse faild.') {
                $error=new MessageBag([
                    'title'   => $title,
                    'message' => $msg,
                ]);
                return back()->with(compact('error'));
            };
            $pcode=$form->pcode;
            $p=Problem::where('pcode', $pcode)->first();
            //check pcode has been token.
            $pid=$form->pid ?? null;
            if (!empty($p) && $p->pid!=$pid) {
                return $err('Pcode has been token', 'Error occur.');
            }
            //Make sure the user enters SPJ_SRc in spj problem.
            if ($form->spj && empty($form->spj_src)) {
                return $err('The SPJ problem must provide spj_src', 'create problem error');
            }

            $test_case=null;

            if (!is_null(request()->get('test_case'))) {
                $test_case=explode('http://fake.path/', request()->get('test_case'), 2)[1];
                $path=Storage::disk('temp')->path($test_case);

                if (pathinfo($path, PATHINFO_EXTENSION)!=='zip') {
                    return $err('You must upload a zip file iuclude test case info and content.');
                }

                $zip=new ZipArchive;

                if ($zip->open($path)!==true) {
                    return $err('You must upload a zip file without encrypt and can open successfully.');
                };

                //check info file. Try to generate if it does not exist.
                $info_content=[];
                if (($zip->getFromName('info'))===false) {
                    if (!$form->spj) {
                        $info_content=[
                            'spj' => false,
                            'test_cases' => []
                        ];
                        $files=[];
                        for ($i=0; $i<$zip->numFiles; $i++) {
                            $filename=$zip->getNameIndex($i);
                            $files[]=$filename;
                        }
                        $files_in=array_filter($files, function($filename) {
                            return pathinfo($filename, PATHINFO_EXTENSION)=='in';
                        });
                        sort($files_in);
                        $testcase_index=1;
                        foreach ($files_in as $filename_in) {
                            $filename=basename($filename_in, '.in');
                            $filename_out=$filename.'.out';
                            if (($zip->getFromName($filename_out))===false) {
                                continue;
                            }
                            $test_case_in=preg_replace('~(*BSR_ANYCRLF)\R~', "\n", $zip->getFromName($filename_in));
                            $test_case_out=preg_replace('~(*BSR_ANYCRLF)\R~', "\n", $zip->getFromName($filename_out));
                            $info_content['test_cases']["{$testcase_index}"]=[
                                'input_size' => strlen($test_case_in),
                                'input_name' => $filename_in,
                                'output_size' => strlen($test_case_out),
                                'output_name' => $filename_out,
                                'stripped_output_md5' => md5(utf8_encode(rtrim($test_case_out)))
                            ];
                            $testcase_index+=1;
                        }
                        if ($testcase_index==1) {
                            return $err('Cannot detect any validate testcases, please make sure they are placed under the root directory of the zip file.');
                        }
                    } else {
                        $info_content=[
                            'spj' => true,
                            'test_cases' => []
                        ];
                        $files=[];
                        for ($i=0; $i<$zip->numFiles; $i++) {
                            $filename=$zip->getNameIndex($i);
                            $files[]=$filename;
                        }
                        $files_in=array_filter($files, function($filename) {
                            return pathinfo($filename, PATHINFO_EXTENSION)=='in';
                        });
                        sort($files_in);
                        $testcase_index=1;
                        foreach ($files_in as $filename_in) {
                            $test_case_in=$zip->getFromName($filename_in);
                            $info_content['test_cases']["{$testcase_index}"]=[
                                'input_size' => strlen($test_case_in),
                                'input_name' => $filename_in
                            ];
                            $testcase_index+=1;
                        }
                        if ($testcase_index==1) {
                            return $err('Cannot detect any validate testcases, please make sure they are placed under the root directory of the zip file.');
                        }
                    }
                    $zip->addFromString('info', json_encode($info_content));
                    $zip->close();
                    //return $err('The zip files must include a file named info including info of test cases, and the format can see ZsgsDesign/NOJ wiki.');
                } else {
                    $info_content=json_decode($zip->getFromName('info'), true);
                };
                $zip->open($path);
                //If there is an INFO file, check that the contents of the file match the actual situation
                $test_cases=$info_content['test_cases'];
                foreach ($test_cases as $index => $case) {
                    if (!isset($case['input_name']) || (!$form->spj && !isset($case['output_name']))) {
                        return $err("Test case index {$index}: configuration missing input/output files name.");
                    }
                    $test_case_in=$zip->getFromName($case['input_name']);
                    if (!$form->spj) {
                        $test_case_out=$zip->getFromName($case['output_name']);
                    }
                    if ($test_case_in===false || (!$form->spj && $test_case_out===false)) {
                        return $err("Test case index {$index}: missing input/output files that record in the configuration.");
                    }
                    $zip->addFromString($case['input_name'], preg_replace('~(*BSR_ANYCRLF)\R~', "\n", $test_case_in));
                    if (!$form->spj) {
                        $zip->addFromString($case['output_name'], preg_replace('~(*BSR_ANYCRLF)\R~', "\n", $test_case_out));
                    }
                }
                $zip->close();
                $zip->open($path);
                if (!empty($form->pid)) {
                    $problem=Problem::find($form->pid);
                    if (!empty($problem)) {
                        $pcode=$problem->pcode;
                    } else {
                        $pcode=$form->pcode;
                    }
                } else {
                    $pcode=$form->pcode;
                }

                if (Storage::disk('test_case')->exists($pcode)) {
                    Storage::disk('test_case')->deleteDirectory($pcode);
                }

                Storage::disk('test_case')->makeDirectory($pcode);

                $zip->extractTo(Storage::disk('test_case')->path($pcode));

                $form->tot_score=count($info_content['test_cases']);

            }
            //Set the spj-related data
            if ($form->spj) {
                $form->spj_lang='c';
                $form->spj_version="{$form->pcode}#".time();
            }
            //Set default data
            if ($form->isCreating() && empty($test_case)) {
                $form->tot_score=0;
            }
            $form->markdown=true;
            $form->input_type='standard input';
            $form->output_type='standard output';
            $form->solved_count=0;
            $form->difficulty=-1;
            $form->partial=1;
            $form->file=0;
        });
        return $form;
    }
}
