<?php

declare(strict_types=1);

use App\Enum\NotificationTypeEnum;

return [
    [
        'type' => NotificationTypeEnum::TASK_ASSIGNED,
        'title' => 'Nova tarefa atribuída',
        'message' => 'Você foi atribuído à tarefa "Implementar autenticação de usuários"',
    ],
    [
        'type' => NotificationTypeEnum::TASK_DUE_SOON,
        'title' => 'Tarefa próxima do vencimento',
        'message' => 'A tarefa "Comprar mantimentos para a semana" vence amanhã',
    ],
    [
        'type' => NotificationTypeEnum::COMMENT_ADDED,
        'title' => 'Novo comentário',
        'message' => 'Maria Santos comentou na tarefa "Preparar apresentação do projeto"',
    ],
    [
        'type' => NotificationTypeEnum::TASK_COMPLETED,
        'title' => 'Tarefa concluída',
        'message' => 'Pedro Oliveira concluiu a tarefa "Revisar código do módulo de vendas"',
    ],
    [
        'type' => NotificationTypeEnum::REMINDER,
        'title' => 'Lembrete de tarefa',
        'message' => 'Lembrete: Marcar consulta médica está agendada para hoje',
    ],
    [
        'type' => NotificationTypeEnum::TASK_OVERDUE,
        'title' => 'Tarefa atrasada',
        'message' => 'A tarefa "Pagar contas do mês" está atrasada',
    ],
    [
        'type' => NotificationTypeEnum::PROJECT_SHARED,
        'title' => 'Projeto compartilhado',
        'message' => 'Carlos Ferreira compartilhou o projeto "Trabalho" com você',
    ],
    [
        'type' => NotificationTypeEnum::TASK_ASSIGNED,
        'title' => 'Nova tarefa atribuída',
        'message' => 'Você foi atribuído à tarefa "Estudar React Hooks"',
    ],
    [
        'type' => NotificationTypeEnum::COMMENT_ADDED,
        'title' => 'Novo comentário',
        'message' => 'Ana Costa comentou: "Ótimo trabalho! Continue assim."',
    ],
    [
        'type' => NotificationTypeEnum::TASK_DUE_SOON,
        'title' => 'Tarefa próxima do vencimento',
        'message' => 'A tarefa "Planejar menu da semana" vence hoje',
    ],
];
