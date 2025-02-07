<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\Uap;
use App\Models\Salarie;
use App\Models\Tache;
use App\Models\User;
use App\Models\Evaluation;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Mail\SignUp;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    public function count()
    {
        $count1 = Salarie::count();
        $count2 = Uap::count();
        $count3 = Tache::count();
        $count4 = User::where('isAdmin', 0)->count();
        $latestEvaluations = Evaluation::with(['Salarie', 'Tache'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'salaries' => $count1,
            'uaps' => $count2,
            'taches' => $count3,
            'formateurs' => $count4,
            'latestEvaluations' => $latestEvaluations
        ]);
    }

    public function getUapInfos()
    {
        $uaps = Uap::all();
        $tasks = [];
        
        foreach ($uaps as $uap) {
            $Couv = []; 
        
            $taches = Tache::where('uap_id', $uap->id)->get();
        
            foreach ($taches as $tache) {
                $actuel = 0;
                $potentiel = 0;
        
                $evaluations = Evaluation::where('tache_id', $tache->id)->pluck('noteEvaluation')->toArray();
        
                foreach ($evaluations as $evaluation) {
                    if ($evaluation == "A" || $evaluation == "B") {
                        $actuel++;
                    }
                    if ($evaluation == "A" || $evaluation == "B" || $evaluation == "C") {
                        $potentiel++;
                    }
                }
        
                $couv = ($potentiel > 0) ? ($actuel / $potentiel) * 100 : 0;
        
                $Couv[] = $couv;
        
            }
        
            $couv_total = (!empty($Couv)) ? array_sum($Couv) / count($Couv) : 0;
        
            // Stocker les résultats
            $tasks[] = [
                'name' => $uap->name,
                'responsable' => $uap->responsableUAP,
                'couv' => $couv_total
            ];
        }
        
        // Retourner les résultats en JSON
        return response()->json($tasks);
        

    }
    public function getUAPData()
    {
        $uaps = Uap::all();
        $data = [];

        foreach ($uaps as $uap) {
            $countSalariés = Salarie::where('uap_id', $uap->id)->count();
            $data[] = [
                'uap' => $uap->name,
                'count' => $countSalariés
            ];
        }
        return response()->json($data);
    }
    public function getTacheData()
    {
        $uaps = Uap::all();
        $data = [];
        foreach ($uaps as $uap) {
            $countTaches = Tache::where('uap_id', $uap->id)->count();
            $data[] = [
                'uap' => $uap->name,
                'count' => $countTaches
            ];
        }
        return response()->json($data);
    }
    public function getCategoriesData()
    {
        $categories = Categorie::all();
        $data = [];

        foreach ($categories as $categorie) {
            $countTaches = Tache::where('categorie_id', $categorie->id)->count();
            $data[] = [
                'categorie' => $categorie->name,
                'count' => $countTaches,
                'countTotal' => Tache::count()
            ];
        }
        return response()->json($data);
    }
    public function fetchTacheEvaluation()
    {
        $taches = Tache::all();
        $data = [];

        foreach ($taches as $tache) {
            $countEvaluationA = Evaluation::where('tache_id', $tache->id)
                ->where('noteEvaluation', 'A')
                ->count();
            $countEvaluationB = Evaluation::where('tache_id', $tache->id)
                ->where('noteEvaluation', 'B')
                ->count();
            $countEvaluationC = Evaluation::where('tache_id', $tache->id)
                ->where('noteEvaluation', 'C')
                ->count();

            $actuel = $countEvaluationA + $countEvaluationB;
            $potentiel = $actuel + $countEvaluationC;

            $couv = 0;
            if ($potentiel > 0) {
                $couv = ($actuel / $potentiel) * 100;
            }

            $UAPname = Uap::where('id', $tache->uap_id)->value('name');

            $data[] = [
                'name' => $tache->name,
                'nomUAP' => $UAPname,
                'couv' => $couv
            ];
        }

        return response()->json($data);
    }
    public function fetchNotifications()
    {
        $notifications = Notification::with('user')->get()->reverse();

        $countnotReadNotifications = $notifications->where('isRead', 0)->count();
        $data = [];

        foreach ($notifications as $notification) {
            $data[] = [
                'name' => $notification->user->name ?? 'Inconnu',
                'lastname' => $notification->user->lastname ?? 'Inconnu',
                'userId' => $notification->user->id ?? null,
                'notification' => $notification,
            ];
        }

        return response()->json([$data, $countnotReadNotifications]);
    }


    public function notificatioIsRead($id)
    {
        $notification = Notification::where("id", $id)->first();
        $notification->isRead = 1;
        $notification->save();
        return response()->json("Notification lue", 200);
    }

    public function showFormateurs(){
        $formateurs=User::where('isAdmin',0)->get();
        return response()->json($formateurs);
    }
    public function deleteFormateur($id){
        $Formateur=User::where('id',$id);
        $Formateur->delete();
        return response()->json("Formateur supprimé",200);
    }
    public function updateFormateur(Request $request,$id){
        $Formateur=User::where('id',$id)->first();
        $image = null;
        if ($request->hasFile('image')) {
            $request->validate(['image'=>"nullable|mimes:jpg,jpeg,png,svg,gif"]);
            $image = $request->file('image')->store('users', 'public');
        }
        $Formateur->name=$request->name;
        $Formateur->lastname=$request->lastname;
        $Formateur->email=$request->email;
        $Formateur->image=$image;
        $Formateur->save();
        return response()->json($Formateur,200);
    }

    public function createTrainer(Request $request){
        $request->validate([
            'name' => 'required',
            'lastname' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'image' => 'nullable|mimes:jpg,jpeg,png,svg,gif'
        ]);

        $image = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image')->store('users', 'public');
        }

        $formateur = new User();
        $formateur->name = $request->name;
        $formateur->lastname = $request->lastname;
        $formateur->email = $request->email;
        $formateur->password = bcrypt($request->password);
        $formateur->image = $image;
        $formateur->isAdmin = 0;
        $formateur->save();

        Mail::to($request->email)->send(new SignUp($request->name));

        return response()->json($formateur, 201);
    }
}

