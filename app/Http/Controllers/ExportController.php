<?php

namespace App\Http\Controllers;

use App\Exports\FirmExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function exportFirmData($firmId)
    {
        return Excel::download(new FirmExport($firmId), 'firm_data.xlsx');
    }
}

