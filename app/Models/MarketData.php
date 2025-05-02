<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarketData extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'market_data';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'date',
        'market_name',
        'product_name',
        'price',
        'quantity',
        'source',
        'status',
        'submitted_by',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'date',
        'price' => 'float',
        'quantity' => 'float',
    ];

    /**
     * Get the user that submitted this data.
     */
    public function submitter(): BelongsTo
    {
        // Try submitted_by first, then fall back to user_id
        if ($this->submitted_by) {
            return $this->belongsTo(User::class, 'submitted_by');
        }
        
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Boot method to sync user_id and submitted_by
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function($model) {
            // If only submitted_by is set, copy to user_id
            if ($model->submitted_by && !$model->user_id) {
                $model->user_id = $model->submitted_by;
            }
            
            // If only user_id is set, copy to submitted_by
            if ($model->user_id && !$model->submitted_by) {
                $model->submitted_by = $model->user_id;
            }
        });
    }

    /**
     * Get the status history records for this market data.
     */
    public function statusHistory(): HasMany
    {
        return $this->hasMany(MarketDataStatusHistory::class);
    }
    
    /**
     * Record a status change in the history
     */
    public function recordStatusChange($oldStatus, $newStatus, $userId = null, $comments = null): void
    {
        $this->statusHistory()->create([
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'changed_by' => $userId ?? auth()->id(),
            'comments' => $comments,
        ]);
    }
} 