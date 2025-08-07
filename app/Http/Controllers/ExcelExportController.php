<?php

namespace App\Http\Controllers;

use App\Exports\RekapPresensiExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExcelExportController extends Controller
{
    public function export(Request $request)
    {
        $filters = $request->query('filters', []);
        
        $processedFilters = [];
        foreach ($filters as $key => $filterData) {
            $value = $filterData['value'] ?? null;
            if ($value !== null) {
                $processedFilters[$key] = $value;
            }
        }
        
        $export = new RekapPresensiExport($processedFilters);
        $filename = 'rekap_presensi_' . now()->format('Y-m-d') . '.xlsx';
        
        return Excel::download($export, $filename);
    }
}