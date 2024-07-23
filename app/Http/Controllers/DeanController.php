<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DeanController extends Controller
{
    public function show_results()
    {
        return view('pages.dean.result-management.results');
    }



    public function api_index_results(Request $request)
    {
        

        $pendingResults = Result::query()->where('status', 'PENDING')
            ->where('dean_approved', false)
            ->whereNull('dean_reason_for_disapproval')
            ->with(['student.user', 'updater', 'course']);


        $approvedResults = Result::query()->where('status', 'APPROVED')
            ->where('dean_approved', false)
            ->with(['student.user', 'updater', 'course']);

        $disapprovedResults = Result::query()->where('status', 'DISAPPROVED');




        $pendingResults = $pendingResults->get()->groupBy('reference_id');
        $approvedResults = $approvedResults->get()->groupBy('reference_id');
        $disapprovedResults = $disapprovedResults->get()->groupBy('reference_id');
        
        return compact('approvedResults', 'pendingResults', 'disapprovedResults');
    }
}
