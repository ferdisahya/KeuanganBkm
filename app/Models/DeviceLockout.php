<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceLockout extends Model
{
    protected $table = 'device_lockouts';

    protected $fillable = [
        'ip_address',
        'failed_attempts',
        'blocked_until',
        'block_reason',
        'unblocked_at',
        'unblocked_by',
    ];

    protected $casts = [
        'blocked_until' => 'datetime',
        'unblocked_at' => 'datetime',
    ];

    public function unblocker()
    {
        return $this->belongsTo(User::class, 'unblocked_by');
    }

    /**
     * Periksa apakah IP ini sedang diblokir aktif.
     */
    public function isCurrentlyBlocked(): bool
    {
        if ($this->unblocked_at !== null) {
            return false;
        }

        return $this->blocked_until !== null && $this->blocked_until->isFuture();
    }
}
