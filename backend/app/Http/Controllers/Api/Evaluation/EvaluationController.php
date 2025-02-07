<?php

namespace App\Http\Controllers\Api\Evaluation;
use App\Models\Salarie;
use App\Models\Uap;
use App\Models\Evaluation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class EvaluationController extends Controller
{
    public function showEvaluation()
    {
        $uaps = Uap::with('Taches', 'Salaries')->get();
        return response()->json(["data" => $uaps], 200);
    }

    public function updateEmployee(Request $request)
    {

        // Récupérer les évaluations envoyées
        $id = $request->input('employeeId');
        $evaluations = $request->input('evaluations', []);

        foreach ($evaluations as $tacheId => $noteEvaluation) {
            $evaluation = Evaluation::where('salarie_id', $id)
                ->where('tache_id', $tacheId)
                ->first();
            if ($evaluation) {
                $evaluation->noteEvaluation = $noteEvaluation;
                $evaluation->dateEvaluation = now();
                $evaluation->save();
            } else {
                Evaluation::create([
                    'salarie_id' => $id,
                    'tache_id' => $tacheId,
                    'noteEvaluation' => $noteEvaluation,
                    'dateEvaluation' => now(),
                ]);
            }

        }
        $sum1=0;
        $sum2=0;
        $allEvaluations = Evaluation::where('salarie_id', $id)->get('noteEvaluation');
        foreach ($allEvaluations as $evaluation) {
            if ($evaluation->noteEvaluation == "A" || $evaluation->noteEvaluation == "B") {
                $sum1++;
            }
            if ($evaluation->noteEvaluation == "C") {
                $sum2++;
            }
        }
        $salarie=Salarie::where('id',$id)->first();
        $salarie->tauxPolyvalence=($sum1/($sum1+$sum2))*100;
        $salarie->save();
        return response()->json([
            'salarie_id' => $id,
            'evaluations' => $salarie
        ]);
    }
    public function getEvaluationTasks($id)
    {
        $evaluations = Evaluation::where('salarie_id', $id)
            ->select('tache_id', 'noteEvaluation')
            ->get();

        return response()->json(['data' => $evaluations], 200);
    }

}
