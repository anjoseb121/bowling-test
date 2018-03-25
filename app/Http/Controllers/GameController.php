<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GameController extends Controller
{

    /**
     * Loop all array and return the total score
     */
    private function calculateScore($scores) {
        $sum = 0;
        foreach($scores as $k => $v) {
            // if is one of the last 3 throws
            if (count($scores) - 3 <= $k) {
                if ($v === "/") {
                    $sum += 10 - $scores[$k - 1];
                } else {
                    $sum += $v;
                }
                continue;
            } else if ($v === 10) {
                // add 10 value to sum
                $sum += $v;
                // add the next score to sum
                $sum += $scores[$k + 1];
                // if the next score is a spare add the diff from 10 to sum
                // otherwise add the value to sum too
                if ($scores[$k + 2] == "/") {                    
                    $sum += 10 - $scores[$k + 1];
                } else {
                    $sum += $scores[$k + 2];
                }
            } else if ($v === "/") {
                // if value is a spare add the diff between 10 and last score to sum
                // also add te next value to sum
                $sum += (10 - $scores[$k - 1]);
                $sum += $scores[$k + 1];
            } else {
                // add the current value to sum
                $sum = $sum + $v;
            }
        }
        return $sum;
    }

    /**
     * Loop all array values and replace them by a numeric value
     * 
     */
    private function assignValues($scores) {
        foreach($scores as $k => $v) {
            switch(strtoupper($v)) {
                case "X": 
                    $scores[$k] = 10;
                    break;
                case "-":
                    $scores[$k] = 0;
                    break;
                case "/":
                    continue;
                default:
                    $scores[$k] = intval($v);
            }
        }
        return $scores;
    }

    public function totalScore(Request $request) {
        $rolls = $request->input('rolls');
        $scores = str_split($rolls);
        $scores = $this->assignValues($scores);
        $sum = $this->calculateScore($scores);
        return response()->json([
            'score' => $sum,
        ]);
    }
}
