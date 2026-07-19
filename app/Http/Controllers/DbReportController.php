<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DbReportController extends Controller
{
    public function index()
    {
        $files = Storage::files('db_report');
        rsort($files);
        $latest = count($files) ? $files[0] : null;
        $report = $latest ? json_decode(Storage::get($latest), true) : null;
        return view('admin.db_report', ['report' => $report, 'path' => $latest]);
    }
}
