<?php

namespace App\Support;

use App\Models\SiteSetting;
use Carbon\Carbon;

class ChatAvailability
{
    public static function isOpen(): bool
    {
        $settings = SiteSetting::query()
            ->whereIn('key', ['chat_enabled', 'chat_working_days', 'chat_open_time', 'chat_close_time', 'chat_timezone'])
            ->pluck('value', 'key')
            ->all();

        if (($settings['chat_enabled'] ?? '1') !== '1') {
            return false;
        }

        $now = Carbon::now($settings['chat_timezone'] ?? 'Africa/Lagos');
        $days = json_decode($settings['chat_working_days'] ?? '["Monday","Tuesday","Wednesday","Thursday","Friday"]', true);

        if (! is_array($days) || ! in_array($now->format('l'), $days, true)) {
            return false;
        }

        $open = $now->copy()->setTimeFromTimeString($settings['chat_open_time'] ?? '08:00');
        $close = $now->copy()->setTimeFromTimeString($settings['chat_close_time'] ?? '17:00');

        return $now->greaterThanOrEqualTo($open) && $now->lessThan($close);
    }
}
