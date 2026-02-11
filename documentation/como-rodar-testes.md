# Como Rodar os Testes do Sistema TODO

Este documento explica como executar os testes unitários do sistema TODO usando a biblioteca Pest PHP dentro do container Docker.

## Pré-requisitos

- Docker e Docker Compose instalados
- Containers do projeto em execução

## Iniciar os Containers

Antes de rodar os testes, certifique-se de que os containers estão rodando:

```bash
docker compose up -d
```

## Instalação das Dependências

Se for a primeira vez executando, instale as dependências dentro do container:

```bash
docker compose exec php bash -c "cd /var/www/ && composer install"
```

## Estrutura dos Testes

Os testes estão organizados da seguinte forma:

```
app/tests/
├── Pest.php              # Configuração principal do Pest
├── TestCase.php          # Classe base para testes
├── Unit/                 # Testes unitários
│   ├── UserTest.php      # Testes da entidade User
│   ├── ProjectTest.php   # Testes da entidade Project
│   ├── TodoListTest.php  # Testes da entidade TodoList
│   ├── TaskTest.php      # Testes da entidade Task
│   ├── SubtaskTest.php   # Testes da entidade Subtask
│   ├── TagTest.php       # Testes da entidade Tag
│   ├── CommentTest.php   # Testes da entidade Comment
│   ├── AttachmentTest.php # Testes da entidade Attachment
│   ├── ReminderTest.php  # Testes da entidade Reminder
│   └── NotificationTest.php # Testes da entidade Notification
└── Feature/              # Testes de integração (quando houver)
```

## Comandos para Executar os Testes

Todos os comandos devem ser executados dentro do container PHP via Docker.

### Rodar todos os testes

```bash
docker compose exec php bash -c "cd /var/www/ && ./vendor/bin/pest"
```

### Rodar apenas testes unitários

```bash
docker compose exec php bash -c "cd /var/www/ && ./vendor/bin/pest tests/Unit"
```

### Rodar testes de uma entidade específica

```bash
docker compose exec php bash -c "cd /var/www/ && ./vendor/bin/pest tests/Unit/UserTest.php"
```

### Rodar um teste específico pelo nome

```bash
docker compose exec php bash -c "cd /var/www/ && ./vendor/bin/pest --filter='cria um usuário com sucesso'"
```

### Rodar testes com cobertura de código

```bash
docker compose exec php bash -c "cd /var/www/ && ./vendor/bin/pest --coverage"
```

### Rodar testes em modo verbose (mais detalhes)

```bash
docker compose exec php bash -c "cd /var/www/ && ./vendor/bin/pest --verbose"
```

### Rodar testes paralelamente (mais rápido)

```bash
docker compose exec php bash -c "cd /var/www/ && ./vendor/bin/pest --parallel"
```

### Rodar testes com relatório de tempo

```bash
docker compose exec php bash -c "cd /var/www/ && ./vendor/bin/pest --profile"
```

## Acessando o Container Interativamente

Se preferir, você pode acessar o container e executar os comandos diretamente:

```bash
# Acessar o container PHP
docker compose exec php bash

# Dentro do container
cd /var/www/
./vendor/bin/pest
```

Para sair do container, digite `exit`.

## Entidades Testadas

Cada entidade possui testes cobrindo:

### User
- Criação de usuário
- Geração automática de UUID
- Getters e setters
- Método getShortName
- Inicialização de coleções

### Project
- Criação de projeto
- Métodos de negócio (archive, unarchive, rename)
- Validações
- Relacionamentos

### TodoList
- Criação de lista
- Métodos de negócio (archive, unarchive, reorder)
- Validações
- Relacionamentos

### Task
- Criação de tarefa
- Métodos de negócio (completeTask, rescheduleTask)
- Verificações de status (isOverdue, isCompleted)
- Cálculo de porcentagem de conclusão
- Relacionamentos

### Subtask
- Criação de subtarefa
- Métodos de negócio (completeSubtask, uncompleteSubtask, reorderSubtask)
- Status de conclusão

### Tag
- Criação de tag
- Gerenciamento de tarefas associadas
- Getters e setters

### Comment
- Criação de comentário
- Método editComment
- Validações
- Datas de criação e edição

### Attachment
- Criação de anexo
- Métodos auxiliares (getFormattedSize, isImage, getFileExtension)
- Formatação de tamanho de arquivo

### Reminder
- Criação de lembrete
- Métodos de negócio (schedule, markAsSent)
- Verificações de status (isDue, isPending)
- Canais de envio

### Notification
- Criação de notificação
- Métodos de negócio (markAsRead, markAsUnread)
- Cálculo de tempo decorrido
- Tipos de notificação

## Convenções dos Testes

1. **Nome dos testes**: Descritivos em português, explicando o comportamento testado
2. **Comentários**: Cada teste possui comentário explicativo em português
3. **Helpers**: Funções auxiliares para criar dependências (usuários, listas, etc.)
4. **Expectativas**: Uso da API `expect()` do Pest para asserções legíveis

## Boas Práticas

- Cada teste deve ser independente
- Use helpers para evitar repetição de código
- Teste tanto o caminho feliz quanto os casos de erro
- Mantenha os testes rápidos e focados
- Documente o comportamento esperado nos comentários

## Solução de Problemas

### Erro: "Class not found"
Certifique-se de que o autoload está atualizado dentro do container:
```bash
docker compose exec php bash -c "cd /var/www/ && composer dump-autoload"
```

### Erro: "Permission denied"
Dê permissão de execução dentro do container:
```bash
docker compose exec php bash -c "chmod +x /var/www//vendor/bin/pest"
```

### Testes falhando após alterações
Limpe o cache de testes:
```bash
docker compose exec php bash -c "cd /var/www/ && ./vendor/bin/pest --cache-clear"
```

### Container não está rodando
Verifique se os containers estão em execução:
```bash
docker compose ps
```

Se não estiverem rodando, inicie-os:
```bash
docker compose up -d
```

## Recursos Adicionais

- [Documentação oficial do Pest](https://pestphp.com/)
- [Guia de asserções do Pest](https://pestphp.com/docs/expectations)
- [PHPUnit - Base do Pest](https://phpunit.de/documentation.)
