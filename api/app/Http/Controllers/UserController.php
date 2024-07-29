<?php

namespace App\Http\Controllers;

use App\Http\Middleware\JwtValidtion;
use App\Models\brands;
use App\Models\graphiccard;
use App\Models\machine;
use App\Models\machinehasstoragedevice;
use App\Models\motherboard;
use App\Models\powersupply;
use App\Models\processor;
use App\Models\rammemory;
use App\Models\rammemorytype;
use App\Models\sockettype;
use App\Models\storagedevice;
use Illuminate\Http\Request;
use App\Models\user;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Stmt\Return_;

class UserController extends Controller
{

    public function LOGIN(Request $parms)
    {
        $validToken = new JwtValidtion;
        $Token = new jwtcreate;

        $name = $parms->username ?? null;
        $password = $parms->password ?? null;


        if (!$name || !$password) {
            return data(['message' => "Credenciais inválidas"], 422);
        }

        $user = user::where('username', $name)->first();

        $valid = $validToken->validToken($user->accessToken);

        if ($valid) {
            return data(['messege' =>  'Usuário já autenticado']);
        }

        if (!$user) {
            return data(['message' => "Credenciais inválidas"], 422);
        }

        $crip = hash('sha256', $password);

        if (strlen($user->password) !== 64) {
            $user->password = hash("sha256", $user->password);
            $user->save();
        }
        if ($crip !== $user->password) {
            return data(['message' => "Credenciais inválidas"], 422);
        }


        $newToken = $Token->Token($name);



        return data(['token' => $newToken], 403);
    }
    public function Delete(Request $token)
    {

        $user = user::where('username', $token['auth']['user']);
        $user->accessToken = null;

        return data(['message' => 'Logout com sucesso'], 200);
    }
}
