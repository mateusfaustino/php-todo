<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\ProjectService;
use App\Service\TaskService;
use App\Service\TodoListService;
use App\Service\TagService;
use App\Enum\TaskStatusEnum;

/**
 * Controller for the homepage/dashboard
 * Displays overview statistics and recent activity
 */
final class HomepageController extends AbstractController
{
    private ProjectService $projectService;
    private TaskService $taskService;
    private TodoListService $todoListService;
    private TagService $tagService;

    public function __construct()
    {
        $this->projectService = new ProjectService();
        $this->taskService = new TaskService();
        $this->todoListService = new TodoListService();
        $this->tagService = new TagService();
    }

    public function index(): void
    {
        // Get statistics
        $projects = $this->projectService->findAll();
        $tasks = $this->taskService->findAll();
        $taskStats = $this->taskService->getStatistics();
        $overdueTasks = $this->taskService->findOverdue();
        $lists = $this->todoListService->findAll();
        $tags = $this->tagService->findAll();

        // Get recent tasks (last 5)
        $recentTasks = array_slice($tasks, 0, 5);

        // Get tasks by status
        $todoTasks = $this->taskService->findByStatus(TaskStatusEnum::TODO);
        $inProgressTasks = $this->taskService->findByStatus(TaskStatusEnum::IN_PROGRESS);

        $this->render('homepage/index', [
            'stats' => [
                'projects' => count($projects),
                'tasks' => count($tasks),
                'completed' => $taskStats['completed'] ?? 0,
                'todo' => $taskStats['todo'] ?? 0,
                'inProgress' => $taskStats['in_progress'] ?? 0,
                'overdue' => count($overdueTasks),
                'lists' => count($lists),
                'tags' => count($tags),
            ],
            'recentTasks' => $recentTasks,
            'overdueTasks' => array_slice($overdueTasks, 0, 5),
            'todoTasks' => array_slice($todoTasks, 0, 5),
            'inProgressTasks' => array_slice($inProgressTasks, 0, 5),
        ]);
    }
}
