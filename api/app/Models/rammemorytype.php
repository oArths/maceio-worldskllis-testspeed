<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class     rammemorytype
 extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = "rammemorytype";
    protected $fillable = [
   "id",
    "name",
    ];
}
