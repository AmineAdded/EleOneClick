<?php

namespace App\Http\Controllers\Api\Employees;

use App\Models\Salarie;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EmployeesController extends Controller
{
    public function afiicheEmployees(){
        $employees=Salarie::all();
        return response ()->json($employees,200);
    }
    public function deleteEmployee($id){
        $employee=Salarie::find($id);
        if($employee){
            $employee->delete();
            return response()->json(['message'=>'suppression avec succés'],200);
        }
        else{
            return response()->json(['message'=>'employée non trouvé'],404);
        }
    }

    public function updateEmployee(Request $request,$id){
        $employee=Salarie::find($id);
        if($employee){
            $employee->update([
                "name"=>$request->name,
                "lastname"=>$request->lastname,
                "uap_id"=>$request->uap_id,
                "formationIPC"=>$request->formationIPC,
                "tauxPolyvalence"=>$request->tauxPolyvalence
            ]);
    
            return response()->json(['data'=>$employee],200);
        }
        return response()->json(["Something went wrong"],401);
    }
    public function createEmployee(Request $request){
        $user=Salarie::create([
            "name"=>$request->name,
            "lastname"=>$request->lastname,
            "uap_id"=>$request->uap_id,
            "formationIPC"=>$request->formationIPC,
            "tauxPolyvalence"=>$request->tauxPolyvalence,
            "tauxPolyvalencePotentiel"=>$request->tauxPolyvalencePotentiel
        ]);
        $user->save();
        return response()->json(['data'=>$user],200);
    }
}
