<?php

namespace Tests\Unit;

use App\Domains\Administration\Services\BusinessTimeService;
use App\Domains\Identity\Tenant;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class BusinessTimeServiceTest extends TestCase
{
    public function test_add_business_minutes_skips_weekends()
    {
        $tenant = $this->createMock(Tenant::class);
        $tenant->method('getTimezone')->willReturn('UTC');
        $tenant->method('getHolidays')->willReturn([]);
        $tenant->method('getBusinessHours')->willReturn([
            'monday' => ['09:00-18:00'],
            'tuesday' => ['09:00-18:00'],
            'wednesday' => ['09:00-18:00'],
            'thursday' => ['09:00-18:00'],
            'friday' => ['09:00-18:00'],
        ]);

        $service = new BusinessTimeService();

        // Viernes a las 17:00
        $start = Carbon::create(2023, 10, 6, 17, 0, 0, 'UTC'); // 2023-10-06 was a Friday
        
        // Sumar 120 minutos (2 horas)
        // 1 hora se consume el viernes (hasta las 18:00)
        // La otra hora pasa al lunes a las 09:00, resultando en Lunes a las 10:00
        $end = $service->addBusinessMinutes($start, 120, $tenant);

        $this->assertEquals('2023-10-09 10:00:00', $end->format('Y-m-d H:i:s'));
    }
}
