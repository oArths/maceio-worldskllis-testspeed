<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class machinehasstoragedevice extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = "machinehasstoragedevice";
    protected $fillable = [
    "machineId",
    "storageDeviceId",
    "amount"
    ];

}
