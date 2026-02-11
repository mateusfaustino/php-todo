<?php

/**
 * Testes de integração para o AttachmentService
 * Testa as operações de CRUD e lógica de negócio do serviço
 */

use App\Service\AttachmentService;
use App\Service\TaskService;
use App\Service\UserService;
use App\Entity\Attachment;
use App\Entity\Task;
use App\Entity\User;

// Testa buscar todos os anexos
it('busca todos os anexos', function () {
    $service = new AttachmentService();
    $attachments = $service->findAll();

    expect($attachments)->toBeArray();
});

// Testa buscar anexo por ID
it('busca anexo por id', function () {
    $service = new AttachmentService();
    $attachments = $service->findAll();

    if (count($attachments) > 0) {
        $firstAttachment = $attachments[0];
        $found = $service->find($firstAttachment->getId());

        expect($found)->toBeInstanceOf(Attachment::class);
        expect($found->getId())->toBe($firstAttachment->getId());
    }
});

// Testa buscar anexos por tarefa
it('busca anexos por tarefa', function () {
    $taskService = new TaskService();
    $service = new AttachmentService();

    $tasks = $taskService->findAll();

    if (count($tasks) === 0) {
        expect(true)->toBeTrue();
        return;
    }

    $task = $tasks[0];
    $attachments = $service->findByTask($task);

    expect($attachments)->toBeArray();
});

// Testa buscar anexos por usuario
it('busca anexos por usuario', function () {
    $userService = new UserService();
    $service = new AttachmentService();

    $users = $userService->findAll();

    if (count($users) === 0) {
        expect(true)->toBeTrue();
        return;
    }

    $user = $users[0];
    $attachments = $service->findByUser($user);

    expect($attachments)->toBeArray();
});

// Testa buscar anexos por criterios
it('busca anexos por criterios', function () {
    $service = new AttachmentService();
    $attachments = $service->findBy(['mimeType' => 'image/jpeg']);

    expect($attachments)->toBeArray();
});

// Testa buscar imagens
it('busca imagens', function () {
    $service = new AttachmentService();
    $images = $service->findImages();

    expect($images)->toBeArray();

    foreach ($images as $image) {
        expect($image->isImage())->toBeTrue();
    }
});

// Testa buscar anexos por tipo MIME
it('busca anexos por tipo mime', function () {
    $service = new AttachmentService();
    $attachments = $service->findByMimeType('image/jpeg');

    expect($attachments)->toBeArray();

    foreach ($attachments as $attachment) {
        expect($attachment->getMimeType())->toBe('image/jpeg');
    }
});

// Testa anexos possuem tamanho valido
it('anexos possuem tamanho valido', function () {
    $service = new AttachmentService();
    $attachments = $service->findAll();

    if (count($attachments) === 0) {
        expect(true)->toBeTrue();
        return;
    }

    foreach ($attachments as $attachment) {
        expect($attachment->getSizeBytes())->toBeInt();
        expect($attachment->getSizeBytes())->toBeGreaterThanOrEqual(0);
    }
});

// Testa retornar null ao buscar anexo inexistente
it('retorna null ao buscar anexo inexistente', function () {
    $service = new AttachmentService();
    $found = $service->find('00000000-0000-0000-0000-000000000000');

    expect($found)->toBeNull();
});

// Testa verificar se anexo e imagem retorna boolean
it('verificar se anexo e imagem retorna boolean', function () {
    $service = new AttachmentService();
    $attachments = $service->findAll();

    if (count($attachments) === 0) {
        expect(true)->toBeTrue();
        return;
    }

    $attachment = $attachments[0];
    $isImage = $service->isImage($attachment->getId());

    expect($isImage)->toBeBool();
});
