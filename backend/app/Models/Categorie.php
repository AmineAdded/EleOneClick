<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categorie extends Model
{
    protected $fillable = [
        'name',
        'uap_id'
    ];
    use HasFactory;
    public function Uap(){
        return $this->belongsTo(Uap::class);
    }
}
