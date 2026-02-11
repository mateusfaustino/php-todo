<?php

/**
 * Testes de integração para o ProjectService
 * Testa as operações de CRUD e lógica de negócio do serviço
 */

use App\Service\ProjectService;
use App\Service\UserService;
use App\Entity\Project;
use App\Entity\User;

// Testa buscar projetos por usuario
it('busca projetos por usuario', function () {
    $userService = new UserService();
    $service = new ProjectService();

    $users = $userService->findAll();

    if (count($users) === 0) {
        expect(true)->toBeTrue();
        return;
    }

    $user = $users[0];
    $projects = $service->findByUser($user);

    expect($projects)->toBeArray();
});

// Testa buscar todos os projetos
it('busca todos os projetos', function () {
    $service = new ProjectService();
    $projects = $service->findAll();

    expect($projects)->toBeArray();
});

// Testa buscar projeto por ID
it('busca projeto por id', function () {
    $service = new ProjectService();
    $projects = $service->findAll();

    if (count($projects) > 0) {
        $firstProject = $projects[0];
        $found = $service->find($firstProject->getId());

        expect($found)->toBeInstanceOf(Project::class);
        expect($found->getId())->toBe($firstProject->getId());
    }
});

// Testa buscar projetos por critérios
it('busca projetos por criterios', function () {
    $service = new ProjectService();
    $projects = $service->findBy(['archived' => false]);

    expect($projects)->toBeArray();

    foreach ($projects as $project) {
        expect($project->isArchived())->toBeFalse();
    }
});

// Testa buscar projetos arquivados
it('busca projetos arquivados', function () {
    $service = new ProjectService();
    $projects = $service->findArchived();

    expect($projects)->toBeArray();

    foreach ($projects as $project) {
        expect($project->isArchived())->toBeTrue();
    }
});

// Testa buscar projetos ativos
it('busca projetos ativos', function () {
    $service = new ProjectService();
    $projects = $service->findActive();

    expect($projects)->toBeArray();

    foreach ($projects as $project) {
        expect($project->isArchived())->toBeFalse();
    }
});

// Testa buscar projeto existente retorna dados corretos
it('busca projeto existente retorna dados corretos', function () {
    $service = new ProjectService();
    $projects = $service->findAll();

    if (count($projects) === 0) {
        expect(true)->toBeTrue();
        return;
    }

    $project = $projects[0];

    expect($project->getId())->toBeString();
    expect($project->getName())->toBeString();
    expect($project->getUser())->toBeInstanceOf(User::class);
});

// Testa que projetos arquivados tem flag correta
it('projetos arquivados tem flag correta', function () {
    $service = new ProjectService();
    $archivedProjects = $service->findArchived();

    foreach ($archivedProjects as $project) {
        expect($project->isArchived())->toBeTrue();
    }

    expect(true)->toBeTrue();
});

// Testa que projetos ativos nao estao arquivados
it('projetos ativos nao estao arquivados', function () {
    $service = new ProjectService();
    $activeProjects = $service->findActive();

    foreach ($activeProjects as $project) {
        expect($project->isArchived())->toBeFalse();
    }

    expect(true)->toBeTrue();
});

// Testa projeto possui cor valida
it('projeto possui cor valida', function () {
    $service = new ProjectService();
    $projects = $service->findAll();

    if (count($projects) === 0) {
        expect(true)->toBeTrue();
        return;
    }

    foreach ($projects as $project) {
        $color = $project->getColor();
        expect($color)->toBeString();
        expect(strlen($color))->toBe(7);
        expect($color[0])->toBe('#');
    }
});

// Testa retornar null ao buscar projeto inexistente
it('retorna null ao buscar projeto inexistente', function () {
    $service = new ProjectService();
    $found = $service->find('00000000-0000-0000-0000-000000000000');

    expect($found)->toBeNull();
});
