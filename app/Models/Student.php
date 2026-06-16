<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;


class Student extends Model
{

    protected $fillable = [
        'grand_father_name',
        'tazkira_number',
        'ton_number',
        'admission_no',
        'roll_no',
        'dob',
        'phone',
        'gender',
        'category_id',
        'caste',
        'admission_date',
        'photo_path',
        'blood_group',
        'address',
        'height',
        'weight',
        'user_id',
        'section_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->where('type', 'student');
    }


    public function parents()
    {
        return $this->hasMany(\App\Models\ParentStudent::class, 'student_id');
    }

    /**
     * Relationship with Class.
     */
    public function studentClasses(): HasMany
    {
        return $this->hasMany(StudentClass::class, 'student_id', 'user_id');
    }

    /**
     * Relationship with Category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(StudentCategory::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }


    public function submissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function disciplineForms()
    {
        return $this->hasMany(DisciplineForm::class);
    }

    public function getSubjectMarks($subjectName)
    {
        // dd($subjectName);
        if (!$this->user || !$this->user->exam_scores) {
            return '';
        }

        $midTerm = $this->user->exam_scores
            ->where('subject.name', $subjectName)
            ->where('exam.exam_type', 'mid_term')
            ->first();

        // dd($midTerm);

        $final = $this->user->exam_scores
            ->where('subject.name', $subjectName)
            ->where('exam.exam_type', 'final')
            ->first();

        $midTermMarks = $midTerm ? $midTerm->marks : '';
        $finalMarks = $final ? $final->marks : '';

        return $midTermMarks . '/' . $finalMarks;
    }

    public function getMidTermMark($subjectName)
    {
        if (!$this->user || !$this->user->exam_scores) {
            return '';
        }

        $examResult = $this->user->exam_scores
            ->filter(function($score) use ($subjectName) {
                return trim($score->subject->name) === trim($subjectName) && $score->exam->exam_type === 'mid_term';
            })
            ->first();

        return $examResult ? $examResult->marks : '';
    }

    public function getFinalMark($subjectName)
    {
        if (!$this->user || !$this->user->exam_scores) {
            return '';
        }

        $examResult = $this->user->exam_scores
            ->filter(function($score) use ($subjectName) {
                return trim($score->subject->name) === trim($subjectName) && $score->exam->exam_type === 'final';
            })
            ->first();

        return $examResult ? $examResult->marks : '';
    }



    // Mid-term attendance (Jan–May)
    public function getMidTermAttendance($type = null)
    {
        $query = \App\Models\ClassAttendance::where('student_id', $this->id)
            ->whereYear('date', 2025)
            ->whereMonth('date', '<=', 5);

        if (is_null($type)) {
            // return all types at once
            return [
                'present' => $query->clone()->where('status', 1)->count(),
                'absent'  => $query->clone()->where('status', 0)->count(),
                'leave'   => $query->clone()->where('is_leave', 1)->count(),
                'sick'    => $query->clone()->where('is_sick', 1)->count(),
            ];
        }

        return match ((int) $type) {
            1 => $query->where('status', 1)->count(), // ✅ Present
            0 => $query->where('status', 0)->count(), // ❌ Absent
            2 => $query->where('is_leave', 1)->count(), // ⛱ Leave
            3 => $query->where('is_sick', 1)->count(),  // 🤒 Sick
            default => 0,
        };
    }

    // Final-year attendance (whole 2025)
    public function getFinalAttendance($type = null)
    {
        $query = \App\Models\ClassAttendance::where('student_id', $this->id)
            ->whereYear('date', 2025);

        if (is_null($type)) {
            return [
                'present' => $query->clone()->where('status', 1)->count(),
                'absent'  => $query->clone()->where('status', 0)->count(),
                'leave'   => $query->clone()->where('is_leave', 1)->count(),
                'sick'    => $query->clone()->where('is_sick', 1)->count(),
            ];
        }

        return match ((int) $type) {
            1 => $query->where('status', 1)->count(), // ✅ Present
            0 => $query->where('status', 0)->count(), // ❌ Absent
            2 => $query->where('is_leave', 1)->count(), // ⛱ Leave
            3 => $query->where('is_sick', 1)->count(),  // 🤒 Sick
            default => 0,
        };
    }







}
