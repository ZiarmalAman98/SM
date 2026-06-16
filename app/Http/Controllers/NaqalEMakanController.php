<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
use App\Models\SchoolClass;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NaqalEMakanController extends Controller
{
    public function print(Request $request)
    {
        $data = $request->validate([
            'branch_id' => 'required|integer|exists:branches,id',
            'class_id' => 'required|integer|exists:school_classes,id',
            'academic_year' => 'nullable|string',
            'student_id' => 'required|integer|exists:users,id',
        ]);

        $branch = Branch::findOrFail($data['branch_id']);
        $class = SchoolClass::findOrFail($data['class_id']);
        $student = User::where('type', 'student')
            ->with(['student', 'branch', 'studentClasses', 'latestEnrollment'])
            ->findOrFail($data['student_id']);

        // General settings
        $settings = AppSetting::pluck('value', 'key')->toArray();

        // App settings and dynamic logo
        $appSettings = AppSetting::where('tab', 'app')->pluck('value', 'key')->toArray();
        $logoSrc = null;
        if (!empty($appSettings['app_logo'])) {
            $appLogo = $appSettings['app_logo'];
            if (Str::startsWith($appLogo, ['http://', 'https://', 'data:', '/'])) {
                $logoSrc = $appLogo;
            } elseif (Storage::disk('public')->exists($appLogo)) {
                $logoSrc = Storage::url($appLogo);
            } else {
                $logoSrc = asset($appLogo);
            }
        }

        // Serial number (allow override via request)
        $serialNo = (string) $request->query('no', $student->student->roll_no ?? $student->id);

        $lang = strtolower((string) $request->query(    'lang', ''));
        $rtlLocales = ['fa', 'ps', 'ar', 'fa_AF', 'ps_AF'];
        $appLocale = strtolower(app()->getLocale());

        // Default to English; use RTL template for specific locales unless overridden via ?lang
        if ($lang) {
            $view = in_array($lang, $rtlLocales, true) ? 'print.naqal-e-makan' : 'print.naqal-e-makan-en';
        } else {
            $view = in_array($appLocale, $rtlLocales, true) ? 'print.naqal-e-makan' : 'print.naqal-e-makan-en';
        }

        return view($view, [
            'branch' => $branch,
            'class' => $class,
            'student' => $student,
            'academic_year' => $data['academic_year'] ?? null,
            'settings' => $settings,
            'appSettings' => $appSettings,
            'logoSrc' => $logoSrc,
            'serialNo' => $serialNo,
        ]);
    }
}


