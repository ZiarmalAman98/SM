<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;


class User extends Authenticatable
{
    use HasRoles;
    use HasApiTokens;
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];
    public function classAttendances()
    {
        return $this->hasMany(ClassAttendance::class, 'student_id');
    }

    public function schoolClasses()   // keep the old method-name used earlier
    {
        return $this->hasManyThrough(
            SchoolClass::class,  // final model
            Subject::class,      // through model
            'teacher_id',        // Subject.teacher_id → User.id
            'id',                // SchoolClass.id (PK)
            'id',                // User.id
            'school_class_id'    // Subject.school_class_id → SchoolClass.id
        )->distinct();
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function parentGuardian(): HasOne
    {
        return $this->hasOne(ParentGuardian::class, 'user_id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function student(): HasOne
    {
        return $this->hasOne(Student::class, 'user_id');
    }

    public function latestEnrollment(): HasOneThrough
    {
        return $this->hasOneThrough(
            StudentClass::class, // final
            Student::class,      // through
            'user_id',           // Student.user_id -> users.id
            'student_id',        // StudentClass.student_id -> students.id
            'id',                // users.id
            'id'                 // students.id
        )
        ->orderByDesc('academic_year')   // newest year first
        ->orderByDesc('id');             // tie-breaker
    }

    

    public function teacher(): HasOne
    {
        return $this->hasOne(Teacher::class, 'user_id');
    }

    public function staff(): HasOne
    {
        return $this->hasOne(Staff::class, 'user_id');
    }

    public function studentClasses(): HasMany
    {
        return $this->hasMany(StudentClass::class, 'student_id');
    }

    public function feeGroupAssignments(): MorphMany
    {
        return $this->morphMany(FeeGroupAssignment::class, 'assignable');
    }

    public function contactDetails(): HasMany
    {
        return $this->hasMany(ContactDetail::class);
    }

    public function complaintsAsComplainant(): HasMany
    {
        return $this->hasMany(Complaint::class, 'complainant_id')->whereColumn('complainant_type', 'users.type');
    }

    /**
     * Get complaints where the user is the correspondent.
     */
    public function complaintsAsCorrespondent(): HasMany
    {
        return $this->hasMany(Complaint::class, 'correspondent_id')->whereColumn('correspondent_type', 'users.type');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ParentStudent::class, 'student_id');
    }

    public function children1(): HasMany
    {
        return $this->hasMany(ParentStudent::class, 'parent_guardian_id');
    }

    public function parent(): HasMany
    {
        return $this->hasMany(ParentStudent::class, 'student_id');
    }

    public function feePayments()
    {
        return $this->hasMany(FeePayment::class, 'student_id');
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class, 'teacher_id');
    }

    public function downloads(): HasMany
    {
        return $this->hasMany(DownloadCenter::class, 'uploaded_by');
    }

    public function addBooks()
    {
        return $this->hasMany(AddBook::class);
    }

    public function borrowedBooks(): HasMany
    {
        return $this->hasMany(BorrowBook::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'student_id');
    }

    public function employeeAttendances()
    {
        return $this->hasMany(EmployeeAttendance::class, 'employee_id');
    }

    public function exam_scores()
    {
        return $this->hasMany(ExamResult::class, 'student_id');
    }

    public function socialMediaLinks(): HasMany
    {
        return $this->hasMany(SocialMediaLink::class, 'user_id');
    }

    public function userDocuments(): HasMany
    {
        return $this->hasMany(UserDocument::class, 'user_id');
    }


    public function bonuses()
    {
        return $this->hasMany(Bonus::class, 'user_id');
    }

    public function advances()
    {
        return $this->hasMany(Advance::class, 'user_id');
    }

    public function deductions()
    {
        return $this->hasMany(Deduction::class, 'user_id');
    }


    public function taxes()
    {
        return $this->hasMany(Payroll::class, 'teacher_id');
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class, 'teacher_id');
    }

    /** @return HasMany<\App\Models\BorrowBook, self> */
    public function borrowBooks(): HasMany
    {
        return $this->hasMany(\App\Models\BorrowBook::class);
    }


    /** @return HasMany<\App\Models\EmployeeLeave, self> */
    public function employeeLeaves(): HasMany
    {
        return $this->hasMany(\App\Models\EmployeeLeave::class);
    }


    /** @return HasMany<\App\Models\Question, self> */
    public function questions(): HasMany
    {
        return $this->hasMany(\App\Models\Question::class);
    }

    public function getAvatarAttribute(): ?string
    {
        // Check if the user has a related Student model with a photo_path
        if ($this->student && $this->student->photo_path) {
            return Storage::url($this->student->photo_path);
        }

        // Check if the user has a related Teacher model with a photo
        if ($this->teacher && $this->teacher->photo) {
            return Storage::url($this->teacher->photo);
        }

        // Check if the user has a related Staff model with a photo
        if ($this->staff && $this->staff->photo) {
            return Storage::url($this->staff->photo);
        }

        // Return a default avatar if no photo is found
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=FFFFFF&background=111827';
    }

    public function submittedEvaluations(): HasMany
    {
        return $this->hasMany(EvaluationResponse::class, 'student_id');
    }

    /**
     * Evaluation responses received by this teacher.
     */
    public function receivedEvaluations(): HasMany
    {
        return $this->hasMany(EvaluationResponse::class, 'teacher_id');
    }
}
