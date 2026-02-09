<?php

declare(strict_types=1);

use App\Enum\EducationalPlanStatusEnum;

return [
    [
        'name' => 'Plano Básico',
        'value' => 99.89,
        'status' => EducationalPlanStatusEnum::ACTIVE,
        'startDate' => '2025-01-01',
    ],
    [
        'name' => 'Plano Premium',
        'value' => 159.59,
        'status' => EducationalPlanStatusEnum::ACTIVE,
        'startDate' => '2025-01-01',
    ],
    [
        'name' => 'Plano de Entrada',
        'value' => 9.09,
        'status' => EducationalPlanStatusEnum::CANCELED,
        'startDate' => '2024-06-01',
        'endDate' => '2024-12-31',
    ],
    [
        'name' => 'Plano Antigo',
        'value' => 59.90,
        'status' => EducationalPlanStatusEnum::EXPIRED,
        'startDate' => '2023-01-01',
        'endDate' => '2024-01-01',
    ],
    [
        'name' => 'Plano Premium Prime',
        'value' => 398.19,
        'status' => EducationalPlanStatusEnum::ACTIVE,
        'startDate' => '2025-02-01',
    ],
];
