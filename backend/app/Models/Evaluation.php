<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;
    protected $guarded=[];
    public function Salarie(){
        return $this->belongsTo(Salarie::class);
    }
    public function Tache(){
        return $this->belongsTo(Tache::class);
    }
}
