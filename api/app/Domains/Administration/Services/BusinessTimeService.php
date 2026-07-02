<?php

namespace App\Domains\Administration\Services;

use App\Domains\Identity\Tenant;
use Carbon\Carbon;

class BusinessTimeService
{
    /**
     * Añade minutos hábiles a una fecha dada basándose en el Tenant.
     */
    public function addBusinessMinutes(Carbon $start, int $minutes, Tenant $tenant): Carbon
    {
        $timezone = $tenant->getTimezone();
        $current = $start->copy()->setTimezone($timezone);
        $holidays = $tenant->getHolidays();

        // Asumimos un horario estándar simple: Lunes a Viernes de 09:00 a 18:00
        // Para una implementación completa, aquí se leería el array de getBusinessHours() del Tenant.
        // Por simplicidad, implementamos la lógica base de horas hábiles fijas.

        $businessStartHour = 9;
        $businessEndHour = 18;

        while ($minutes > 0) {
            // Verificar si es fin de semana
            if ($current->isWeekend()) {
                $current->addDay()->startOfDay()->addHours($businessStartHour);

                continue;
            }

            // Verificar si es festivo
            if (in_array($current->format('Y-m-d'), $holidays)) {
                $current->addDay()->startOfDay()->addHours($businessStartHour);

                continue;
            }

            // Si es antes del horario laboral, saltar a la hora de inicio
            if ($current->hour < $businessStartHour) {
                $current->hour = $businessStartHour;
                $current->minute = 0;
                $current->second = 0;
            }

            // Calcular minutos restantes en el día de hoy
            $endOfDay = $current->copy()->setTime($businessEndHour, 0, 0);
            $minutesLeftToday = $current->diffInMinutes($endOfDay, false);

            if ($minutesLeftToday <= 0) {
                // Ya pasó la hora de cierre, saltar al día siguiente
                $current->addDay()->startOfDay()->addHours($businessStartHour);

                continue;
            }

            if ($minutes <= $minutesLeftToday) {
                $current->addMinutes($minutes);
                $minutes = 0;
            } else {
                $minutes -= $minutesLeftToday;
                $current->addDay()->startOfDay()->addHours($businessStartHour);
            }
        }

        return $current;
    }
}
