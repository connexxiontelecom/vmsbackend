<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class Appointment extends Model
{

    public function createAppointment(Array $parameters){

        $appointment = new Appointment();
        $appointment->name = $parameters['name'];
        $appointment->phone = $parameters['phone'];
        $appointment->purpose = $parameters['purpose'];
        $appointment->host = $parameters['host'];
        $appointment->time = $parameters['time'];
        $appointment->date = $parameters['date'];
        $appointment->status = 3;
        $appointment->save();
        $count = Appointment::count();
        return ['appointment'=>$appointment, 'count'=>$count];
    }


    public function getAllAppointments(){
        $appointments =  DB::table('appointments')
            ->join('members', 'members.id', '=', 'appointments.host')
            ->select('members.id as m_id', 'members.name as m_name', 'members.phone as m_phone',"appointments.id as a_id", 'appointments.*')
            ->orderBy('id', 'DESC')->get();
        $count = Appointment::count();
        return ["appointments"=>$appointments, "count"=>$count];
    }

    public function getAppointments(){
        $appointments =  DB::table('appointments')
            ->join('members', 'members.id', '=', 'appointments.host')
            ->select('members.id as m_id', 'members.name as m_name', 'members.phone as m_phone',"appointments.id as a_id", 'appointments.*')
            ->orderBy('id', 'DESC')->take(10)->get();
        $count = Appointment::count();
        return ["appointments"=>$appointments, "count"=>$count];
    }


    public function getPendingAppointments(){
        $pending = 3;
        $appointments =  DB::table('appointments')
            ->join('members', 'members.id', '=', 'appointments.host')
            ->select('members.id as m_id', 'members.name as m_name', 'members.phone as m_phone',"appointments.id as a_id", 'appointments.*')
            ->where(function ($query) use($pending){
                $query->where('appointments.status', $pending);
            })->take(10)->get();
        $count = Appointment::where('status', $pending)->count();
        return ["appointments"=>$appointments, "pending_appointments"=>$count];
    }


    public function getMoreAppointments($id)
    {
        $appointments =  DB::table('appointments')
            ->join('members', 'members.id', '=', 'appointments.host')
            ->select('members.id as m_id', 'members.name as m_name', 'members.phone as m_phone',"appointments.id as a_id", 'appointments.*')
            ->where(function ($query) use($id){
                $query->where('appointments.id', '<', $id);
            })->orderBy('id', 'DESC')->take(10)->get();
        $count = Appointment::count();
        return ["appointments"=>$appointments, "count"=>$count];
    }

    public function getAppointmentsSummary(){
        $approved = 2;
        $pending = 3;
        $declined = 5;
        $finished = 4;
        $approveds = Appointment::where('status', $approved)->count();
        $pendings = Appointment::where('status', $pending)->count();
        $declineds = Appointment::where('status', $declined)->count();
        $count = Appointment::count();
        return ["approveds"=>$approveds, "pendings"=>$pendings, "declineds"=>$declineds, "count"=>$count];
    }

}
