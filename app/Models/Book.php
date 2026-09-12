<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_name',
        'title',
        'author',
        'isbn',
        'category',
        'publisher',
        'edition',
        'total_copies',
        'available_copies',
        'shelf_location',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'total_copies'     => 'integer',
            'available_copies' => 'integer',
        ];
    }

    public function borrowings(): HasMany
    {
        return $this->hasMany(BookBorrowing::class);
    }
}
