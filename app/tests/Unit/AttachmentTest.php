<?php

/**
 * Testes unitários para a entidade Attachment
 * Cobertura: criação, getters, setters e métodos auxiliares
 */

use App\Entity\Attachment;
use App\Entity\Task;
use App\Entity\TodoList;
use App\Entity\User;
use App\Enum\TaskPriorityEnum;

// Helper para criar dependências
function createAttachmentDependencies(): array
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

// Testa a criação de um anexo com dados válidos
it('cria um anexo com sucesso', function () {
    $deps = createAttachmentDependencies();
    
    $attachment = new Attachment(
        task: $deps['task'],
        user: $deps['user'],
        fileName: 'documento.pdf',
        mimeType: 'application/pdf',
        sizeBytes: 1024000,
        storageUrl: '/storage/attachments/documento.pdf'
    );

    expect($attachment)->toBeInstanceOf(Attachment::class);
    expect($attachment->getFileName())->toBe('documento.pdf');
    expect($attachment->getMimeType())->toBe('application/pdf');
    expect($attachment->getSizeBytes())->toBe(1024000);
});

// Testa que o UUID é gerado automaticamente
it('gera UUID automaticamente ao criar anexo', function () {
    $deps = createAttachmentDependencies();
    
    $attachment = new Attachment(
        task: $deps['task'],
        user: $deps['user'],
        fileName: 'arquivo.txt',
        mimeType: 'text/plain',
        sizeBytes: 1024,
        storageUrl: '/storage/arquivo.txt'
    );

    expect($attachment->getId())->toBeString();
    expect(strlen($attachment->getId()))->toBe(36);
});

// Testa o método getFormattedSize com bytes
it('formata tamanho em bytes corretamente', function () {
    $deps = createAttachmentDependencies();
    
    $attachment = new Attachment(
        task: $deps['task'],
        user: $deps['user'],
        fileName: 'arquivo.txt',
        mimeType: 'text/plain',
        sizeBytes: 500,
        storageUrl: '/storage/arquivo.txt'
    );

    expect($attachment->getFormattedSize())->toBe('500 B');
});

// Testa o método getFormattedSize com kilobytes
it('formata tamanho em kilobytes corretamente', function () {
    $deps = createAttachmentDependencies();
    
    $attachment = new Attachment(
        task: $deps['task'],
        user: $deps['user'],
        fileName: 'arquivo.txt',
        mimeType: 'text/plain',
        sizeBytes: 1536,
        storageUrl: '/storage/arquivo.txt'
    );

    expect($attachment->getFormattedSize())->toBe('1.5 KB');
});

// Testa o método getFormattedSize com megabytes
it('formata tamanho em megabytes corretamente', function () {
    $deps = createAttachmentDependencies();
    
    $attachment = new Attachment(
        task: $deps['task'],
        user: $deps['user'],
        fileName: 'arquivo.txt',
        mimeType: 'text/plain',
        sizeBytes: 2097152,
        storageUrl: '/storage/arquivo.txt'
    );

    expect($attachment->getFormattedSize())->toBe('2 MB');
});

// Testa o método isImage para imagem
it('identifica arquivo como imagem', function () {
    $deps = createAttachmentDependencies();
    
    $attachment = new Attachment(
        task: $deps['task'],
        user: $deps['user'],
        fileName: 'foto.jpg',
        mimeType: 'image/jpeg',
        sizeBytes: 1024,
        storageUrl: '/storage/foto.jpg'
    );

    expect($attachment->isImage())->toBeTrue();
});

// Testa o método isImage para não-imagem
it('identifica arquivo como não-imagem', function () {
    $deps = createAttachmentDependencies();
    
    $attachment = new Attachment(
        task: $deps['task'],
        user: $deps['user'],
        fileName: 'documento.pdf',
        mimeType: 'application/pdf',
        sizeBytes: 1024,
        storageUrl: '/storage/documento.pdf'
    );

    expect($attachment->isImage())->toBeFalse();
});

// Testa o método getFileExtension
it('retorna extensão do arquivo corretamente', function () {
    $deps = createAttachmentDependencies();
    
    $attachment = new Attachment(
        task: $deps['task'],
        user: $deps['user'],
        fileName: 'documento.pdf',
        mimeType: 'application/pdf',
        sizeBytes: 1024,
        storageUrl: '/storage/documento.pdf'
    );

    expect($attachment->getFileExtension())->toBe('pdf');
});

// Testa que a data de criação é definida automaticamente
it('define data de criação automaticamente', function () {
    $deps = createAttachmentDependencies();
    
    $attachment = new Attachment(
        task: $deps['task'],
        user: $deps['user'],
        fileName: 'arquivo.txt',
        mimeType: 'text/plain',
        sizeBytes: 1024,
        storageUrl: '/storage/arquivo.txt'
    );

    expect($attachment->getCreatedAt())->toBeInstanceOf(DateTime::class);
});
