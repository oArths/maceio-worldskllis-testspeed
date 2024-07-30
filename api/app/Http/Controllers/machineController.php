<?php

namespace App\Http\Controllers;

use App\Models\graphiccard;
use App\Models\machine;
use App\Models\machinehasstoragedevice;
use App\Models\motherboard;
use App\Models\powersupply;
use App\Models\processor;
use App\Models\rammemory;
use App\Models\rammemorytype;
use App\Models\storagedevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class machineController extends Controller
{
    public function Search(Request $parms)
    {
        $error = [];
        $PATH = Request()->url();
        $categorys = explode('/', $PATH);

        $category = end($categorys) ?? $error[] = ['message' => 'é obrigatorio a presensa de um categoria'];
        $q = $parms->q ?? $error[] = ['message' => 'é obrigatorio a presensa de um nome a ser pesquisado
        '];
        $pagesize = $parms->pageSize ?? 20;
        $page = $parms->page ?? 1;
        $offset = ($page - 1) * $pagesize;
        
        if ($error) {
            return data($error, 404);
        }
        if(substr($category, -1) === 's'){
            $category = substr($category, 0, -1);
        }
        if (strpos('-', $category)) {
            $clear = explode('-', $category);
            $category = $clear[0] . end($clear);
        }
       $ALL =  DB::table($category)->where('name',  'LIKE', '%' . $q . '%' )->get();

        if($page || $pagesize){
            $slice = array_slice($ALL->toArray(), $offset, $pagesize);
            return data($slice, 200);
        }

        return data($ALL, 200);
    }
    public function updateMachine(Request $params){
        $error = [];
        $path = request()->url();
        $clear = explode('/', $path);

        $id = end($clear);
        $nameUser = $params->name ?? $error[] = ['message' => "é necessario o nome"];
        $descriptionUser = $params->description ?? $error[] = ['message' => "é necessario uma descrição"];
        $motherboardId = $params->motherboardId ?? $error[] = ['message' => "é necessario ao menos 1 motherboard"];
        $powerSupplyId = $params->powerSupplyId ?? $error[] = ['message' => "é necessario ao menos 1 powerSupply"];
        $processorId = $params->processorId ?? $error[] = ['message' => "é necessario ao menos 1 proccers"];
        $ramMemoryId = $params->ramMemoryId ?? $error[] = ['message' => "é necessario ao menos 1 ramMemory"];
        $ramMemoryAmount = $params->ramMemoryAmount ?? $error[] = ['message' => "é necessario ao menos 1 ramMemoryAmount"];
        $storageDeviceId = $params->storageDevices['storageDeviceId'] ?? $error[] = ['message' => "é necessario ao menos 1 StoreDeviceId"];
        $amount = $params->storageDevices['amount'] ?? $error[] = ['message' => "é necessario ao menos 1 StoredeviceAmount"];
        $graphicCardId = $params->graphicCardId ?? $error[] = ['message' => "é necessario ao menos 1 graficCards"];
        $graphicCardAmount = $params->graphicCardAmount ?? $error[] = ['message' => "é necessario ao menos 1 graficCardAmount"];
        $imageBase64 = $params->imageBase64 ?? null;

        if (!empty($error)) {
            return data($error, 422);
        }
        $motherboard = motherboard::find($motherboardId);
        $powersupply = powersupply::find($powerSupplyId);
        $porcces = processor::find($processorId);
        $ram = rammemory::find($ramMemoryId);
        $device = storagedevice::find($storageDeviceId);
        $grafic = graphiccard::find($graphicCardId);

        if ($motherboard->socketTypeId !== $porcces->socketTypeId) {
            $error[] = ['message' => "Tipo de soquete da placa-mãe é diferente do tipo de soquete do processador"];
        }
        if ($motherboard->maxTdp < $porcces->tdp) {
            $error[] = ['message' => "TDP do processador é maior do que o TDP máximo suportado pela placa-mãe"];
        }
        if ($motherboard->ramMemoryTypeId < $ram->ramMemoryTypeId) {
            $error[] = ['message' => "Tipo de memória RAM da placa-mãe é diferente do tipo da memória RAM"];
        }
        if ($motherboard->ramMemorySlots < $ramMemoryAmount) {
            $error[] = ['message' => "Quantidade de memórias RAM for maior do que a quantidade de slots presentes na placa-mãe"];
        }
        if ($motherboard->pciSlots < $graphicCardAmount) {
            $error[] = ['message' => "Quantidade de placas de vídeo for maior do que a quantidade de slots PCI Express na placamãe"];
        }
        if (($grafic->minimumPowerSupply) * $graphicCardAmount > $powersupply->potency) {
            $error[] = ['message' => "Potência da fonte de alimentação é menor do que a potência mínima da placa de vídeo vezes
(multiplicada) pela quantidade de placas de vídeo"];
        }
        if ($graphicCardAmount > 1 && $grafic->supportMultiGpu < 1) {
            $error[] = ['message' => "Quantidade de placas de vídeo é maior que 1 e o modelo de placa de vídeo não suporta
SLI/Crossfire"];
        }
        switch ($device->storageDeviceInterface) {
            case 'sata':
                if ($motherboard->sataSlots < $amount) {
                    $error[] = ['message' => "Quantidade de dispositivos de armazenamento do tipo SATA for maior do que a quantidade de
slots SATA na placa mãe"];
                }
                break;

            default:
                if ($motherboard->sataSlots < $amount) {
                    $error[] = ['message' => "Quantidade de dispositivos de armazenamento do tipo SATA for maior do que a quantidade de
slots SATA na placa mãe"];
                }
                break;
        }
        if ($motherboard->m2Slots < $amount) {
            $error[] = ['message' => "Quantidade de dispositivos de armazenamento do tipo M2 é maior do que a quantidade de
slots M2 na placa mãe"];
        }
        if (!empty($error)) {
            return data($error, 422);
        }
        
        $machineUpdate = [
            'name' => $nameUser,
            'description' => $descriptionUser,
            'motherboardId' => $motherboardId,
            'processorId' => $processorId,
            'ramMemoryId' => $ramMemoryId,
            'ramMemoryAmount' => $ramMemoryAmount,
            'graphicCardId' => $graphicCardId,
            'graphicCardAmount' => $graphicCardAmount,
            'powerSupplyId' => $powerSupplyId,
        ];
        if($imageBase64){

            if (strpos($imageBase64, ',')) {
                $explode = explode(',', $imageBase64);
                $decode = base64_decode($explode[1]);
            }
    
            $name = time();
    
            $publicpath = public_path('images/');
            $imagepath = $publicpath  . $name . '.png';
            file_put_contents($imagepath, $decode);
            $machineUpdate['imageUrl'] = $name;
        }

        $oldmachine = machinehasstoragedevice::where('machineId', $id)->first();
        $machine = machine::where('id', $id)->update($machineUpdate);
        $device = machinehasstoragedevice::where('machineId', $id)->where('storageDeviceId', $oldmachine->storageDeviceId)->update([
            'machineId' => '13',
            'storageDeviceId' => $storageDeviceId,
            'amount' => $amount
        ]);

        return data($machineUpdate, 201);
    
    }
    public function MachinePieces(Request $parms)
    {
         $path = Request()->url();
         $clear = explode('/', $path);

        $op = end($clear)?? null;

        if (substr($op, -1) ===  's') {
            $op = substr($op, 0, -1);
        }

        if (strpos($op, '-')) {
            $clea = explode('-', $op);
            $op = $clea[0] . end($clea);
        }

        $pagesize = $parms->pageSize ?? 20;
        $page = $parms->page ?? 1;
        $offset = ($page - 1) * $pagesize;


        $a = DB::table($op)->get();
        $b = $a->toArray();
        $result = array_slice($b, $offset, $pagesize);


        return data($result, 200);
    }
    public function CreatMachine(Request $params)
    {
        $error = [];
        $nameUser = $params->name ?? $error[] = ['message' => "é necessario o nome"];
        $descriptionUser = $params->description ?? $error[] = ['message' => "é necessario uma descrição"];
        $motherboardId = $params->motherboardId ?? $error[] = ['message' => "é necessario ao menos 1 motherboard"];
        $powerSupplyId = $params->powerSupplyId ?? $error[] = ['message' => "é necessario ao menos 1 powerSupply"];
        $processorId = $params->processorId ?? $error[] = ['message' => "é necessario ao menos 1 proccers"];
        $ramMemoryId = $params->ramMemoryId ?? $error[] = ['message' => "é necessario ao menos 1 ramMemory"];
        $ramMemoryAmount = $params->ramMemoryAmount ?? $error[] = ['message' => "é necessario ao menos 1 ramMemoryAmount"];
        $storageDeviceId = $params->storageDevices['storageDeviceId'] ?? $error[] = ['message' => "é necessario ao menos 1 StoreDeviceId"];
        $amount = $params->storageDevices['amount'] ?? $error[] = ['message' => "é necessario ao menos 1 StoredeviceAmount"];
        $graphicCardId = $params->graphicCardId ?? $error[] = ['message' => "é necessario ao menos 1 graficCards"];
        $graphicCardAmount = $params->graphicCardAmount ?? $error[] = ['message' => "é necessario ao menos 1 graficCardAmount"];
        $imageBase64 = $params->imageBase64 ?? $error[] = ['message' => "é necessario ao menos 1 imageBase64"];

        if (!empty($error)) {
            return data($error, 422);
        }
        $motherboard = motherboard::find($motherboardId);
        $powersupply = powersupply::find($powerSupplyId);
        $porcces = processor::find($processorId);
        $ram = rammemory::find($ramMemoryId);
        $device = storagedevice::find($storageDeviceId);
        $grafic = graphiccard::find($graphicCardId);

        // return $grafic;
        if ($motherboard->socketTypeId !== $porcces->socketTypeId) {
            $error[] = ['message' => "Tipo de soquete da placa-mãe é diferente do tipo de soquete do processador"];
        }
        if ($motherboard->maxTdp < $porcces->tdp) {
            $error[] = ['message' => "TDP do processador é maior do que o TDP máximo suportado pela placa-mãe"];
        }
        if ($motherboard->ramMemoryTypeId < $ram->ramMemoryTypeId) {
            $error[] = ['message' => "Tipo de memória RAM da placa-mãe é diferente do tipo da memória RAM"];
        }
        if ($motherboard->ramMemorySlots < $ramMemoryAmount) {
            $error[] = ['message' => "Quantidade de memórias RAM for maior do que a quantidade de slots presentes na placa-mãe"];
        }
        if ($motherboard->pciSlots < $graphicCardAmount) {
            $error[] = ['message' => "Quantidade de placas de vídeo for maior do que a quantidade de slots PCI Express na placamãe"];
        }
        if (($grafic->minimumPowerSupply) * $graphicCardAmount > $powersupply->potency) {
            $error[] = ['message' => "Potência da fonte de alimentação é menor do que a potência mínima da placa de vídeo vezes
(multiplicada) pela quantidade de placas de vídeo"];
        }
        if ($graphicCardAmount > 1 && $grafic->supportMultiGpu < 1) {
            $error[] = ['message' => "Quantidade de placas de vídeo é maior que 1 e o modelo de placa de vídeo não suporta
SLI/Crossfire"];
        }
        switch ($device->storageDeviceInterface) {
            case 'sata':
                if ($motherboard->sataSlots < $amount) {
                    $error[] = ['message' => "Quantidade de dispositivos de armazenamento do tipo SATA for maior do que a quantidade de
slots SATA na placa mãe"];
                }
                break;

            default:
                if ($motherboard->sataSlots < $amount) {
                    $error[] = ['message' => "Quantidade de dispositivos de armazenamento do tipo SATA for maior do que a quantidade de
slots SATA na placa mãe"];
                }
                break;
        }
        if ($motherboard->m2Slots < $amount) {
            $error[] = ['message' => "Quantidade de dispositivos de armazenamento do tipo M2 é maior do que a quantidade de
slots M2 na placa mãe"];
        }
        if (!empty($error)) {
            return data($error, 422);
        }

        if (strpos($imageBase64, ',')) {
            $explode = explode(',', $imageBase64);
            $decode = base64_decode($explode[1]);
        }

        $name = time();

        $publicpath = public_path('images/');
        $imagepath = $publicpath  . $name . '.png';
        file_put_contents($imagepath, $decode);


        $machine = machine::create([
            'name' => $nameUser,
            'description' => $descriptionUser,
            'imageUrl' => $name,
            'motherboardId' => $motherboardId,
            'processorId' => $processorId,
            'ramMemoryId' => $ramMemoryId,
            'ramMemoryAmount' => $ramMemoryAmount,
            'graphicCardId' => $graphicCardId,
            'graphicCardAmount' => $graphicCardAmount,
            'powerSupplyId' => $powerSupplyId,
        ]);
        $device = machinehasstoragedevice::create([
            'machineId' => $machine->id,
            'storageDeviceId' => $storageDeviceId,
            'amount' => $amount
        ]);

        return data(['id' => $machine->id], 201);
    }

    public function deleteMachine(Request $parms)
    {
        $id = $parms->id ?? null;

        $exist = machine::where('id', $id)->first();
        if (!$exist) {
            return data(["message:" => "Modelo de máquina não encontrado"], 404);
        }
        $exist->delete();
        return data($exist, 204);
    }
}
