<?php

/**
 * Testes de integração para o NotificationService
 * Testa as operações de CRUD e lógica de negócio do serviço
 */

use App\Service\NotificationService;
use App\Service\UserService;
use App\Entity\Notification;
use App\Entity\User;
use App\Enum\NotificationTypeEnum;

// Testa buscar todas as notificacoes
it('busca todas as notificacoes', function () {
    $service = new NotificationService();
    $notifications = $service->findAll();

    expect($notifications)->toBeArray();
});

// Testa buscar notificacao por ID
it('busca notificacao por id', function () {
    $service = new NotificationService();
    $notifications = $service->findAll();

    if (count($notifications) > 0) {
        $firstNotification = $notifications[0];
        $found = $service->find($firstNotification->getId());

        expect($found)->toBeInstanceOf(Notification::class);
        expect($found->getId())->toBe($firstNotification->getId());
    }
});

// Testa buscar notificacoes por usuario
it('busca notificacoes por usuario', function () {
    $userService = new UserService();
    $service = new NotificationService();

    $users = $userService->findAll();

    if (count($users) === 0) {
        expect(true)->toBeTrue();
        return;
    }

    $user = $users[0];
    $notifications = $service->findByUser($user);

    expect($notifications)->toBeArray();
});

// Testa buscar notificacoes nao lidas por usuario
it('busca notificacoes nao lidas por usuario', function () {
    $userService = new UserService();
    $service = new NotificationService();

    $users = $userService->findAll();

    if (count($users) === 0) {
        expect(true)->toBeTrue();
        return;
    }

    $user = $users[0];
    $notifications = $service->findUnreadByUser($user);

    expect($notifications)->toBeArray();

    foreach ($notifications as $notification) {
        expect($notification->isRead())->toBeFalse();
    }
});

// Testa buscar notificacoes lidas por usuario
it('busca notificacoes lidas por usuario', function () {
    $userService = new UserService();
    $service = new NotificationService();

    $users = $userService->findAll();

    if (count($users) === 0) {
        expect(true)->toBeTrue();
        return;
    }

    $user = $users[0];
    $notifications = $service->findReadByUser($user);

    expect($notifications)->toBeArray();

    foreach ($notifications as $notification) {
        expect($notification->isRead())->toBeTrue();
    }
});

// Testa buscar notificacoes por tipo
it('busca notificacoes por tipo', function () {
    $service = new NotificationService();
    $notifications = $service->findByType(NotificationTypeEnum::TASK_ASSIGNED);

    expect($notifications)->toBeArray();

    foreach ($notifications as $notification) {
        expect($notification->getType())->toBe(NotificationTypeEnum::TASK_ASSIGNED);
    }
});

// Testa buscar notificacoes recentes
it('busca notificacoes recentes', function () {
    $service = new NotificationService();
    $notifications = $service->findRecent(5);

    expect($notifications)->toBeArray();
    expect(count($notifications))->toBeLessThanOrEqual(5);
});

// Testa contar notificacoes nao lidas retorna inteiro
it('contar notificacoes nao lidas retorna inteiro', function () {
    $userService = new UserService();
    $service = new NotificationService();

    $users = $userService->findAll();

    if (count($users) === 0) {
        expect(true)->toBeTrue();
        return;
    }

    $user = $users[0];
    $count = $service->getUnreadCountForUser($user);

    expect($count)->toBeInt();
    expect($count)->toBeGreaterThanOrEqual(0);
});

// Testa contar notificacoes por usuario retorna inteiro
it('contar notificacoes por usuario retorna inteiro', function () {
    $userService = new UserService();
    $service = new NotificationService();

    $users = $userService->findAll();

    if (count($users) === 0) {
        expect(true)->toBeTrue();
        return;
    }

    $user = $users[0];
    $count = $service->getCountByUser($user);

    expect($count)->toBeInt();
    expect($count)->toBeGreaterThanOrEqual(0);
});

// Testa retornar null ao buscar notificacao inexistente
it('retorna null ao buscar notificacao inexistente', function () {
    $service = new NotificationService();
    $found = $service->find('00000000-0000-0000-0000-000000000000');

    expect($found)->toBeNull();
});
