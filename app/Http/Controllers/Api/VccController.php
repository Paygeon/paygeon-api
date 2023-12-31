<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vcc;
use GuzzleHttp\Client;
use Auth;
use App\Models\User;
class VccController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */



    public $checkbookurl = "";
    public $checkbookapi = "";
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
        return $this->checkbookurl;
        // $response = $this->client->request('GET','account/vcc');
        // $data = json_decode($response->getBody());
        // return response()->json($data, 200);

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
        $user = User::where("checkbook_id",$request->header("client-id"))->first();

        $body = json_encode($request->all());
        $response = $this->client->request('post','account/vcc',[
           'body' =>  $body
        ]);
        $data = json_decode($response->getBody());
        Vcc::create([
            "user_id" => $user->id,
            "card_id" => $data->id,
            "card_number" => $data->card_number,
            "cvv" => $data->cvv,
            "expiration_date" => $data->expiration_date,
        ]);
        return response()->json($data, 200);
    }

    public function transactions($id)
    {
        $response = $this->client->request('get','account/vcc/'.$id.'/transaction');
        $data = json_decode($response->getBody());
        return response()->json($data, 200);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $vc = Vcc::where("card_id",$id)->first();
        return response()->json($vc, 200);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $body = json_encode($request->all());
        $response = $this->client->request('put','account/vcc/'.$id,[
            "body" => $body
        ]);
        $data = json_decode($response->getBody());
        Vcc::where("card_id",$id)->update([
            "user_id" => 1,
            "card_id" => $data->id,
            "card_number" => $data->card_number,
            "cvv" => $data->cvv,
            "expiration_date" => $data->expiration_date,
        ]);
        return response()->json($data, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $response = $this->client->request('DELETE','account/vcc/'.$id);
        $data = json_decode($response->getBody());
        Vcc::where("card_id",$id)->delete();
        return response()->json($data, 200);
    }
}
