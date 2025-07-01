<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\TaskHistory;

class Task extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'priority',
        'status',
        'due_date',
        'public_token',
        'public_token_expires_at',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'public_token_expires_at' => 'datetime',
    ];

    protected $keyType = 'string';
    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function generatePublicToken(int $hours = 24)
    {
        $this->public_token = Str::random(32);
        $this->public_token_expires_at = Carbon::now()->addHours($hours);
        $this->save();

        return $this->public_token;
    }

    public function isPublicTokenValid()
    {
        return $this->public_token && $this->public_token_expires_at && $this->public_token_expires_at->isFuture();
    }

    public function histories()
    {
        return $this->hasMany(TaskHistory::class);
    }
}
