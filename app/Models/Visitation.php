<?php

namespace App\Models;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Model;

class Visitation extends Model
{
    //
    public function findVisitionById($id){
        return Visitation::find($id);
    }
}
