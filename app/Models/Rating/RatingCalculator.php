<?php

namespace App\Models\Rating;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Cache;
use Storage;
use Log;

class RatingCalculator extends Model
{
    public $cid=0;
    public $contestants=[];
    public $totParticipants=0;
    public $INITIAL_RATING=1500;

    public function __construct($cid){
        $this->cid=$cid;

        // get rank
        $this->getRecord();
    }

    private function getRecord(){
        $ret = DB::table("professional_ranking_temp")->where(["cid"=>$this->cid])->get()->all();
        $this->totParticipants = count($ret);
        foreach($ret as $r){
            $this->contestants[]=[
                "uid"=>$r["uid"],
                "points"=>$r["points"],
                "rating"=>$r["rating"]
            ];
        }
    }

    private function reassignRank(){
        $this->sort("points");
        $idx = 0;
        $points = $this->contestants[0]["points"];
        $i = 1;
        while($i < $this->totParticipants){
            if($this->contestants[$i]["points"] < $points){
                $j = $idx;
                while($j < $i){
                    $this->contestants[$j]["rank"] = $i;
                    $j += 1;
                }
                $idx = $i;
                $points = $this->contestants[$i]["points"];
            }
            $i += 1;
        }
        $j = $idx;
        while($j < $this->totParticipants){
            $this->contestants[$j]["rank"] = $this->totParticipants;
            $j += 1;
        }
    }

    private function getEloWinProbability($Ra, $Rb){
        return 1.0 / (1 + pow(10, ($Rb-$Ra)/400.0));
    }

    private function getSeed($rating){
        $result = 1.0;
        foreach($this->contestants as $other){
            $result += $this->getEloWinProbability($other["rating"], $rating);
        }
        return $result;
    }

    private function getRatingToRank($rank){
        $left=1;
        $right=8000;
        while($right - $left > 1){
            $mid = floor(($right + $left)/2);
            if($this->getSeed($mid) < $rank){
                $right = $mid;
            }else{
                $left = $mid;
            }
        }
        return $left;
    }

    private function sort($key){
        usort($this->contestants, function ($a, $b) use ($key) {
            return $b[$key] <=> $a[$key];
        });
    }

    public function calculate(){
        if(empty($this->contestants)){
            return;
        }

        // recalc rank
        $this->reassignRank();

        foreach($this->contestants as $member){
            $member["seed"] = 1.0;
            foreach($this->contestants as $other){
                if($member["uid"] != $other["uid"]){
                    $member["seed"] += $this->getEloWinProbability($other["rating"], $member["rating"]);
                }
            }
        }

        foreach($this->contestants as $contestant){
            $midRank = sqrt($contestant["rank"] * $contestant["seed"]);
            $contestant["needRating"] = $this->getRatingToRank($midRank);
            $contestant["delta"] = floor(($contestant["needRating"] - $contestant["rating"])/2);
        }

        $this->sort("rating");

        // DO some adjuct
        // Total sum should not be more than ZERO.
        $sum = 0;

        foreach($this->contestants as $contestant){
            $sum += $contestant["delta"];
        }
        $inc = -floor($sum / $this->totParticipants) - 1;
        foreach($this->contestants as $contestant){
            $contestant["delta"] += $inc;
        }

        // Sum of top-4*sqrt should be adjusted to ZERO.

        $sum = 0;
        $zeroSumCount = min(intval(4*round(sqrt($this->totParticipants))), $this->totParticipants);

        for($i=0;$i<$zeroSumCount;$i++){
            $sum += $this->contestants[i]["delta"];
        }

        $inc = min(max(-floor($sum / $zeroSumCount), -10), 0);

        for($i=0;$i<$zeroSumCount;$i++){
            $this->contestants[i]["delta"] += $inc;
        }

        $this->validateDeltas();
    }

    private function validateDeltas(){
        $this->sort("points");

        for($i=0;$i<$this->totParticipants;$i++){
            for($j=$i+1;$j<$this->totParticipants;$j++){
                if($this->contestants[i]["rating"] > $this->contestants[j]["rating"]){
                    if($this->contestants[i]["rating"] + $this->contestants[i]["delta"] < $this->contestants[j]["rating"] + $this->contestants[j]["delta"]){
                        Log::debug("First rating invariant failed: {$this->contestants[i]["uid"]} vs. {$this->contestants[j]["uid"]}.");
                    }
                }

                if($this->contestants[i]["rating"] < $this->contestants[j]["rating"]){
                    if($this->contestants[i]["delta"] < $this->contestants[j]["delta"]){
                        Log::debug("Second rating invariant failed: {$this->contestants[i]["uid"]} vs.  {$this->contestants[j]["uid"]}.");
                    }
                }
            }
        }
    }

}
