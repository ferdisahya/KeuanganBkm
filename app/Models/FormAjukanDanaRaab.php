<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormAjukanDanaRaab extends Model
{
    use HasFactory;

    protected $table = 'form_ajukan_dana_raab';

    protected $fillable = [
        'user_id',
        'tanggal',
        'file',
        'nominal',
        'keterangan',
        'status',
        'pm_approved_at',
        'pm_approved_by',
        'do_approved_at',
        'do_approved_by',
        'dir_approved_at',
        'dir_approved_by',
        'rejected_at',
        'rejected_by',
        'rejection_reason',
        'bukti',
        'bukti_uploaded_at',
        'bukti_uploaded_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'nominal' => 'decimal:2',
        'pm_approved_at' => 'datetime',
        'do_approved_at' => 'datetime',
        'dir_approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'bukti_uploaded_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pmUser()
    {
        return $this->belongsTo(User::class, 'pm_approved_by');
    }

    public function doUser()
    {
        return $this->belongsTo(User::class, 'do_approved_by');
    }

    public function dirUser()
    {
        return $this->belongsTo(User::class, 'dir_approved_by');
    }

    public function rejectedByUser()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function buktiUser()
    {
        return $this->belongsTo(User::class, 'bukti_uploaded_by');
    }

    // Requirements based on nominal
    public function needsPmApproval(): bool
    {
        return true;
    }

    public function needsDoApproval(): bool
    {
        return false;
    }

    public function needsDirApproval(): bool
    {
        return (float) $this->nominal > 2000000;
    }

    public function isPmApproved(): bool
    {
        return !is_null($this->pm_approved_at);
    }

    public function isDoApproved(): bool
    {
        return !is_null($this->do_approved_at);
    }

    public function isDirApproved(): bool
    {
        return !is_null($this->dir_approved_at);
    }

    public function isRejected(): bool
    {
        return $this->status === 'Ditolak' || !is_null($this->rejected_at);
    }

    public function isApprovedAll(): bool
    {
        if ($this->isRejected()) {
            return false;
        }

        if (!$this->isPmApproved()) {
            return false;
        }

        if ($this->needsDoApproval() && !$this->isDoApproved()) {
            return false;
        }

        if ($this->needsDirApproval() && !$this->isDirApproved()) {
            return false;
        }

        return true;
    }

    public function canUserApprove(?User $user): bool
    {
        if (!$user || $this->isRejected() || $this->status === 'Approved All') {
            return false;
        }

        if ($user->isProjectManager() && !$this->isPmApproved()) {
            return true;
        }

        if ($user->isDirekturOperasional() && $this->needsDoApproval() && !$this->isDoApproved()) {
            // Require previous tier approval if hierarchical
            return $this->isPmApproved();
        }

        if ($user->isDirector() && $this->needsDirApproval() && !$this->isDirApproved()) {
            // Require previous tier approval if hierarchical
            return $this->isPmApproved();
        }

        return false;
    }

    public function canUserReject(?User $user): bool
    {
        if (!$user || $this->isRejected() || $this->status === 'Approved All') {
            return false;
        }

        if ($user->isProjectManager()) {
            return true;
        }

        if ($user->isDirekturOperasional() && $this->needsDoApproval()) {
            return true;
        }

        if ($user->isDirector() && $this->needsDirApproval()) {
            return true;
        }

        return false;
    }

    public function checkAndUpdateStatus(): void
    {
        if ($this->isRejected()) {
            $this->status = 'Ditolak';
        } elseif ($this->isApprovedAll()) {
            $this->status = 'Approved All';
        } else {
            $this->status = 'Pending';
        }
        $this->save();
    }
}
