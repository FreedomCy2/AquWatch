<?php

namespace App\Services;

use App\Models\FloodReading;
use App\Models\RainReading;
use App\Models\User;
use App\Models\UserNotification;

class AutoSensorAlertService
{
    public function __construct(private readonly FirebaseMessagingService $firebaseMessaging)
    {
    }

    public function onRainReading(RainReading $reading): void
    {
        if (! (bool) config('services.sensors.auto_alert_push', true)) {
            return;
        }

        $level = (string) $reading->intensity_level;

        if ($level !== 'heavy_rain') {
            return;
        }

        $title = 'Heavy rain detected';
        $message = sprintf('%s reported heavy rain.', (string) $reading->sensor_id);

        $this->createAndPushOnce($title, $message);
    }

    public function onFloodReading(FloodReading $reading): void
    {
        if (! (bool) config('services.sensors.auto_alert_push', true)) {
            return;
        }

        $status = (string) $reading->status;

        $title = match ($status) {
            'CRITICAL' => 'Critical flood alert',
            'FLASH FLOOD WARNING' => 'Flash flood warning',
            'NORMAL RISE', 'LEVEL 1 DETECTED' => 'Flood warning',
            default => '',
        };

        if ($title === '') {
            return;
        }

        $message = sprintf('%s reported %s.', (string) $reading->sensor_id, $status);

        $this->createAndPushOnce($title, $message);
    }

    private function createAndPushOnce(string $title, string $message): void
    {
        $cooldownSeconds = max(30, (int) config('services.sensors.auto_alert_cooldown_seconds', 180));

        $alreadyRecent = UserNotification::query()
            ->whereNull('user_id')
            ->where('title', $title)
            ->where('message', $message)
            ->where('created_at', '>=', now()->subSeconds($cooldownSeconds))
            ->exists();

        if ($alreadyRecent) {
            return;
        }

        $sentBy = User::query()->where('role', 'admin')->value('id')
            ?? User::query()->value('id');

        if (! $sentBy) {
            return;
        }

        $notification = UserNotification::query()->create([
            'user_id' => null,
            'title' => $title,
            'message' => $message,
            'sent_by' => (int) $sentBy,
        ]);

        $this->firebaseMessaging->sendUserNotification($notification);
    }
}
