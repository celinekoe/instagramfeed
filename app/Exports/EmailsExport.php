<?php

namespace App\Exports;

use App\Models\Email;
use Maatwebsite\Excel\Concerns\FromCollection;

class EmailsExport implements FromCollection
{
    public function collection()
    {
        return Email::all();
    }
}