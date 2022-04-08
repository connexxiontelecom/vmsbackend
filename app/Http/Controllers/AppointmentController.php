<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function __construct()
    {
        $this->appointment = new Appointment();
    }

    public function createAppointment(Request $request){
        $this->validate($request, [
            'name' => 'required',
            'phone' => 'required',
            'purpose' => 'required',
            'host' => 'required',
            'date' => 'required',
            'time' => 'required',
        ]);

       $parameters =  $request->all();

      $response =  $this->appointment->createAppointment($parameters);

      return response()->json(['message' => 'success', 'data'=>$response, 'code'=>200 ], 200);

    }

    public function getAppointments(){
        $response = $this->appointment->getAppointments();
        return response()->json(['message' => 'success', 'data'=>$response, 'code'=>200 ], 200);
    }

    public function pendingAppointments(){
       $response = $this->appointment->getPendingAppointments();
        return response()->json(['message' => 'success', 'data'=>$response, 'code'=>200 ], 200);
    }

    public function getMoreAppointments(Request $request){
        $this->validate($request, [
            'id' => 'required',
        ]);
        $id  =  $request->input('id');
        $response = $this->appointment->getMoreAppointments($id);
        return response()->json(['message' => 'success', 'data'=>$response, 'code'=>200 ], 200);
    }

    public function getAppointmentsSummary(Request $request){
        $response = $this->appointment->getAppointmentsSummary();
        return response()->json(['message' => 'success', 'data'=>$response, 'code'=>200 ], 200);
    }

}

