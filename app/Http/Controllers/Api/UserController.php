<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use GuzzleHttp\Client;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public $checkbookurl = null;
    public $checkbookapi = null;
    public $client;

    public function __construct()
    {
        $this->checkbookurl = env('checkbookurl','https://sandbox.checkbook.io/v3/');
        $this->checkbookapi = env('checkbookapi','85329354cccd48ba9b04279cea9e0bbb:LubkrkOAvpJ8l8mlrFlkSp6cFBO0ms');
        $this->client = new Client([
            'base_uri' => $this->checkbookurl,
            'headers' => [
                'Authorization' => $this->checkbookapi,
                'accept' => 'application/json',
                'content-type' => 'application/json',
            ],
        ]);
    }
    public function index()
    {
        $response = $this->client->request('get','user/list?page=1&per_page=50');
        $data = json_decode($response->getBody());
        return response()->json($data, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //

       
        
 

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            //Validated
            $validateUser = Validator::make($request->all(), 
            [
                'username' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required'
            ]);

            if($validateUser->fails()){
                return response()->json([
                    'error' => 'Invalid data',
                    'errors' => $validateUser->errors()->first()
                ], 400);
            }

            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);
            $body = '{"name":"'.$request->username.'", "user_id":"'.$request->email.'"}';
            $response = $this->client->request('POST','user',[
                'body' => $body,
            ]);
            $data = json_decode($response->getBody());
            return response()->json([
                'message' => 'User onboarded successfully',
                "user_id" =>   $user->id,
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        return $id;
        
    }
}
