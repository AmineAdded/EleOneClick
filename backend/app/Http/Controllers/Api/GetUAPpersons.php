<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Uap;
use App\Models\Salarie;
use App\Models\Tache;

class GetUAPpersons extends Controller
{
    public function getUapPersonnelsWithTasks($uapId)
    {
        // Récupérer toutes les tâches de l'UAP(tableau associatif key:id -> value:name)
        $taches = Tache::where('uap_id', $uapId)->pluck('name', 'id'); // Clé = ID, Valeur = Nom

        // Récupérer tous les salariés de l'UAP avec leurs évaluations
        $salaries = Salarie::where('uap_id', $uapId)->with('Taches')->get();

        // Initialiser un tableau pour stocker les résultats
        $salariesData = [];

        // Parcourir tous les salariés
        foreach ($salaries as $salarie) {
            // Initialiser un tableau pour les notes avec 'D' par défaut
            $notes = [];

            foreach ($taches as $tacheName) {
                $notes[$tacheName] = 'D';
            }

            // Remplir les notes avec les vraies valeurs des évaluations
            foreach ($salarie->Taches as $tache) {
                $notes[$tache->name] = $tache->pivot->noteEvaluation;
            }

            // Ajouter le salarié dans le tableau final
            $salariesData[] = [
                'salarie_id' => $salarie->id,
                'salarie_name' => $salarie->name,
                'salarie_lastname' => $salarie->lastname,
                'formationIPC' => $salarie->formationIPC,
                'notes' => $notes
            ];
        }

        // Retourner la réponse JSON
        return response()->json([
            'taches' => $taches->values(), // Liste des noms des tâches
            'salaries' => $salariesData
        ]);
    }
}
