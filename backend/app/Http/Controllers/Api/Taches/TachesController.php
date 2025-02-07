<?php

namespace App\Http\Controllers\Api\Taches;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tache;

class TachesController extends Controller
{
    public function showTaches(){
        //Réponse ici sous forme de tableau(Eloquent la convertit en tableau)
        $taches=Tache::all();
        return response()->json($taches,200);
    }
    public function deleteTaches($id){
        $tache=Tache::find($id);
        $tache->delete();
        return response()->json(['message'=>'suppression avec succé'],200);
    }
    public function addTaches(Request $request){
        $task=Tache::create([
            'name'=>$request->name,
            'description'=>$request->description,
            'uap_id'=>$request->uap_id,
            'categorie_id'=>$request->categorie_id
        ]);

        return response()->json(['message'=>"Ajout du tache avec succé",200]);
    }
}
