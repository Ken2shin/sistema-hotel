<?php

namespace App\Providers;

use App\Models\Client;
use App\Models\Reservation;
use App\Models\Report;
use App\Models\Room;
use App\Policies\ClientPolicy;
use App\Policies\ReservationPolicy;
use App\Policies\ReportPolicy;
use App\Policies\RoomPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Client::class => ClientPolicy::class,
        Reservation::class => ReservationPolicy::class,
        Report::class => ReportPolicy::class,
        Room::class => RoomPolicy::class,
    ];

    public function boot(): void
    {
    }
}
