<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class user extends Model
{
    use HasFactory;
        protected $fillable = ['id,username,password,accessToken']; 
        protected $table = 'user'; 
        public $timestamps = false;
}
