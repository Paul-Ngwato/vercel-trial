<?php

namespace App\Support;

/**
 * Bridges legacy emoji icon values (stored in the DB / chosen by admins)
 * to Bootstrap Icons class names so the guest pages render a consistent icon set.
 */
final class Icons
{
    /** Map of emoji (or plain keyword) -> Bootstrap Icons class. */
    public const MAP = [
        // WeddingSeeder information items
        '👔' => 'bi-handbag-fill',      // Dress code
        '🚗' => 'bi-car-front-fill',    // Parking
        '🚌' => 'bi-bus-front-fill',    // Transport
        '🏨' => 'bi-building-fill',     // Accommodation
        '📸' => 'bi-camera-fill',       // Photography
        '👶' => 'bi-balloon-fill',      // Children
        '🎁' => 'bi-gift-fill',         // Gifts
        '📞' => 'bi-telephone-fill',    // Contact

        // Extra choices offered in the admin picker
        '🍽️' => 'bi-basket2-fill',
        '🎵' => 'bi-music-note-beamed',
        '✈️' => 'bi-airplane',
        '💰' => 'bi-cash-coin',
        '🔑' => 'bi-key-fill',
        '📱' => 'bi-phone-fill',
        '🎭' => 'bi-brush-fill',
        '🎂' => 'bi-cake2-fill',
        '💐' => 'bi-flower2',
        '⛪' => 'bi-bank2',
        '🅿️' => 'bi-p-circle-fill',
        '🌊' => 'bi-water',

        // Fallbacks used across guest pages
        '📋' => 'bi-clipboard-check-fill',
        '📌' => 'bi-pin-map-fill',
        '💬' => 'bi-chat-heart-fill',
        '💌' => 'bi-envelope-heart-fill',
        '❤️' => 'bi-heart-fill',
        '📍' => 'bi-geo-alt-fill',
        '📅' => 'bi-calendar-check',
        '📖' => 'bi-book',
        '🎉' => 'bi-balloon-heart-fill',
        '💝' => 'bi-gift-fill',
        '✨' => 'bi-stars',
        '🧾' => 'bi-receipt',
    ];

    public static function bi(?string $value, string $fallback = 'bi-patch-question-fill'): string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return $fallback;
        }

        // Already a bootstrap icon class, e.g. "bi-car-front-fill"
        if (str_starts_with($value, 'bi-')) {
            return $value;
        }

        return self::MAP[$value] ?? $fallback;
    }
}
