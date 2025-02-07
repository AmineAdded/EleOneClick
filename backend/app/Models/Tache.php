<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tache extends Model
{
    protected $fillable = [
        'name',
        'description',
        'uap_id',
        'categorie_id'
    ];

    use HasFactory;
     public function Salaries()
     {
         return $this->belongsToMany(Salarie::class, 'evaluations', 'tache_id', 'salarie_id')->withPivot('noteEvaluation');
     }
    public function Evaluation(){
        return $this->hasMany(Evaluation::class);
    }
    public function Uap(){
        return $this->belongsTo(Uap::class);
    }
    
}
