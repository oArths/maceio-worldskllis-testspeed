<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class powersupply extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = "powersupply";
    protected $fillable = [
      "id",
    "name",
    "imageUrl",
    "brandId",
    "potency",
    "badge80Plus"
    ];

}
