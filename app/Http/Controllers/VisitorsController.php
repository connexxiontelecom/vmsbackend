<?php

namespace App\Http\Controllers;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class VisitorsController                                                                                                        extends Controller
{


    public function register(Request $request)
    {

        $this->validate($request, [
            'name' => 'required',
            'address' => 'required',
            'phone' => 'required',
        ]);


        $exists = Visitor::where("phone", $request->input('phone'))->first();

        if ($exists != null) {
            return response()->json(['message' => 'Phone number is taken already', "code"=>200, "data"=>[]], 200);
        } else {
            $visitor = new Visitor();
            $visitor->name = $request->input('name');
            $visitor->phone = $request->input('phone');
            $visitor->address = $request->input('address');
            $visitor->uuid = substr(sha1(time()), 20, 40);
            $visitor->save();

            return response()->json(['message' => 'Visitor Registered Successfully', "code"=>201, "data"=>$visitor], 201);

        }

    }

    public function getVisitors(){
        $visitors = Visitor::orderBy('id', 'DESC')->take(10)->get();
        $count = Visitor::count();
        return response()->json(['message' => 'success','code'=>'200', 'data'=>['visitors'=>$visitors, 'count'=>$count], 'code'=>200 ], 200);
    }

    public function getMoreVisitors(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
        $lastId = $request->input('id');
        $visitors = Visitor::where('id', '<', $lastId)->orderBy('id','DESC')->take(10)->get();
        $count = Visitor::count();
        return response()->json(['message' => 'success','code'=>'200', 'data'=>['visitors'=>$visitors, 'count'=>$count], 'code'=>200 ], 200);

    }

    public function getAllVisitors(){
        $visitors = Visitor::all();
        $count = Visitor::count();
        return response()->json(['message' => 'success','code'=>'200', 'data'=>['visitors'=>$visitors, 'count'=>$count], 'code'=>200 ], 200);
    }

    public function updateVisitor(Request $request){

        $this->validate($request, [
            'name' => 'required',
            'address' => 'required',
            'phone' => 'required',
            'id' => 'required',
        ]);
        $id = $request->input('id');
        $visitor = Visitor::find($id);
        if(!is_null($visitor))
        {
            $visitor->name = $request->input('name');
            $visitor->phone = $request->input('phone');
            $visitor->address = $request->input('address');
            $visitor->save();
            $count = Visitor::count();
            return response()->json(['message' => 'success','code'=>'200', 'data'=>['visitors'=>$visitor, 'count'=>$count], 'code'=>200 ], 200);
        }
    }


    //Visits Section

    public function createVisit(Request $request){

        $this->validate($request, [
            'purpose' => 'required',
            'members'=>'required',
            'visitors'=>'required',
            'items'=>'required'
        ]);
        //create new visit

        //create visitation for members
        foreach ($request->members as $member) {
            $pickup["traveltime"] = "N/A";
            $pickup_place_id = $pickup['place'];
        }


        //create visitation for visitors


        //create items for visit


        //return success
    }



}
