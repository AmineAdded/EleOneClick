<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salarie extends Model
{
    protected $guarded=[];
    use HasFactory;
      // Relation Many to Many avec Tache via la table Evaluation
      public function Taches()
      {
          return $this->belongsToMany(Tache::class, 'evaluations', 'salarie_id', 'tache_id')->withPivot('noteEvaluation');
      }

    public function Evaluation(){
        return $this->hasMany(Evaluation::class);
    }
    public function Uap(){
        return $this->belongsTo(Uap::class);
    }
}
