<?php

namespace App\Http\Controllers;

use App\Exports\EmailsExport;
use App\Models\Email;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class JoinMailingListController extends Controller
{
    public function index(Request $request)
    {
        return Excel::download(new EmailsExport, 'emails.xlsx');
    }

    public function update(Request $request)
    {
        $email = $request->email;
        if ($email !== null && $email !== "") {
            DB::table('emails')->insert([
                'email' => $email,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);
        }
    }

}
