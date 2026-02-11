<?php

namespace App\Http\Controllers\Clinical;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClinicalController extends Controller
{
    public function prescriptions()
    {
        return view('pages.clinical.prescriptions.index');
    }

    public function createPrescription()
    {
        return view('pages.clinical.prescriptions.form');
    }

    public function dispensePrescription($id)
    {
        return view('pages.clinical.prescriptions.dispense', [
            'prescriptionId' => $id
        ]);
    }

    public function wardRequests()
    {
        return view('pages.clinical.ward-requests.index');
    }

    public function createWardRequest()
    {
        return view('pages.clinical.ward-requests.form');
    }
}
