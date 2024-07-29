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

class ImageController extends Controller
{
    public function getimage($id){

        $id = $id ?? null;
        
        $image = graphiccard::where('imageUrl', $id)->union(machine::where('imageUrl', $id))
        ->union(motherboard::where('imageUrl', $id))->union(powersupply::where('imageUrl', $id))
        ->union(processor::where('imageUrl', $id))->union(rammemory::where('imageUrl', $id))
        ->union(storagedevice::where('imageUrl', $id))->get();
        if(!$image){
            return data(['messsage' => 'Imagem não encontrada'], 404);
            
        }
        $path = public_path("/images//{$id}") . '.png';
        $file = file_get_contents($path);

        if(!$file){
            return data(['messsage' => 'Imagem não encontrada'], 404);
        }
        $typ = mime_content_type($path);
        
        header("Content-type : $typ");

        return $file;
    }
}
