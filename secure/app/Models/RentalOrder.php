<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RentalOrder extends Model
{
    protected $fillable = [
        'customer_name',
        'customer_street',
        'customer_city',
        'customer_license_plate',
        'customer_phone',
        'customer_email',
        'customer_id_number',
        'customer_driver_license_number',
        'rental_date',
        'return_date',
        'items',
        'notes',
        'outstanding_amount',
        'confirmation_sent_at',
        'reminder_sent_at',
        'overdue_sent_at',
    ];

    protected $casts = [
        'rental_date' => 'date',
        'return_date' => 'date',
        'items' => 'array',
        'outstanding_amount' => 'decimal:2',
        'confirmation_sent_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
        'overdue_sent_at' => 'datetime',
    ];

    public function attachments(): HasMany
    {
        return $this->hasMany(RentalOrderAttachment::class);
    }

    public function mailLogs(): HasMany
    {
        return $this->hasMany(RentalOrderMailLog::class)->latest();
    }

    public function photoAttachment(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->attachments->firstWhere('type', 'photo'),
        );
    }

    public function signatureAttachment(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->attachments->firstWhere('type', 'signature'),
        );
    }

    public function receiptAttachment(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->attachments->firstWhere('type', 'receipt'),
        );
    }

    public function hasOutstandingAmountData(): bool
    {
        return $this->outstanding_amount !== null
            || (bool) $this->receiptAttachment;
    }

    public function statusLabel(): string
    {
        if ($this->isOverdue()) {
            return $this->overdue_sent_at ? 'Überfällig (E-Mail gesendet)' : 'Überfällig';
        }

        if ($this->isDueToday()) {
            return $this->reminder_sent_at ? 'Heute fällig (Erinnerung gesendet)' : 'Heute fällig';
        }

        if ($this->reminder_sent_at) {
            return 'Erinnerung gesendet';
        }

        if ($this->confirmation_sent_at) {
            return 'Bestätigt';
        }

        // No label for initial state.
        return '';
    }

    public function statusBadgeClass(): string
    {
        if ($this->isOverdue()) return 'bg-danger';
        if ($this->isDueToday()) return 'bg-primary';
        if ($this->reminder_sent_at) return 'bg-warning text-dark';
        if ($this->confirmation_sent_at) return 'bg-success';

        // Neutral / hidden
        return 'bg-secondary';
    }

    public function isOverdue(): bool
    {
        // Only overdue if the return date is before today (yesterday or earlier)
        return $this->return_date && $this->return_date->lt(now()->startOfDay());
    }

    public function isDueToday(): bool
    {
        return $this->return_date && $this->return_date->isToday();
    }
}
