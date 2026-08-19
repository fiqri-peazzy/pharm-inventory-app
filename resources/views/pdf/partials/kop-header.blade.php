@php
    $setting = \App\Models\Setting::current();
    $logoAbsolutePath = $setting->logo_path ? public_path($setting->logo_path) : null;
    $logoDataUri = null;
    if ($logoAbsolutePath && is_file($logoAbsolutePath)) {
        $mime = match (strtolower(pathinfo($logoAbsolutePath, PATHINFO_EXTENSION))) {
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'svg' => 'image/svg+xml',
            default => null,
        };
        if ($mime) {
            $logoDataUri = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoAbsolutePath));
        }
    }
    $contactLine = collect([
        $setting->phone ? 'Telp: ' . $setting->phone : null,
        $setting->email ? 'Email: ' . $setting->email : null,
    ])->filter()->implode('  |  ');
@endphp
<table style="width: 100%; border-collapse: collapse; margin-bottom: 0;">
    <tr>
        <td style="width: 70px; vertical-align: middle; padding: 0 12px 0 0;">
            @if ($logoDataUri)
                <img src="{{ $logoDataUri }}" style="width: 60px; height: 60px; object-fit: contain;">
            @endif
        </td>
        <td style="vertical-align: middle; text-align: center;">
            <div style="font-size: 15px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px;">
                {{ $setting->hospital_name ?: $setting->app_name }}
            </div>
            @if ($setting->address)
                <div style="font-size: 9.5px; margin-top: 2px;">{{ $setting->address }}</div>
            @endif
            @if ($contactLine)
                <div style="font-size: 9.5px;">{{ $contactLine }}</div>
            @endif
        </td>
        <td style="width: 70px;"></td>
    </tr>
</table>
<div style="border-bottom: 2.5px solid #000; margin-top: 8px; margin-bottom: 16px;"></div>
