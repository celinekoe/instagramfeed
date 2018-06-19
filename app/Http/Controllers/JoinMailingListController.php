<?php

namespace App\Http\Controllers;

use App\Models\Email;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class JoinMailingListController extends Controller
{
    public function index(Request $request)
    {
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=file.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );
    
        $emails = Email::all();
        $columns = array('Email', 'Created At', 'Updated At');
    
        $callback = function() use ($emails, $columns)
        {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
    
            foreach($emails as $email) {
                fputcsv($file, array($email->email, $email->created_at, $email->updated_at));
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
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
