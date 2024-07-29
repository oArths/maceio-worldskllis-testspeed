<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class JwtValidtion
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    private $key;
    public function __construct(){
        $this->key = env('JWT');
    
        
    }
     
    public function handle(Request $request, Closure $next)
    {
        $token  = $this->getToken($request);

        if (!$token) {

            return data([
                'message' => 'Necessário estar autenticado no sistema'
            ], 401);
        }
        $valid = $this->validToken($token);

        if(!$valid){
            return data(['message' => "Token inválido"], 403);
        }

        $request->merge(['auth' => (array) $valid]);

        return $next($request);
    }
    public function getToken($request)
    {
        $data = $request->header('Authorization');

        if (!$data) {
            return false;
        }

        $token = explode(" ", $data);

        return $token[1];
    }
    public function validToken($token)
    {
        $valid = explode('.', $token);

        if (!count($valid) === 3 || !strstr( $token,'.')) {
            return false;
        }

        list($header, $payload, $sing) = explode('.', $token);
        $decPayload = json_decode(base64_decode($payload));
        $validSing = base64_encode(hash_hmac("sha256", $header . "." . $payload,$this->key, true));


        if($decPayload->exp < time()){
            return false;
        }
        if($validSing !== $sing){
            return false;
        }
        return $decPayload;


    }
}
