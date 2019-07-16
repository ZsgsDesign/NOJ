<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Events\AfterSheet;
class GroupAnalysisExport implements FromCollection, WithEvents, WithStrictNullComparison
{
    private $contest_data;
    private $member_data;
    private $config;

    public function __construct($contest_data, $member_data, $config = [])
    {
        $this->contest_data = $contest_data;
        $this->member_data = $member_data;
        $this->config = $config;
    }

    /**
    * @return array
    */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $mergeCell = ['A1:A2','B1:D1'];
                foreach ($this->contest_data as $c) {
                    array_push($mergeCell,$this->mergeCellColumnNext());
                }
                $event->sheet->getDelegate()->setMergeCells($mergeCell);
            },
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $maxium = $this->config['maxium'] ?? false;
        $percent = $this->config['percent'] ?? false;

        $row_1 = ['Member','Total','',''];
        $row_2 = ['','Rank','Solved','Penalty'];
        foreach ($this->contest_data as $contest) {
            array_push($row_1,$contest['name'],'');
            array_push($row_2,'Solved','Penalty');
        }
        $data = [$row_1,$row_2];
        foreach ($this->member_data as $member) {
            $display_name = $member['name'];
            if(!empty($member['nick_name'])){
                $display_name .= ' ('.$member['nick_name'].')';
            }
            $row = [
                $display_name,
                !empty($member['rank_ave']) ? round($member['rank_ave'],1) : '-',
                !$percent ? ($member['problem_all'] != 0 ? round( $member['solved_all'] / $member['problem_all'] * 100 , 1) : '-'). ' %'
                         : ($maxium ? $member['solved_all'] . ' / ' . $member['problem_all'] : $member['solved_all']),
                round($member['penalty']),
            ];
            foreach ($this->contest_data as $contest) {
                if(in_array($contest['cid'] , array_keys($member['contest_detial']))){
                    $contest_detial = $member['contest_detial'][$contest['cid']];
                    array_push(
                        $row,
                        !$percent ? (round($contest_detial['solved'] / $contest_detial['problems'] * 100,1) . ' %')
                                 : ($maxium ? $contest_detial['solved'].' / '.$contest_detial['problems'] : $contest_detial['solved']),
                        $contest_detial['penalty']
                    );
                }else{
                    array_push(
                        $row,
                        $percent ? '- %'
                                 : ($maxium ? '- / -' : ' - '),
                        0
                    );
                }
            }
            array_push($data,$row);
        }
        return collect($data);
    }

    private function mergeCellColumnNext(){
        static $columns = [
            [2],[3]
        ];
        $columns_str = ['',''];
        $chars = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ');
        foreach($columns as $key => &$column){
            $column[0] += 2;
            if($column[0] > 25){
                if(isset($column[1])){
                    $column[1] += 1;
                    if($column[1] > 25){
                        if(isset($column[2])){
                            $column[2] += 1;
                        }else{
                            $column[2] = 0;
                        }
                    }
                }else{
                    $column[1] = 0;
                }
            }
            $columns_str[$key] = '';
            $reverse_column = array_reverse($column);
            foreach ($reverse_column as $ord) {
                $columns_str[$key] .= $chars[$ord];
            }
            $columns_str[$key] .= '1';
        }
        return $columns_str[0].':'.$columns_str[1];
    }
}
