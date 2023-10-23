<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Client;

class AuthController extends Controller
{
 /**
     * Create User
     * @param Request $request
     * @return User 
     */

    public $checkbookurl = null;
    public $checkbookapi = null;
    public $client;

    public function __construct()
    {
        $this->checkbookurl = env('checkbookurl');
        $this->checkbookapi = env('checkbookapi');
        $this->client = new Client([
            'base_uri' => $this->checkbookurl,
            'headers' => [
                'Authorization' => $this->checkbookapi,
                'accept' => 'application/json',
                'content-type' => 'application/json',
            ],
        ]);
    }


    public function createUser(Request $request)
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
            $body = '{"name":"'.$request->username.'", "user_id":"'.$request->email.'"}';
            $response = $this->client->request('POST','user',[
                'body' => $body,
            ]);
            $data = json_decode($response->getBody());
            $user = User::create([
                'username' => $request->username,
                "checkbook_id" =>   $data->id,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);
            
            $user->CheckbookRotatekey()->create([
                "secret" => $data->key,
                "key" => $data->secret
            ]);
            return response()->json([
                'message' => 'User onboarded successfully',
                "user_id" =>   $user->id,
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Login The User
     * @param Request $request
     * @return User
     */
    public function loginUser(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), 
            [
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            if(!Auth::attempt($request->only(['email', 'password']))){
                return response()->json([
                    'status' => false,
                    'message' => 'Email & Password does not match with our record.',
                ], 401);
            }

            $user = User::where('email', $request->email)->first();

            return response()->json([
                'status' => true,
                'message' => 'User Logged In Successfully',
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
