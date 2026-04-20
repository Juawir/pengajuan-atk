<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResetPasswordRequest extends Model
{
    protected $fillable = [
        'email',
        'status',
        'resolved_by',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    /**
     * Admin yang menyelesaikan request.
     */
    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}
