<?php

use App\Http\Controllers\ApiConfiguration;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public routes: Login and Register
// Route::post('/register_user', [AuthController::class, 'register_user']);
Route::post('/login_user', [AuthController::class, 'login_user']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->get('/dashboard-overview', [DashboardController::class, 'overview']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/my-result', [ApiConfiguration::class, 'myResults']);
});


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/my-class', [ApiConfiguration::class, 'myClasses']);
    // Route::get('/my-assignments', [ApiConfiguration::class, 'myAssignments']);
    Route::get('/my-payments', [ApiConfiguration::class, 'myPayments']);
    Route::get('/my-visitors', [ApiConfiguration::class, 'myVisitors']);
    Route::get('/my-attendance', [ApiConfiguration::class, 'myAttendance']);
    Route::get('/my-profile', [ApiConfiguration::class, 'myProfile']);
    Route::get('/my-timetable', [ApiConfiguration::class, 'printTimetable']);
    Route::get('/my-attendancy', [ApiConfiguration::class, 'myAttendancy']);
    Route::get('/my-exam-results', [ApiConfiguration::class, 'studentResults']);
    Route::get('/my-assignments', [ApiConfiguration::class, 'getStudentAssignments']);
    Route::post('/upload-assignment', [ApiConfiguration::class, 'uploadAssignment']);
    Route::post('/fcm-token', [ApiConfiguration::class, 'storeToken']);
    Route::get('/get-notifications', [ApiConfiguration::class, 'getAllNotifications']);
    Route::post('/read-notification', [ApiConfiguration::class, 'markAsRead']);


    Route::get("/parent", [ApiConfiguration::class, "parentProfile"]);
    Route::get('/child-payments', [ApiConfiguration::class, 'childPayments']);
    Route::get('/child-visitors', [ApiConfiguration::class, 'childVisitors']);
    Route::get('/child-attendance', [ApiConfiguration::class, 'childAttendance']);
    Route::get('/child-assignments', [ApiConfiguration::class, 'getChildAssignments']);
    Route::get('/child-discipline', [ApiConfiguration::class, 'childDiscipline']);
});
