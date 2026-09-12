<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookBorrowing extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_name',
        'book_id',
        'student_id',
        'borrowed_date',
        'due_date',
        'returned_date',
        'status',
        'remarks',
        'issued_by',
    ];

    protected function casts(): array
    {
        return [
            'borrowed_date' => 'date',
            'due_date'      => 'date',
            'returned_date' => 'date',
        ];
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function issuedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }
}
