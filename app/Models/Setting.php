<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'app_name',
        'hospital_name',
        'address',
        'phone',
        'email',
        'logo_path',
    ];

    /**
     * Get the single settings row (id = 1), creating it with sensible
     * defaults if it does not exist yet.
     */
    public static function current(): self
    {
        return static::query()->firstOrCreate(
            ['id' => 1],
            [
                'app_name' => config('app.name', 'Sistem Farmasi'),
                'hospital_name' => config('app.name', 'Sistem Farmasi'),
                'address' => null,
                'phone' => null,
                'email' => null,
                'logo_path' => null,
            ]
        );
    }

    /**
     * Safe accessor for the configured app name, used in places (like the
     * base layout's <title> tag) that render on every single page — falls
     * back to config('app.name') instead of throwing if the settings table
     * isn't reachable yet (fresh install before migrations, test DB, etc.).
     */
    public static function appName(): string
    {
        try {
            return static::current()->app_name ?: config('app.name', 'Sistem Farmasi');
        } catch (\Throwable $e) {
            return config('app.name', 'Sistem Farmasi');
        }
    }
}
