<?php

namespace App\Http\Controllers\Api\Categories;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Categorie;
use App\Models\UAP;

class CategoryController extends Controller
{
    public function showCategories(){
        //Réponse ici sous forme de tableau(Eloquent la convertit en tableau)
        $categories=Categorie::all();
        return response()->json($categories,200);
    }
    public function deleteCategory($id){
        $category=Categorie::find($id);
        $category->delete();
        return response()->json(['message'=>'suppression avec succé'],200);
    }
    public function showUAPs(){
        $UAPs=UAP::all();
        return response()->json($UAPs,200);
    }
    public function addCategory(Request $request){
        $request->validate([
            "name" => "required|unique:categories",
        ]);
    
        $category=Categorie::create([
            'name'=>$request->name,
            
            'uap_id'=>$request->uap_id
        ]);

        return response()->json(['message'=>"Ajout du tache avec succé",200]);
    }
}
