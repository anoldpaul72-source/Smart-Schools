<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolIncome extends Model
{
    use HasFactory;

    protected $table = 'school_incomes';

    protected $fillable = [
        'school_name',
        'category',
        'source_title',
        'payer_name',
        'amount',
        'payment_date',
        'receipt_number',
        'payment_method',
        'academic_year',
        'notes',
        'recorded_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount'       => 'decimal:2',
    ];

    public const CATEGORIES = [
        'Mauzo ya Mazao'         => 'Mauzo ya Mazao (Farm & Agriculture)',
        'Ushuru wa Mama Ntilie'  => 'Ushuru wa Mama Ntilie / Wauza Vyakula (Food Vendors Levy)',
        'Kodi za Fremu'          => 'Kodi za Fremu za Biashara (Commercial Stalls Rent)',
        'Kumbi & Viwanja'        => 'Kukodisha Kumbi & Viwanja vya Shule (Hall & Grounds Hire)',
        'Miradi Mingine'         => 'Miradi Mingine ya Shule (Other Institutional Projects)',
    ];

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
