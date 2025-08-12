<?php

namespace App\Http\Controllers\Team\Coaching;


use App\DataTables\Team\Coaching\CoachingMaterialDataTable;
use App\Http\Controllers\Controller;

class CoachingMaterialController extends Controller
{

    public function index(CoachingMaterialDataTable $CoachingMaterialDataTable){

         return $CoachingMaterialDataTable->render('team.coaching-material.index');
    }
}
