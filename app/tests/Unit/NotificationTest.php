<?php

/**
 * Testes unitários para a entidade Notification
 * Cobertura: criação, getters, setters e métodos de negócio
 */

use App\Entity\Notification;
use App\Entity\User;
use App\Enum\NotificationTypeEnum;

// Helper para criar um usuário de teste
function createNotificationTestUser(): User
{
    return new User(
        name: 'Test User',
        email: 'test@email.com',
        passwordHash: password_hash('senha123', PASSWORD_BCRYPT)
    );
}

// Testa a criação de uma notificação com dados válidos
it('cria uma notificação com sucesso', function () {
    $user = createNotificationTestUser();
    
    $notification = new Notification(
        user: $user,
        type: NotificationTypeEnum::TASK_ASSIGNED,
        title: 'Nova tarefa',
        message: 'Você foi atribuído a uma nova tarefa'
    );

    expect($notification)->toBeInstanceOf(Notification::class);
    expect($notification->getTitle())->toBe('Nova tarefa');
    expect($notification->getMessage())->toBe('Você foi atribuído a uma nova tarefa');
    expect($notification->getType())->toBe(NotificationTypeEnum::TASK_ASSIGNED);
    expect($notification->getUser())->toBe($user);
});

// Testa que o UUID é gerado automaticamente
it('gera UUID automaticamente ao criar notificação', function () {
    $user = createNotificationTestUser();
    
    $notification = new Notification(
        user: $user,
        type: NotificationTypeEnum::TASK_ASSIGNED,
        title: 'Teste',
        message: 'Mensagem de teste'
    );

    expect($notification->getId())->toBeString();
    expect(strlen($notification->getId()))->toBe(36);
});

// Testa que a notificação inicia não lida
it('inicia como não lida', function () {
    $user = createNotificationTestUser();
    
    $notification = new Notification(
        user: $user,
        type: NotificationTypeEnum::TASK_ASSIGNED,
        title: 'Teste',
        message: 'Mensagem de teste'
    );

    expect($notification->isRead())->toBeFalse();
    expect($notification->getReadAt())->toBeNull();
});

// Testa o método markAsRead
it('marca notificação como lida', function () {
    $user = createNotificationTestUser();
    
    $notification = new Notification(
        user: $user,
        type: NotificationTypeEnum::TASK_ASSIGNED,
        title: 'Teste',
        message: 'Mensagem de teste'
    );

    $notification->markAsRead();

    expect($notification->isRead())->toBeTrue();
    expect($notification->getReadAt())->toBeInstanceOf(DateTime::class);
});

// Testa o método markAsUnread
it('marca notificação como não lida', function () {
    $user = createNotificationTestUser();
    
    $notification = new Notification(
        user: $user,
        type: NotificationTypeEnum::TASK_ASSIGNED,
        title: 'Teste',
        message: 'Mensagem de teste'
    );

    $notification->markAsRead();
    $notification->markAsUnread();

    expect($notification->isRead())->toBeFalse();
    expect($notification->getReadAt())->toBeNull();
});

// Testa o método getTimeElapsed
it('retorna tempo decorrido desde criação', function () {
    $user = createNotificationTestUser();
    
    $notification = new Notification(
        user: $user,
        type: NotificationTypeEnum::TASK_ASSIGNED,
        title: 'Teste',
        message: 'Mensagem de teste'
    );

    // Verifica que o tempo decorrido é retornado (acabou de ser criada)
    expect($notification->getTimeElapsed())->toBe('Just now');
});

// Testa o setter de título
it('atualiza o título da notificação', function () {
    $user = createNotificationTestUser();
    
    $notification = new Notification(
        user: $user,
        type: NotificationTypeEnum::TASK_ASSIGNED,
        title: 'Título Antigo',
        message: 'Mensagem'
    );

    $notification->setTitle('Novo Título');

    expect($notification->getTitle())->toBe('Novo Título');
});

// Testa o setter de mensagem
it('atualiza a mensagem da notificação', function () {
    $user = createNotificationTestUser();
    
    $notification = new Notification(
        user: $user,
        type: NotificationTypeEnum::TASK_ASSIGNED,
        title: 'Título',
        message: 'Mensagem Antiga'
    );

    $notification->setMessage('Nova Mensagem');

    expect($notification->getMessage())->toBe('Nova Mensagem');
});

// Testa que a data de criação é definida automaticamente
it('define data de criação automaticamente', function () {
    $user = createNotificationTestUser();
    
    $notification = new Notification(
        user: $user,
        type: NotificationTypeEnum::TASK_ASSIGNED,
        title: 'Teste',
        message: 'Mensagem de teste'
    );

    expect($notification->getCreatedAt())->toBeInstanceOf(DateTime::class);
});
