<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MembersController extends Controller
{
    public function __construct(){
        $this->member =new Member();
    }
    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'address' => 'required',
            'phone' => 'required',
            'email' => 'required',
        ]);

        $parameters =  $request->all();
        $response = $this->member->register($parameters);

        if ($response==false) {
            return response()->json(['message' => 'Phone number is taken already', "code"=>406], 200);
        }
        else {
            return response()->json(['message' => 'Member Registered Successfully', "code"=>200, "hosts"=>$response], 200);
        }

    }

    public function getVisits(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
        $id = $request->input('id');
        $visits = $this->member->getVisits($id);
        return response()->json(['message' => 'success', 'data'=>['history_visits'=>$visits], 'code'=>200 ], 200);

    }

    public function getMembers(){
        $members = $this->member->getMembers();
        return response()->json(['message' => 'success', 'data'=>['hosts'=>$members], 'code'=>200 ], 200);
    }


}
