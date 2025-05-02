<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketDataStatusHistory extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'market_data_status_history';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'market_data_id',
        'changed_by',
        'old_status',
        'new_status',
        'comments',
    ];

    /**
     * Get the market data that owns this status history.
     */
    public function marketData(): BelongsTo
    {
        return $this->belongsTo(MarketData::class);
    }

    /**
     * Get the user that changed this status.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
} 