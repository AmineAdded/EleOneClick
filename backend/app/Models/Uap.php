<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Uap extends Model
{
    use HasFactory;
    public function Salaries(){
        return $this->hasMany(Salarie::class);
    }
    public function Categories(){
        return $this->hasMany(Categorie::class);
    }
    public function Taches(){
        return $this->hasMany(Tache::class);
    }
}
