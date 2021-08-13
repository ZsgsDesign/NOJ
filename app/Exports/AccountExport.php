<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AccountExport implements FromArray, WithHeadings
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data=$data;
    }

    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'uid',
            'name',
            'email',
            'password',
        ];
    }

    //Export:
    //Excel::download(new AccountExport($exportData), $filename.'.xlsx');
    //Store:
    //Excel::store(new AccountExport, $filename.'.xlsx', $custom_disk);
}
