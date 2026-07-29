<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use Prunable, SoftDeletes;

    protected $table = 'notifications';

    protected $fillable = [
        'recipient_user_id',
        'sender_user_id',
        'publication_id',
        'publication_comment_id',
        'type',
        'title',
        'message',
        'read',
        'expires_at',
    ];

    protected $hidden = [
        'recipient_user_id',
        'sender_user_id',
        'publication_id',
        'publication_comment_id',
        'read',
    ];

    protected $casts = [
        'read' => 'boolean',
        'expires_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Notification $notification): void {
            if ($notification->expires_at !== null) {
                return;
            }

            $notification->expires_at = CarbonImmutable::now(config('notifications.timezone'))
                ->addMonthsNoOverflow(config('notifications.retention_months'));
        });
    }

    public function publication()
    {
        return $this->belongsTo(Publication::class);
    }

    public function comment()
    {
        return $this->belongsTo(PublicationComment::class, 'publication_comment_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_user_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_user_id');
    }

    /**
     * Registros vencidos. Las notificaciones anteriores a expires_at
     * conservan la retención calculada desde su fecha de creación.
     */
    public function prunable(): Builder
    {
        $now = CarbonImmutable::now(config('notifications.timezone'));
        $legacyThreshold = $now->subMonthsNoOverflow(config('notifications.retention_months'));

        return static::query()
            ->withTrashed()
            ->where(function (Builder $query) use ($now, $legacyThreshold): void {
                $query->where('expires_at', '<=', $now)
                    ->orWhere(function (Builder $legacy) use ($legacyThreshold): void {
                        $legacy->whereNull('expires_at')
                            ->where('created_at', '<=', $legacyThreshold);
                    });
            });
    }
}
