<?php

namespace App\Http\Controllers;

use App\Imports\ProvinImport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;


class SyncProvinController extends Controller
{
    public function index(): View
    {
        return view('import.index');
    }

    public function startImport(Request $request): RedirectResponse
    {
        set_time_limit(10800);

        $request->validate([
            'ihld_data' => 'required|file|mimes:csv,txt'
        ]);

        $file = $request->file('ihld_data');

        Excel::import(new ProvinImport, $file);

        return redirect()->back()->with('success', 'Data berhasil diimpor.');
    }
}
