<?php

namespace App\Models;

use App\Traits\GuardHelpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Student extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, GuardHelpers;

    /**
     * The guard associated with the model.
     *
     * @var string
     */
    protected $guard_name = 'student';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'name',
        'email',
        'password',
        'phone',
        'date_of_birth',
        'gender',
        'address',
        'profile_photo',
        'enrollment_date',
        'graduation_date',
        'status',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
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
            'date_of_birth' => 'date',
            'enrollment_date' => 'date',
            'graduation_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Generate unique student ID
     */
    public static function generateStudentId(): string
    {
        $lastStudent = self::latest('id')->first();
        $lastId = $lastStudent ? (int) substr($lastStudent->student_id, 3) : 0;
        return 'STU' . str_pad($lastId + 1, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Check if student is active
     */
    public function isActive(): bool
    {
        return $this->is_active && $this->hasGuardRole('Active Student');
    }

    /**
     * Check if student is graduated
     */
    public function isGraduated(): bool
    {
        return $this->hasGuardRole('Graduated Student') || 
               ($this->graduation_date && $this->graduation_date->isPast());
    }

    /**
     * Check if student is suspended
     */
    public function isSuspended(): bool
    {
        return $this->hasGuardRole('Suspended Student') || !$this->is_active;
    }

    /**
     * Get student's age
     */
    public function getAge(): ?int
    {
        return $this->date_of_birth ? $this->date_of_birth->age : null;
    }

    /**
     * Get student's full name with ID
     */
    public function getFullNameWithId(): string
    {
        return "{$this->name} ({$this->student_id})";
    }

    /**
     * Get student's status display
     */
    public function getStatusDisplay(): string
    {
        if ($this->isSuspended()) {
            return 'Suspended';
        }
        
        if ($this->isGraduated()) {
            return 'Graduated';
        }
        
        if ($this->isActive()) {
            return 'Active';
        }
        
        return 'Inactive';
    }

    /**
     * Check if student can enroll in courses
     */
    public function canEnrollInCourses(): bool
    {
        return $this->isActive() && $this->hasGuardPermission('enroll_courses');
    }

    /**
     * Check if student can view grades
     */
    public function canViewGrades(): bool
    {
        return $this->hasGuardPermission('view_grades');
    }

    /**
     * Check if student can download certificates
     */
    public function canDownloadCertificates(): bool
    {
        return $this->hasGuardPermission('download_certificates');
    }

    /**
     * Scope to get active students
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->whereHas('roles', function ($q) {
                        $q->where('name', 'Active Student')
                          ->where('guard_name', 'student');
                    });
    }

    /**
     * Scope to get graduated students
     */
    public function scopeGraduated($query)
    {
        return $query->whereHas('roles', function ($q) {
            $q->where('name', 'Graduated Student')
              ->where('guard_name', 'student');
        });
    }

    /**
     * Scope to get suspended students
     */
    public function scopeSuspended($query)
    {
        return $query->whereHas('roles', function ($q) {
            $q->where('name', 'Suspended Student')
              ->where('guard_name', 'student');
        });
    }

    /**
     * Scope to search students by name or ID
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('student_id', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }

    /**
     * Boot method to assign default role and generate student ID
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($student) {
            // Generate student ID if not provided
            if (empty($student->student_id)) {
                $student->student_id = self::generateStudentId();
            }

            // Set default enrollment date
            if (empty($student->enrollment_date)) {
                $student->enrollment_date = now();
            }
        });

        static::created(function ($student) {
            // Assign default role if no role is assigned
            if ($student->roles()->count() === 0) {
                $student->assignGuardRole('Active Student');
            }
        });
    }
}
