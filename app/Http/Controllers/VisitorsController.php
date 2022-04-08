<?php

namespace App\Http\Controllers;
use App\Models\Visit;
use App\Models\Visitation;
use App\Models\Visitor;
use App\Models\Item;
use App\Models\Visitorslog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Eloquent;
class VisitorsController                                                                                                        extends Controller
{


    public function __construct()
    {
        $this->visitation =  new Visitation();
        $this->visitor = new Visitor();
    }

    public function register(Request $request)
    {

        $this->validate($request, [
            'name' => 'required',
            'address' => 'required',
            'phone' => 'required',
        ]);


        $exists = Visitor::where("phone", $request->input('phone'))->first();

        if ($exists != null) {
            return response()->json(['message' => 'Phone number is taken already', "code"=>406], 200);
        } else {
            $visitor = new Visitor();
            $visitor->name = $request->input('name');
            $visitor->phone = $request->input('phone');
            $visitor->address = $request->input('address');
            $visitor->uuid = substr(sha1(time()), 20, 40);
            $visitor->save();
            $totalVisit = $this->getTotalVisit($visitor->id);
            $visitor->totalvisits = $totalVisit;
            return response()->json(['message' => 'Visitor Registered Successfully', "code"=>200, "data"=>["visitors"=>$visitor]], 201);

        }

    }

    public function getTotalVisit($id){
        $totalVisit = Visitation::where('member', $id)->where('membertype', 4)->count();
        return $totalVisit;
    }

    public function getVisitors(){
        $visitors = Visitor::orderBy('id', 'DESC')->take(10)->get();
        $count = Visitor::count();
        foreach($visitors as $visitor)
        {
            $totalVisit = $this->getTotalVisit($visitor->id); //Visitation::where('member', $visitor->id)->where('membertype', 4)->count();
            $visitor->totalvisits = $totalVisit;
        }
        return response()->json(['message' => 'success','code'=>'200', 'data'=>['visitors'=>$visitors, 'count'=>$count], 'code'=>200 ], 200);
    }

    public function getMoreVisitors(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
        $lastId = $request->input('id');
        $visitors = Visitor::where('id', '<', $lastId)->orderBy('id','DESC')->take(10)->get();
        foreach($visitors as $visitor)
        {
            $totalVisit = $this->getTotalVisit($visitor->id);//Visitation::where('member', $visitor->id)->where('membertype', 4)->count();
            $visitor->totalvisits = $totalVisit;
        }
        $count = Visitor::count();
        return response()->json(['message' => 'success','code'=>'200', 'data'=>['visitors'=>$visitors, 'count'=>$count], 'code'=>200 ], 200);

    }

    public function getAllVisitors(){
        $visitors = Visitor::all();
        $count = Visitor::count();
        foreach($visitors as $visitor)
        {
            $totalVisit = $this->getTotalVisit($visitor->id); //Visitation::where('member', $visitor->id)->where('membertype', 4)->count();
            $visitor->totalvisits = $totalVisit;
        }
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
            return response()->json(['message' => 'success', 'data'=>['visitors'=>$visitor, 'count'=>$count], 'code'=>200 ], 200);
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
        $visit = new Visit();
        $visit->purpose = $request->purpose;
        $visit->save();
        $visit_id = $visit->id;

        //create visitation for Host
        foreach ($request->members as $member) {
           $visitation  =  new Visitation();
           $visitation->visit = $visit_id;
           $visitation->member = $member;
           $visitation->membertype = 3;//Host
           $visitation->save();
        }

        //create visitation for visitors
        foreach ($request->visitors as $visitor) {
            $visitation  =  new Visitation();
            $visitation->visit = $visit_id;
            $visitation->member = $visitor;
            $visitation->membertype = 4;//visitor
            $visitation->save();
        }

        //create items for visit
        foreach ($request->items as $vitem) {
            $item =  new Item();
            $item->name = $vitem['name'];
            $item->description = $vitem['description'];
            $item->qty = $vitem['qty'];
            $item->colour = $vitem['color'];
            $item->serial = $vitem['serial'];
            $item->visit = $visit_id;
            $item->save();
        }
        return response()->json(['message' => 'success',  'code'=>200 ], 200);
    }

    public function getVisits(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
        $id  =  $request->input('id');

        $visits  = DB::table('visitations')
            ->join('visits', 'visits.id', '=', 'visitations.visit')
            ->select('visits.id as visits_id', 'visits.*', 'visitations.*')
            ->where(function ($query) use ($id) {
                $query->where('visitations.member', $id)->where('membertype', 4);
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
            $visit->visitors = $visitors;
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
            $visit->members = $members;
        }

        return response()->json(['message' => 'success', 'data'=>['visits'=>$visits], 'code'=>200 ], 200);

    }

    //visitors who haven't signed out
    public function currentVisitors(){
        $visitors = $this->visitor->currentVisitors();
        $count = Visitor::count();//total number of visitors
        return response()->json(['message' => 'success', 'data'=>['current_visitors'=>$visitors,'total_visitors'=>$count], 'code'=>200 ], 200);
    }

    public function signOut(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
        $id  =  $request->input('id');

        $visitation = $this->visitation->findVisitionById($id);
        $visitation->signed_out = 1;
        $visitation->sign_out_time = Carbon::now();
        $visitation->save();
        $current_visitors = $this->visitor->currentVisitors();
        $count = Visitor::count();
        return response()->json(['message' => 'success', 'data'=>['current_visitors'=>$current_visitors, 'total_visitors'=>$count], 'code'=>200 ], 200);

    }

}
