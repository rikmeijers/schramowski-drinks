<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class RentalOrderAttachment extends Model
{
    // Allow mass assignment for our controlled fields
    protected $guarded = [];

    public function rentalOrder(): BelongsTo
    {
        return $this->belongsTo(RentalOrder::class);
    }

    protected static function booted(): void
    {
        static::deleting(function (self $attachment) {
            try {
                Storage::disk($attachment->disk)->delete($attachment->path);
            } catch (\Throwable) {
                // Don't block DB delete if file is already gone.
            }
        });
    }
}
