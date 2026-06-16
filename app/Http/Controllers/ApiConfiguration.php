<?php

namespace App\Http\Controllers;

use App\Models\AssignmentSubmission;
use App\Models\Attendance;
use App\Models\DisciplineForm;
use App\Models\ExamResult;
use App\Models\ParentInvoicePayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use App\Models\StudentClass;
use App\Models\User;
use App\Models\VisitorLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ApiConfiguration extends Controller
{
    public function myResults(Request $request)
    {
        $userId = Auth::id();

        $results = ExamResult::with(['exam', 'class'])
            ->where('student_id', $userId)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $results
        ]);
    }


    public function myClasses(Request $request)
    {
        try {
            $userId = Auth::id();

            $studentClasses = StudentClass::with([
                'schoolClass.subjects.teacher'
            ])->where('student_id', $userId)->get();

            if ($studentClasses->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No classes found for this user.',
                    'data' => []
                ], 404);
            }

            $classes = $studentClasses->map(function ($studentClass) {
                $schoolClass = $studentClass->schoolClass;

                $subjects = $schoolClass->subjects->map(function ($subject) {
                    $teacherUser = $subject->teacherUser;
                    $teacherProfile = $teacherUser?->teacherProfile;

                    return [
                        'subject' => $subject->toArray(),
                    ];
                });

                return [
                    'status' => $studentClass->status, // from student_classes table
                    'academic_year' => $studentClass->academic_year,
                    'school_class' => array_merge(
                        $schoolClass->toArray(),
                        [
                            'subjects' => $subjects
                        ]
                    )
                ];
            });

            // Sort classes: active first
            $sorted = $classes->sortByDesc(fn($c) => $c['status'] === 'active')->values();

            return response()->json([
                'success' => true,
                'data' => $sorted
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTrace() : [],
            ], 500);
        }
    }



    public function myAssignments(Request $request)
    {
        $userId = Auth::id();

        $assignments = AssignmentSubmission::where('student_id', $userId)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $assignments
        ]);
    }

    public function myPayments(Request $request)
    {
        $userId = Auth::id();

        $payments = ParentInvoicePayment::with(['invoice.parentGuardian.user'])
            ->whereHas('invoice.parentGuardian.linkedStudents', fn($query) => $query->where('student_id', $userId))
            ->get()
            ->map(function ($payment) {
                $invoice = $payment->invoice;

                return [
                    'receipt_number' => $payment->receipt_number,
                    'invoice_number' => $invoice?->invoice_number,
                    'total_fees' => $invoice?->total_amount,
                    'amount_paid' => $payment->amount,
                    'month' => $invoice?->billing_month,
                    'billing_year' => $invoice?->billing_year,
                    'payment_date' => $payment->payment_date,
                    'payment_method' => $payment->payment_method,
                    'family_code' => $invoice?->family_code,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $payments
        ]);
    }


    public function myVisitors(Request $request)
    {
        $userId = Auth::id();

        $visitors = VisitorLog::where('person_to_meet', $userId)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $visitors
        ]);
    }

    public function myAttendance(Request $request)
    {
        $userId = Auth::id();

        $attendances = Attendance::where('student_id', $userId)
            ->with(['subject:id,name', 'teacher:id,name']) // Adjust field names if needed
            ->get()
            ->groupBy(fn($record) => $record->subject->name ?? 'Unknown Subject')
            ->map(function ($group, $subjectName) {
                $firstRecord = $group->first();

                return [
                    'subject' => $subjectName,
                    'teacher' => $firstRecord->teacher->name ?? 'Unknown Teacher',
                    'records' => $group->map(function ($record) {
                        return [
                            'id' => $record->id,
                            'date' => $record->date,
                            'status' => $record->status === 1 ? 'Present' : 'Absent',
                        ];
                    })->values()
                ];
            })
            ->values(); // Reset numeric array keys

        return response()->json([
            'success' => true,
            'message' => 'Attendance records grouped by subject.',
            'data' => $attendances
        ]);
    }

    public function myProfile(Request $request)
    {
        $userId = Auth::id();

        $profile = DB::table('users')
            ->where('id', $userId)
            ->first();

        return response()->json([
            'success' => true,
            'data' => $profile
        ]);
    }

    public function printTimetable()
    {
        $userId = Auth::id();

        // Find the active class for the authenticated student
        $studentClass = StudentClass::where('student_id', $userId)
            ->where('status', 'active')
            ->with('schoolClass', 'schoolClass.subjects.schedules')
            ->first();

        if (!$studentClass || !$studentClass->schoolClass) {
            return response()->json([
                'success' => false,
                'message' => 'Active class not found for this user.',
                'class' => null,
                'subjects' => []
            ], 404);
        }

        $class = $studentClass->schoolClass;

        return response()->json([
            'success' => true,
            'class' => $class,
        ]);
    }

    public function myAttendancy(): JsonResponse
    {
        $user = Auth::user();
        $currentYear = Carbon::now()->year;

        $attendances = Attendance::with('subject')
            ->where('student_id', $user->id)
            ->whereYear('date', $currentYear)
            ->orderBy('date')
            ->get();

        // Group by month name
        $grouped = $attendances->groupBy(function ($item) {
            return Carbon::parse($item->date)->format('F'); // e.g., January, February...
        });

        // Format response
        $result = [];

        foreach ($grouped as $month => $records) {
            $result[$month] = $records->map(function ($att) {
                return [
                    'day' => Carbon::parse($att->date)->day,
                    'subject' => $att->subject->name,
                    'status' => $att->status ? 'Present' : 'Absent',
                ];
            })->values();
        }

        return response()->json($result);
    }

    public function studentResults()
    {
        $studentId = auth()->id();

        $studentClasses = StudentClass::with([
            'schoolClass.subjects.teacher',
            'schoolClass.subjects.examResults' => function ($query) use ($studentId) {
                $query->where('student_id', $studentId)->with('exam');
            }
        ])
            ->where('student_id', $studentId)
            ->get()
            ->map(function ($studentClass) {
                $schoolClass = $studentClass->schoolClass;

                return [
                    'class' => $schoolClass->class_name ?? null,
                    'academic_year' => $studentClass->academic_year,
                    'status' => $studentClass->status,
                    'subjects' => $schoolClass && $schoolClass->subjects
                        ? $schoolClass->subjects->map(function ($subject) {
                            return [
                                'subject_name' => $subject->name,
                                'teacher' => [
                                    'id' => $subject->teacher->id ?? null,
                                    'name' => $subject->teacher->name ?? null,
                                    'email' => $subject->teacher->email ?? null,
                                ],
                                'exam_results' => $subject->examResults
                                    ? $subject->examResults->map(function ($result) {
                                        // Format exam type text similar to your BadgeColumn format
                                        $examType = $result->exam->exam_type ?? 'unknown';
                                        $formattedExamType = match ($examType) {
                                            'mid_term' => 'Mid-term Exam',
                                            'final' => 'Final Exam',
                                            default => 'Unknown',
                                        };

                                        return [
                                            'exam_name' => $result->exam->name ?? null,
                                            'exam_date' => $result->exam->date ?? null,
                                            'exam_type' => $formattedExamType,
                                            'marks' => $result->marks,
                                        ];
                                    })->toArray()
                                    : [],
                            ];
                        })
                        : [],
                ];
            });

        return response()->json($studentClasses);
    }


    public function getStudentAssignments()
    {
        $studentId = Auth::id();

        $activeStudentClass = StudentClass::with([
            'schoolClass.subjects.assignments.submissions' => function ($query) use ($studentId) {
                $query->where('student_id', $studentId);
            }
        ])
            ->where('student_id', $studentId)
            ->where('status', 'active')
            ->first();

        if (!$activeStudentClass) {
            return response()->json(['message' => 'No active class found for the user'], 404);
        }

        $schoolClass = $activeStudentClass->schoolClass;

        $appUrl = config('app.url');

        $data = [
            'class' => $schoolClass->class_name ?? null,
            'academic_year' => $activeStudentClass->academic_year,
            'status' => $activeStudentClass->status,
            'subjects' => $schoolClass->subjects->map(function ($subject) use ($appUrl) {
                return [
                    'subject_name' => $subject->name,
                    'assignments' => $subject->assignments->map(function ($assignment) use ($appUrl) {
                        return [
                            'title' => $assignment->title,
                            'description' => $assignment->description,
                            'deadline' => $assignment->deadline,
                            'file_path' => $assignment->file_path ? $appUrl . 'storage/' . $assignment->file_path : null,
                            'submissions' => $assignment->submissions->map(function ($submission) use ($appUrl) {
                                return [
                                    'id' => $submission->id,
                                    'file_path' => $submission->file_path ? $appUrl . 'storage/' . $submission->file_path : null,
                                    'submitted_at' => $submission->submitted_at,
                                    'status' => $submission->status,
                                ];
                            }),
                        ];
                    }),
                ];
            }),
        ];

        return response()->json($data);
    }

    public function uploadAssignment(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'assignment_id' => 'required',
                'file_path' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png,zip',
                'description' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $studentId = Auth::id();

            // Upload file
            $filePath = $request->file('file_path')->store('assignmentSubmission', 'public');

            // Check if submission exists
            $submission = AssignmentSubmission::where('assignment_id', $request->assignment_id)
                ->where('student_id', $studentId)
                ->first();

            if ($submission) {
                $submission->update([
                    'file_path' => $filePath,
                    'description' => $request->description,
                    'submitted_at' => now(),
                    'status' => 'pending',
                ]);
            } else {
                AssignmentSubmission::create([
                    'assignment_id' => $request->assignment_id,
                    'student_id' => $studentId,
                    'file_path' => $filePath,
                    'description' => $request->description,
                    'submitted_at' => now(),
                    'status' => 'pending',
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Assignment submitted successfully.'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function storeToken(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'fcm_token' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();
            $user->fcm_token = $request->fcm_token;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'FCM token saved successfully',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save FCM token.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getAllNotifications(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.',
                ], 401);
            }

            $notifications = $user->notifications()->latest()->get();

            return response()->json([
                'success' => true,
                'notifications' => $notifications,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve notifications.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function markAsRead(Request $request)
    {
        try {
            $request->validate([
                'notification_id' => 'required|string',
            ]);

            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.',
                ], 401);
            }

            $notification = $user->notifications()->where('id', $request->notification_id)->first();

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found.',
                ], 404);
            }

            $notification->update([
                'read_at' => Carbon::now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read.',
                'notification' => $notification,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function parentProfile()
    {
        try {
            $user = Auth::user();

            $profile = User::with('children1.student')->find($user->id);

            $children = $profile->children1->map(function ($relation) {
                return [
                    'id' => $relation->student->id,
                    'name' => $relation->student->name,
                    'last_name' => $relation->student->last_name,
                    'email' => $relation->student->email,
                    'student_info' => $relation->student->student, // academic info
                    'avatar' => $relation->student->avatar,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $profile->id,
                    'name' => $profile->name,
                    'email' => $profile->email,
                    'avatar' => $profile->avatar, // 👈 Add this line
                    'children' => $children,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve parent profile.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function childPayments(Request $request)
    {
        $userId = $request->input('student_id');

        $payments = ParentInvoicePayment::with(['invoice.parentGuardian.user'])
            ->whereHas('invoice.parentGuardian.linkedStudents', fn($query) => $query->where('student_id', $userId))
            ->get()
            ->map(function ($payment) {
                $invoice = $payment->invoice;

                return [
                    'receipt_number' => $payment->receipt_number,
                    'invoice_number' => $invoice?->invoice_number,
                    'total_fees' => $invoice?->total_amount,
                    'amount_paid' => $payment->amount,
                    'month' => $invoice?->billing_month,
                    'billing_year' => $invoice?->billing_year,
                    'payment_date' => $payment->payment_date,
                    'payment_method' => $payment->payment_method,
                    'family_code' => $invoice?->family_code,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $payments
        ]);
    }

    public function childVisitors(Request $request)
    {
        $userId = $request->input('student_id');

        $visitors = VisitorLog::where('person_to_meet', $userId)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $visitors
        ]);
    }

    public function childAttendance(Request $request)
    {
        $student_id = $request->input('student_id');
        $user = User::where('id', $student_id)->first();
        $currentYear = Carbon::now()->year;

        $attendances = Attendance::with('subject')
            ->where('student_id', $user->id)
            ->whereYear('date', $currentYear)
            ->orderBy('date')
            ->get();

        // Group by month name
        $grouped = $attendances->groupBy(function ($item) {
            return Carbon::parse($item->date)->format('F'); // e.g., January, February...
        });

        // Format response
        $result = [];

        foreach ($grouped as $month => $records) {
            $result[$month] = $records->map(function ($att) {
                return [
                    'day' => Carbon::parse($att->date)->day,
                    'subject' => $att->subject->name,
                    'status' => $att->status ? 'Present' : 'Absent',
                ];
            })->values();
        }

        return response()->json($result);
    }


    public function getChildAssignments(Request $request)
    {
        $studentId = $request->input('student_id');

        $activeStudentClass = StudentClass::with([
            'schoolClass.subjects.assignments.submissions' => function ($query) use ($studentId) {
                $query->where('student_id', $studentId);
            }
        ])
            ->where('student_id', $studentId)
            ->where('status', 'active')
            ->first();

        if (!$activeStudentClass) {
            return response()->json(['message' => 'No active class found for the user'], 404);
        }

        $schoolClass = $activeStudentClass->schoolClass;

        $appUrl = config('app.url');

        $data = [
            'class' => $schoolClass->class_name ?? null,
            'academic_year' => $activeStudentClass->academic_year,
            'status' => $activeStudentClass->status,
            'subjects' => $schoolClass->subjects->map(function ($subject) use ($appUrl) {
                return [
                    'subject_name' => $subject->name,
                    'assignments' => $subject->assignments->map(function ($assignment) use ($appUrl) {
                        return [
                            'title' => $assignment->title,
                            'description' => $assignment->description,
                            'deadline' => $assignment->deadline,
                            'file_path' => $assignment->file_path ? $appUrl . 'storage/' . $assignment->file_path : null,
                            'submissions' => $assignment->submissions->map(function ($submission) use ($appUrl) {
                                return [
                                    'id' => $submission->id,
                                    'file_path' => $submission->file_path ? $appUrl . 'storage/' . $submission->file_path : null,
                                    'submitted_at' => $submission->submitted_at,
                                    'status' => $submission->status,
                                ];
                            }),
                        ];
                    }),
                ];
            }),
        ];

        return response()->json($data);
    }


    public function childDiscipline(Request $request)
    {
        $user = Auth::user();

        // Only get children where the parent has children1 relationship
        $children = $user->children1()->get();

        // Collect all discipline forms from all children where family_notified is true
        $disciplineRecords = $children->flatMap(function ($relation) {
            return ($relation->student->student->disciplineForms ?? collect())
                ->where('family_notified', true)
                ->map(function ($disciplineForm) {
                    return [
                        'id' => $disciplineForm->id,
                        'student_name' => optional($disciplineForm->student->user)->name,
                        'reason' => $disciplineForm->reason,
                        'action_taken' => $disciplineForm->status,
                        'family_notified' => $disciplineForm->family_notified,
                        'created_at' => $disciplineForm->created_at,
                    ];
                });
        });

        return response()->json([
            'success' => true,
            'message' => 'Discipline records for your children.',
            'data' => $disciplineRecords->values(),
        ]);
    }
}
