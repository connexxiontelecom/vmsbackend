<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Visitor extends Model
{
    public function currentVisitors(){
        $visitors =  DB::table('visitations')
            ->join('visitors', 'visitors.id', '=', 'visitations.member')
            ->select('visitors.id as visitor_id', "visitations.id as visitation_id", 'visitors.*', 'visitations.*')
            ->where(function ($query){
                $query->where('visitations.signed_out', null)->where('membertype', 4);
            })->orderBy('visitations.id','DESC')->get();

        //getmembers
        foreach ($visitors as $visitor)
        {
            $members =  DB::table('visitations')
                ->join('members', 'members.id', '=', 'visitations.member')
                ->select('members.id as member_id', "visitations.id as visitation_id", 'members.*', 'visitations.*')
                ->where(function ($query) use ($visitor){
                    $query->where('visitations.visit', $visitor->visit)->where('membertype', 3);
                })->get();

            $visitor->members = $members;
        }

        return $visitors;
    }

}
