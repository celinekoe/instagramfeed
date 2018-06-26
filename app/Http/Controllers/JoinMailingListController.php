<?php

namespace App\Http\Controllers;

use App\Models\Email;
use Excel;
use GuzzleHttp\Client;
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
        $response_token = $request->response_token;
        $is_verified = $this->verifyResponseToken($response_token);

        $email = $request->email;
        if ($is_verified && $email !== null && $email !== "") {
            DB::table('emails')->insert([
                'email' => $email,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);
        }
    }

    private function verifyResponseToken($response_token) 
    {
        $is_verified = false;
        $verify_url = "https://www.google.com/recaptcha/api/siteverify";
        if ($response_token !== "") {
            $client = new Client(); 
            $response = $client->post($verify_url, [
                "form_params" => [
                    "secret" => env("RECAPTCHA_SECRET"),
                    "response" => $response_token,
                ]
            ]);
        }
        $body = json_decode($response->getBody());
        return $body->success;
    }


}
