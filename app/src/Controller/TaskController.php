<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\TaskService;
use App\Service\TodoListService;
use App\Service\TagService;
use App\Entity\Task;
use App\Enum\TaskPriorityEnum;
use App\Enum\TaskStatusEnum;
use DateTime;

/**
 * Controller for managing Task entities
 * Handles CRUD operations and business logic for tasks
 */
final class TaskController extends AbstractController
{
    private TaskService $taskService;
    private TodoListService $todoListService;
    private TagService $tagService;

    public function __construct()
    {
        $this->taskService = new TaskService();
        $this->todoListService = new TodoListService();
        $this->tagService = new TagService();
    }

    /**
     * Display list of all tasks
     */
    public function index(): void
    {
        $tasks = $this->taskService->findAll();
        $stats = $this->taskService->getStatistics();

        $this->render('task/list', [
            'tasks' => $tasks,
            'stats' => $stats,
        ]);
    }

    /**
     * Display tasks by status
     */
    public function byStatus(): void
    {
        $status = $_GET['status'] ?? 'TODO';
        $taskStatus = TaskStatusEnum::from($status);
        $tasks = $this->taskService->findByStatus($taskStatus);

        $this->render('task/list', [
            'tasks' => $tasks,
            'filter' => 'Status: ' . $status,
        ]);
    }

    /**
     * Display overdue tasks
     */
    public function overdue(): void
    {
        $tasks = $this->taskService->findOverdue();

        $this->render('task/list', [
            'tasks' => $tasks,
            'filter' => 'Overdue',
        ]);
    }

    /**
     * Display form to create a new task
     */
    public function create(): void
    {
        $lists = $this->todoListService->findAll();
        $tags = $this->tagService->findAll();

        $this->render('task/create', [
            'lists' => $lists,
            'tags' => $tags,
            'priorities' => TaskPriorityEnum::cases(),
        ]);
    }

    /**
     * Store a newly created task
     */
    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectToURL('/tasks');
            return;
        }

        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $listId = $_POST['list_id'] ?? '';
        $priority = $_POST['priority'] ?? 'MEDIUM';
        $dueDate = $_POST['due_date'] ?? '';

        if (empty($title) || empty($listId)) {
            $this->render('task/create', [
                'error' => 'Title and list are required',
                'lists' => $this->todoListService->findAll(),
                'tags' => $this->tagService->findAll(),
                'priorities' => TaskPriorityEnum::cases(),
            ]);
            return;
        }

        $list = $this->todoListService->find($listId);
        if (!$list) {
            $this->render('task/create', [
                'error' => 'List not found',
                'lists' => $this->todoListService->findAll(),
                'tags' => $this->tagService->findAll(),
                'priorities' => TaskPriorityEnum::cases(),
            ]);
            return;
        }

        $taskPriority = TaskPriorityEnum::from($priority);

        $task = new Task(
            list: $list,
            title: $title,
            priority: $taskPriority,
            order: 0
        );

        if (!empty($description)) {
            $task->setDescription($description);
        }

        if (!empty($dueDate)) {
            $task->setDueDate(new DateTime($dueDate));
        }

        $this->taskService->create($task);

        $this->redirectToURL('/tasks');
    }

    /**
     * Display a specific task
     */
    public function show(): void
    {
        $id = $_GET['id'] ?? '';

        if (empty($id)) {
            $this->redirectToURL('/tasks');
            return;
        }

        $task = $this->taskService->find($id);

        if (!$task) {
            $this->render('error/not-found', [
                'message' => 'Task not found',
            ], false);
            return;
        }

        $this->render('task/show', [
            'task' => $task,
        ]);
    }

    /**
     * Display form to edit a task
     */
    public function edit(): void
    {
        $id = $_GET['id'] ?? '';

        if (empty($id)) {
            $this->redirectToURL('/tasks');
            return;
        }

        $task = $this->taskService->find($id);

        if (!$task) {
            $this->render('error/not-found', [
                'message' => 'Task not found',
            ], false);
            return;
        }

        $lists = $this->todoListService->findAll();
        $tags = $this->tagService->findAll();

        $this->render('task/edit', [
            'task' => $task,
            'lists' => $lists,
            'tags' => $tags,
            'priorities' => TaskPriorityEnum::cases(),
            'statuses' => TaskStatusEnum::cases(),
        ]);
    }

    /**
     * Update the specified task
     */
    public function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectToURL('/tasks');
            return;
        }

        $id = $_POST['id'] ?? '';
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $priority = $_POST['priority'] ?? 'MEDIUM';
        $status = $_POST['status'] ?? 'TODO';
        $dueDate = $_POST['due_date'] ?? '';

        if (empty($id) || empty($title)) {
            $this->redirectToURL('/tasks');
            return;
        }

        $task = $this->taskService->find($id);

        if (!$task) {
            $this->render('error/not-found', [
                'message' => 'Task not found',
            ], false);
            return;
        }

        $task->setTitle($title);
        $task->setDescription($description);
        $task->setPriority(TaskPriorityEnum::from($priority));
        $task->setStatus(TaskStatusEnum::from($status));

        if (!empty($dueDate)) {
            $task->setDueDate(new DateTime($dueDate));
        }

        $this->taskService->update($task);

        $this->redirectToURL('/tasks');
    }

    /**
     * Complete a task
     */
    public function complete(): void
    {
        $id = $_GET['id'] ?? '';

        if (empty($id)) {
            $this->redirectToURL('/tasks');
            return;
        }

        $this->taskService->complete($id);

        $this->redirectToURL('/tasks');
    }

    /**
     * Delete a task
     */
    public function delete(): void
    {
        $id = $_GET['id'] ?? '';

        if (empty($id)) {
            $this->redirectToURL('/tasks');
            return;
        }

        $this->taskService->remove($id);

        $this->redirectToURL('/tasks');
    }
}
