<?php

/**
 * Testes de integração para o UserService
 * Testa as operações de CRUD do serviço
 */

use App\Service\UserService;
use App\Entity\User;

// Testa buscar todos os usuarios
it('busca todos os usuarios', function () {
    $service = new UserService();
    $users = $service->findAll();

    expect($users)->toBeArray();
});

// Testa buscar usuario por criterios
it('busca usuarios por criterios', function () {
    $service = new UserService();
    $users = $service->findBy(['timezone' => 'America/Sao_Paulo']);

    expect($users)->toBeArray();
});

// Testa retornar null ao buscar usuario inexistente
it('retorna null ao buscar usuario inexistente', function () {
    $service = new UserService();
    $found = $service->find(999999);

    expect($found)->toBeNull();
});
