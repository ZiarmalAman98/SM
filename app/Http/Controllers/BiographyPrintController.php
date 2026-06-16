<?php

namespace App\Http\Controllers;

use App\Models\Biography;
use Illuminate\Http\Request;

class BiographyPrintController extends Controller
{
    public function __invoke(Biography $biography)
    {
        // You can authorize here if needed, e.g.:
        // $this->authorize('view', $biography);
        $settings = appReportSettings();

        return view('print.biography', [
            'biography'  => $biography,
            'settings' => $settings,
            'schoolName' => $settings['app_name'],
        ]);
    }
}
