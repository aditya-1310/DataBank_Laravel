<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketData extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'symbol',
        'open',
        'high',
        'low',
        'close',
        'volume',
        'is_approved',
    ];

    protected $casts = [
        'date' => 'date',
        'open' => 'decimal:2',
        'high' => 'decimal:2',
        'low' => 'decimal:2',
        'close' => 'decimal:2',
        'volume' => 'integer',
        'is_approved' => 'boolean',
    ];

    /**
     * Get the user who submitted the market data.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
} 