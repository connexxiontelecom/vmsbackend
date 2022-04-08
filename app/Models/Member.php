<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\Array_;

class Member extends Model
{
    public function register(Array $parameters)
    {

        $exists = Member::where("phone", $parameters['phone'])->first();

        if ($exists != null) {
            return false;
        }
        else {
            $member = new Member();
            $member->name = $parameters['name'];
            $member->phone = $parameters['phone'];
            $member->address = $parameters['address'];
            $member->email = $parameters['email'];
            $member->uuid = substr(sha1(time()), 20, 40);
            $member->save();
            $member->visits = $this->countVisits($member->id);
            return $member;
        }

    }

    //number of visitation for a member
    public function countVisits($id){

        $visits =  DB::table('visitations')
            ->join('members', 'members.id', '=', 'visitations.member')
            ->where(function ($query) use ($id) {
                $query->where('visitations.member', $id)->where('membertype', 3);
            })->count();
        return $visits;
    }

    public function getMembers(){
        $members = Member::all();
        foreach ($members as $member)
        {
            $memberid = $member->id;
            $visits = $this->countVisits($memberid);
            $member->visits = $visits;
        }
        return $members;
    }

    public function getVisits($id)
    {
        $visits  = DB::table('visitations')
            ->join('visits', 'visits.id', '=', 'visitations.visit')
            ->select('visits.id as visits_id', 'visits.*', 'visitations.*')
            ->where(function ($query) use ($id) {
                $query->where('visitations.member', $id)->where('membertype', 3);
            })->get();

        //visitors
        foreach ($visits as $visit)
        {
            $visitId = $visit->visit;
            $visitors =  DB::table('visitations')
                ->join('visitors', 'visitors.id', '=', 'visitations.member')
                ->select('visitors.id as visitor_id', 'visitors.*')
                ->where(function ($query) use ($visitId) {
                    $query->where('visitations.visit', $visitId)->where('membertype', 4);
                })->get();
            $visit->history_visitors = $visitors;
        }

        //hosts
        foreach ($visits as $visit)
        {
            $visitId = $visit->visit;
            $members =  DB::table('visitations')
                ->join('members', 'members.id', '=', 'visitations.member')
                ->select('members.id as members_id', 'members.*')
                ->where(function ($query) use ($visitId) {
                    $query->where('visitations.visit', $visitId)->where('membertype', 3);
                })->get();
            $visit->history_members = $members;
        }

      return $visits;

    }



}
