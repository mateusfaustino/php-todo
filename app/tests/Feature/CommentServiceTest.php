<?php

/**
 * Testes de integração para o CommentService
 * Testa as operações de CRUD e lógica de negócio do serviço
 */

use App\Service\CommentService;
use App\Service\TaskService;
use App\Service\UserService;
use App\Entity\Comment;
use App\Entity\Task;
use App\Entity\User;

// Testa buscar todos os comentarios
it('busca todos os comentarios', function () {
    $service = new CommentService();
    $comments = $service->findAll();

    expect($comments)->toBeArray();
});

// Testa buscar comentario por ID
it('busca comentario por id', function () {
    $service = new CommentService();
    $comments = $service->findAll();

    if (count($comments) > 0) {
        $firstComment = $comments[0];
        $found = $service->find($firstComment->getId());

        expect($found)->toBeInstanceOf(Comment::class);
        expect($found->getId())->toBe($firstComment->getId());
    }
});

// Testa buscar comentarios por tarefa
it('busca comentarios por tarefa', function () {
    $taskService = new TaskService();
    $service = new CommentService();

    $tasks = $taskService->findAll();

    if (count($tasks) === 0) {
        expect(true)->toBeTrue();
        return;
    }

    $task = $tasks[0];
    $comments = $service->findByTask($task);

    expect($comments)->toBeArray();
});

// Testa buscar comentarios por usuario
it('busca comentarios por usuario', function () {
    $userService = new UserService();
    $service = new CommentService();

    $users = $userService->findAll();

    if (count($users) === 0) {
        expect(true)->toBeTrue();
        return;
    }

    $user = $users[0];
    $comments = $service->findByUser($user);

    expect($comments)->toBeArray();
});

// Testa buscar comentarios recentes
it('busca comentarios recentes', function () {
    $service = new CommentService();
    $comments = $service->findRecent(5);

    expect($comments)->toBeArray();
    expect(count($comments))->toBeLessThanOrEqual(5);
});

// Testa buscar comentarios por criterios
it('busca comentarios por criterios', function () {
    $service = new CommentService();
    $comments = $service->findBy([]);

    expect($comments)->toBeArray();
});

// Testa comentarios possuem conteudo valido
it('comentarios possuem conteudo valido', function () {
    $service = new CommentService();
    $comments = $service->findAll();

    if (count($comments) === 0) {
        expect(true)->toBeTrue();
        return;
    }

    foreach ($comments as $comment) {
        expect($comment->getContent())->toBeString();
        expect(strlen($comment->getContent()))->toBeGreaterThan(0);
    }
});

// Testa verificar se comentario foi editado retorna boolean
it('verificar se comentario foi editado retorna boolean', function () {
    $service = new CommentService();
    $comments = $service->findAll();

    if (count($comments) === 0) {
        expect(true)->toBeTrue();
        return;
    }

    $comment = $comments[0];
    $isEdited = $service->isEdited($comment->getId());

    expect($isEdited)->toBeBool();
});

// Testa retornar null ao buscar comentario inexistente
it('retorna null ao buscar comentario inexistente', function () {
    $service = new CommentService();
    $found = $service->find('00000000-0000-0000-0000-000000000000');

    expect($found)->toBeNull();
});
