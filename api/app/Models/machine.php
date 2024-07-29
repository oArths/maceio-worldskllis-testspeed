<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class machine extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = "machine";
    protected $fillable = [
    "id",
    "name",
    "description",
    "imageUrl",
    "motherboardId",
    "processorId",
    "ramMemoryId",
    "ramMemoryAmount",
    "graphicCardId",
    "graphicCardAmount",
    "powerSupplyId"
    ];
}
