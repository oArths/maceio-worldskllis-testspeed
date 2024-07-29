<?php

namespace App\Http\Controllers;

use App\Models\user;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class jwtcreate extends Controller
{
    private $key;
    public function __construct()
    {
        $this->key = env('JWT');
    }
    public function Token($user)
    {


        $header = [
            'Typ' => 'JWT',
            'alg' =>  'hs256'
        ];
        $NOW = time();
        $payload = [
            'now' => $NOW,
            'exp' => $NOW + 3600,
            'user' => $user
        ];

        $payload = base64_encode(json_encode($payload));
        $header = base64_encode(json_encode($header));

        $sing = base64_encode(hash_hmac('sha256', $header . '.' . $payload, $this->key, true));

        $token = "Beara " . $header . "." . $payload . "." . $sing;

        $userdata = user::where('username', $user)->first();

        if($userdata){
            $userdata->accessToken = $header . "." . $payload . "." . $sing;
            $userdata->save();
        }
        return $token;
    }
}
