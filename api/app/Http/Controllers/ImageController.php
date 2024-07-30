<?php

namespace App\Http\Controllers;

use App\Models\graphiccard;
use App\Models\machine;
use App\Models\motherboard;
use App\Models\powersupply;
use App\Models\processor;
use App\Models\rammemory;
use App\Models\storagedevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImageController extends Controller
{
    public function getimage($id)
    {

        $id = $id ?? null;
        if (!$id) {
            return data(['messsage' => 'Imagem não encontrada'], 404);
        }
        $image =
            DB::table('graphiccard')->select('imageUrl')->where('imageUrl', $id)
            ->union(DB::table('machine')->select('imageUrl')->where('imageUrl', $id))
            ->union(DB::table('motherboard')->select('imageUrl')->where('imageUrl', $id))
            ->union(DB::table('powersupply')->select('imageUrl')->where('imageUrl', $id))
            ->union(DB::table('rammemory')->select('imageUrl')->where('imageUrl', $id))
            ->union(DB::table('processor')->select('imageUrl')->where('imageUrl', $id))
            ->union(DB::table('storagedevice')->select('imageUrl')->where('imageUrl', $id))->first();

        if (empty($image)) {
            return data(['messsage' => 'Imagem não encontrada'], 404);
        }
        $path = public_path("/images//{$id}") . '.png';
        $file = file_get_contents($path);

        if (!$file) {
            return data(['messsage' => 'Imagem não encontrada'], 404);
        }
        $typ = mime_content_type($path);

        return response($file,200)->header("Content-type", $typ);
    }
}
