<?php

declare(strict_types=1);

use App\Enum\TaskPriorityEnum;

return [
    [
        'title' => 'Implementar autenticação de usuários',
        'description' => 'Criar sistema de login e registro com JWT',
        'priority' => TaskPriorityEnum::HIGH,
        'order' => 1,
        'startDate' => new DateTime('2025-02-10'),
        'dueDate' => new DateTime('2025-02-15'),
    ],
    [
        'title' => 'Comprar mantimentos para a semana',
        'description' => 'Lista: arroz, feijão, frutas, legumes, carne',
        'priority' => TaskPriorityEnum::MEDIUM,
        'order' => 2,
        'dueDate' => new DateTime('2025-02-11'),
    ],
    [
        'title' => 'Estudar React Hooks',
        'description' => 'Fazer curso sobre useState, useEffect e useContext',
        'priority' => TaskPriorityEnum::MEDIUM,
        'order' => 3,
        'dueDate' => new DateTime('2025-02-20'),
    ],
    [
        'title' => 'Marcar consulta médica',
        'description' => 'Agendar check-up anual',
        'priority' => TaskPriorityEnum::HIGH,
        'order' => 4,
        'dueDate' => new DateTime('2025-02-12'),
    ],
    [
        'title' => 'Preparar apresentação do projeto',
        'description' => 'Criar slides e ensaiar apresentação para cliente',
        'priority' => TaskPriorityEnum::URGENT,
        'order' => 5,
        'startDate' => new DateTime('2025-02-09'),
        'dueDate' => new DateTime('2025-02-14'),
    ],
    [
        'title' => 'Pagar contas do mês',
        'description' => 'Luz, água, internet e cartão de crédito',
        'priority' => TaskPriorityEnum::HIGH,
        'order' => 6,
        'dueDate' => new DateTime('2025-02-10'),
    ],
    [
        'title' => 'Revisar código do módulo de vendas',
        'description' => 'Code review e refatoração',
        'priority' => TaskPriorityEnum::MEDIUM,
        'order' => 7,
        'dueDate' => new DateTime('2025-02-18'),
    ],
    [
        'title' => 'Organizar armário',
        'description' => 'Separar roupas para doação',
        'priority' => TaskPriorityEnum::LOW,
        'order' => 8,
    ],
    [
        'title' => 'Planejar menu da semana',
        'description' => 'Definir refeições e fazer lista de compras',
        'priority' => TaskPriorityEnum::MEDIUM,
        'order' => 9,
        'dueDate' => new DateTime('2025-02-09'),
    ],
    [
        'title' => 'Atualizar currículo',
        'description' => 'Adicionar novas habilidades e experiências',
        'priority' => TaskPriorityEnum::LOW,
        'order' => 10,
    ],
];
