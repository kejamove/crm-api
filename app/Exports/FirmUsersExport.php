<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Facades\Excel;

class FirmUsersExport implements FromCollection
{
    /**
    * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function collection()
    {
        return Excel::download(new FirmUsersExport, 'users.xlsx');
    }
}
