<?php

namespace App\Exports;

// use Maatwebsite\Excel\Concerns\FromCollection;

use Facade\FlareClient\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class UsersDataExport implements FromView, ShouldAutoSize
{
    use Exportable;
    public function __construct()
    {
        $this->users = User::all();
    }

    public function view(): View
    {
        return view('pdf.usersdetails',[
            'users' => $this->users
        ]);
    }
}
