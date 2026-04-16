<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class InitialDataSeeder extends Seeder
{
    public function run(): void
    {
        Setting::setSetting('hotel_name', 'Mi Hotel', 'string', false);
        Setting::setSetting('hotel_email', 'info@hotel.com', 'string', false);
        Setting::setSetting('hotel_phone', '+1234567890', 'string', false);
        Setting::setSetting('hotel_address', 'Dirección del hotel', 'string', false);
        Setting::setSetting('timezone', 'America/New_York', 'string', false);
        Setting::setSetting('currency', 'USD', 'string', false);
        Setting::setSetting('tax_rate', 0.15, 'decimal', false);
        Setting::setSetting('checkin_time', '14:00', 'string', false);
        Setting::setSetting('checkout_time', '11:00', 'string', false);
        Setting::setSetting('max_upload_size', 52428800, 'integer', false);
        Setting::setSetting('api_rate_limit', 100, 'integer', false);
        Setting::setSetting('mail_verification_required', true, 'boolean', false);
    }
}
