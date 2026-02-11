<?php

/**
 * Testes unitários para a entidade Comment
 * Cobertura: criação, getters, setters e métodos de negócio
 */

use App\Entity\Comment;
use App\Entity\Task;
use App\Entity\TodoList;
use App\Entity\User;
use App\Enum\TaskPriorityEnum;

// Helper para criar dependências
function createCommentDependencies(): array
{
    $user = new User(
        name: 'Test User',
        email: 'test@email.com',
        passwordHash: password_hash('senha123', PASSWORD_BCRYPT)
    );

    $list = new TodoList(
        user: $user,
        name: 'A Fazer',
        order: 1
    );

    $task = new Task(
        list: $list,
        title: 'Tarefa Teste',
        priority: TaskPriorityEnum::MEDIUM
    );

    return ['user' => $user, 'list' => $list, 'task' => $task];
}

// Testa a criação de um comentário com dados válidos
it('cria um comentário com sucesso', function () {
    $deps = createCommentDependencies();
    
    $comment = new Comment(
        task: $deps['task'],
        user: $deps['user'],
        content: 'Este é um comentário de teste'
    );

    expect($comment)->toBeInstanceOf(Comment::class);
    expect($comment->getContent())->toBe('Este é um comentário de teste');
    expect($comment->getTask())->toBe($deps['task']);
    expect($comment->getUser())->toBe($deps['user']);
});

// Testa que o UUID é gerado automaticamente
it('gera UUID automaticamente ao criar comentário', function () {
    $deps = createCommentDependencies();
    
    $comment = new Comment(
        task: $deps['task'],
        user: $deps['user'],
        content: 'Comentário teste'
    );

    expect($comment->getId())->toBeString();
    expect(strlen($comment->getId()))->toBe(36);
});

// Testa que o comentário inicia sem data de edição
it('inicia sem data de edição', function () {
    $deps = createCommentDependencies();
    
    $comment = new Comment(
        task: $deps['task'],
        user: $deps['user'],
        content: 'Comentário teste'
    );

    expect($comment->getEditedAt())->toBeNull();
});

// Testa o método editComment
it('edita um comentário corretamente', function () {
    $deps = createCommentDependencies();
    
    $comment = new Comment(
        task: $deps['task'],
        user: $deps['user'],
        content: 'Conteúdo original'
    );

    $comment->editComment('Novo conteúdo editado');

    expect($comment->getContent())->toBe('Novo conteúdo editado');
    expect($comment->getEditedAt())->toBeInstanceOf(DateTime::class);
});

// Testa que editComment lança exceção com conteúdo vazio
it('lança exceção ao editar com conteúdo vazio', function () {
    $deps = createCommentDependencies();
    
    $comment = new Comment(
        task: $deps['task'],
        user: $deps['user'],
        content: 'Conteúdo original'
    );

    expect(fn() => $comment->editComment(''))
        ->toThrow(\InvalidArgumentException::class, 'Comment content cannot be empty');
});

// Testa o setter de conteúdo
it('atualiza o conteúdo do comentário', function () {
    $deps = createCommentDependencies();
    
    $comment = new Comment(
        task: $deps['task'],
        user: $deps['user'],
        content: 'Conteúdo original'
    );

    $comment->setContent('Conteúdo atualizado');

    expect($comment->getContent())->toBe('Conteúdo atualizado');
});

// Testa que a data de criação é definida automaticamente
it('define data de criação automaticamente', function () {
    $deps = createCommentDependencies();
    
    $comment = new Comment(
        task: $deps['task'],
        user: $deps['user'],
        content: 'Comentário teste'
    );

    expect($comment->getCreatedAt())->toBeInstanceOf(DateTime::class);
});
