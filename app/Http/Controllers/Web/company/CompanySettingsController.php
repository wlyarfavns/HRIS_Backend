<?php

namespace App\Http\Controllers\Web\company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CompanySettingsController extends Controller
{
    
    public function updateAttendanceRules(Request $request)
    {
        $request->validate([
            'office_latitude'        => 'required|numeric',
            'office_longitude'       => 'required|numeric',
            'geofence_radius_meters' => 'required|numeric|min:10',
            'standard_in_time'       => 'required|date_format:H:i', 
            'late_tolerance_minutes' => 'required|integer|min:0',
        ]);

        $company = $request->user()->company; 
        
        $company->update([
            'office_latitude'        => $request->office_latitude,
            'office_longitude'       => $request->office_longitude,
            'geofence_radius_meters' => $request->geofence_radius_meters,
            'standard_in_time'       => $request->standard_in_time,
            'late_tolerance_minutes' => $request->late_tolerance_minutes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Aturan absensi dan lokasi kantor berhasil diperbarui.',
            'data'    => $company
        ]);
    }
}