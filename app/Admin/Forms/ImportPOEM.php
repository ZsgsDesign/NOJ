<?php

namespace App\Admin\Forms;

use App\Models\Eloquent\Problem;
use Encore\Admin\Widgets\Form;
use Illuminate\Support\MessageBag;
use Illuminate\Http\Request;
use POEM\Parser as POEMParser;
use Illuminate\Support\Facades\Storage;
use DB;

class ImportPOEM extends Form
{
    /**
     * The form title.
     *
     * @var string
     */
    public $title='Import';

    /**
     * Handle the form request.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request)
    {
        $err=function($msg) {
            $error=new MessageBag([
                'title'   => 'POEM parse failed.',
                'message' => $msg,
            ]);
            return back()->with(compact('error'));
        };
        $success_message='';

        $file=$request->file('Files');
        if (!$file->isValid()) {
            return $err('Invalid POEM files');
        }

        $path=$file->getRealPath();
        $poetryRaw=file_get_contents($path);
        $parser=new POEMParser();
        $poem=$parser->parse($poetryRaw);
        if (empty($poem)) {
            return $err('Malformed POEM files');
        }

        $success_message.="
            POEM standard : {$poem['standard']} <br />
            generator : {$poem['generator']} <br />
            url : {$poem['url']} <br />
            description : {$poem['description']} <br />
            problems: <br />
        ";

        $memory_unit=[
            'kb' => 1,
            'mb' => 1024,
            'gb' => 1024 * 1024
        ];
        $time_unit=[
            'ms' => 1,
            's' => 1000,
            'm' => 60000,
            'h' => 3600000
        ];

        $prefix='NOJ';
        $p=ProblemModel::where('pcode', 'like', $prefix.'%')->orderBy('pcode', 'desc')->select('pcode')->first();
        if (empty($p)) {
            $count=1000;
        } else {
            $count=(int) str_replace($prefix, '', $p['pcode']);
        }

        foreach ($poem['problems'] as $problem) {
            //insert problem
            $title=$problem['title'];
            $pro=[
                'pcode'        => $prefix.(++$count),
                'solved_count' => 0,
                'difficulty'   => -1,
                'file'         => 0,
                'time_limit'   => $problem['timeLimit']['value'] * $time_unit[$problem['timeLimit']['unit']],
                'memory_limit' => $problem['memoryLimit']['value'] * $memory_unit[$problem['memoryLimit']['unit']],
                'title'        => $title,
                'description'  => $problem['description'],
                'input'        => $problem['input'],
                'output'       => $problem['output'],
                'note'         => $problem['note'],
                'input_type'   => 'standard input',
                'output_type'  => 'standard output',
                'OJ'           => 1,
                'tot_score'    => $problem['extra']['totScore'],
                'markdown'     => $problem['extra']['markdown'],
                'force_raw'    => $problem['extra']['forceRaw'],
                'partial'      => $problem['extra']['partial']
            ];
            $pid=ProblemModel::insertGetId($pro);

            //migrate sample
            $sample_count=0;
            foreach ($problem['sample'] as $sample) {
                $sam=[
                    'pid'           => $pid,
                    'sample_input'  => $sample['input'],
                    'sample_output' => $sample['output'],
                ];
                $psid=DB::table('problem_sample')->insert($sam);
                $sample_count+=1;
            }

            //create test case file
            if (Storage::exists(storage_path().'/test_case/'.$pro['pcode'])) {
                Storage::deleteDirectory(storage_path().'/test_case/'.$pro['pcode']);
            }
            Storage::makeDirectory(storage_path().'/test_case/'.$pro['pcode']);
            $test_case_count=0;
            $test_case_info=[
                'spj'        => false,
                'test_cases' => []
            ];
            foreach ($problem['testCases'] as $test_case) {
                $test_case_count+=1;
                $test_case_arr=[
                    'input_name'  => "data{$test_case_count}.in",
                    'output_name' => "data{$test_case_count}.out",
                    'input_size'  => strlen($test_case['input']),
                    'output_size' => strlen($test_case['output']),
                    'stripped_output_md5' => md5(trim($test_case['output']))
                ];
                array_push($test_case_info['test_cases'], $test_case_arr);
                Storage::disk('test_case')->put('/'.$pro['pcode'].'/'.$test_case_arr['input_name'], $test_case['input']);
                Storage::disk('test_case')->put('/'.$pro['pcode'].'/'.$test_case_arr['output_name'], $test_case['output']);
            }
            Storage::disk('test_case')->put('/'.$pro['pcode'].'/info', json_encode($test_case_info));

            //migrate solutions
            $solution_count=0;
            foreach ($problem['solution'] as $solution) {
                $s=[
                    'uid' => 1,
                    'pid' => $pid,
                    'content' => '``` '.$solution['source'],
                    'audit' => 1,
                    'votes' => 0,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                DB::table('problem_solution')->insert($s);
                $solution_count+=1;
            }

            $success_message.='&nbsp;&nbsp;&nbsp;&nbsp;'.
                $pro['pcode'].': "
                '.$title.'" with
                '.$sample_count.' samples,
                '.$test_case_count.' test cases,
                '.$solution_count.' solutions
                <br />';
        }
        admin_success('Import successfully', $success_message);
        return back();
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->file('Files')->rules('required');
    }
}
