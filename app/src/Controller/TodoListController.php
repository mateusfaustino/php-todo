<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\TodoListService;
use App\Service\ProjectService;
use App\Service\UserService;
use App\Entity\TodoList;

/**
 * Controller for managing TodoList entities
 * Handles CRUD operations and business logic for todo lists
 */
final class TodoListController extends AbstractController
{
    private TodoListService $todoListService;
    private ProjectService $projectService;
    private UserService $userService;

    public function __construct()
    {
        $this->todoListService = new TodoListService();
        $this->projectService = new ProjectService();
        $this->userService = new UserService();
    }

    /**
     * Display list of all todo lists
     */
    public function index(): void
    {
        $lists = $this->todoListService->findAll();
        $activeLists = $this->todoListService->findActive();
        $archivedLists = $this->todoListService->findArchived();

        $this->render('todolist/list', [
            'lists' => $lists,
            'activeLists' => $activeLists,
            'archivedLists' => $archivedLists,
            'totalCount' => count($lists),
            'activeCount' => count($activeLists),
            'archivedCount' => count($archivedLists),
        ]);
    }

    /**
     * Display form to create a new todo list
     */
    public function create(): void
    {
        $projects = $this->projectService->findAll();
        $users = $this->userService->findAll();

        $this->render('todolist/create', [
            'projects' => $projects,
            'users' => $users,
        ]);
    }

    /**
     * Store a newly created todo list
     */
    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectToURL('/lists');
            return;
        }

        $name = $_POST['name'] ?? '';
        $userId = $_POST['user_id'] ?? '';
        $projectId = $_POST['project_id'] ?? '';
        $order = (int) ($_POST['order'] ?? 0);

        if (empty($name) || empty($userId)) {
            $this->render('todolist/create', [
                'error' => 'Name and user are required',
                'projects' => $this->projectService->findAll(),
                'users' => $this->userService->findAll(),
            ]);
            return;
        }

        $user = $this->userService->find((int) $userId);
        if (!$user) {
            $this->render('todolist/create', [
                'error' => 'User not found',
                'projects' => $this->projectService->findAll(),
                'users' => $this->userService->findAll(),
            ]);
            return;
        }

        $list = new TodoList(
            user: $user,
            name: $name,
            order: $order
        );

        if (!empty($projectId)) {
            $project = $this->projectService->find($projectId);
            if ($project) {
                $list->setProject($project);
            }
        }

        $this->todoListService->create($list);

        $this->redirectToURL('/lists');
    }

    /**
     * Display a specific todo list with its tasks
     */
    public function show(): void
    {
        $id = $_GET['id'] ?? '';

        if (empty($id)) {
            $this->redirectToURL('/lists');
            return;
        }

        $list = $this->todoListService->find($id);

        if (!$list) {
            $this->render('error/not-found', [
                'message' => 'List not found',
            ], false);
            return;
        }

        $this->render('todolist/show', [
            'list' => $list,
            'tasks' => $list->getTasks(),
        ]);
    }

    /**
     * Display form to edit a todo list
     */
    public function edit(): void
    {
        $id = $_GET['id'] ?? '';

        if (empty($id)) {
            $this->redirectToURL('/lists');
            return;
        }

        $list = $this->todoListService->find($id);

        if (!$list) {
            $this->render('error/not-found', [
                'message' => 'List not found',
            ], false);
            return;
        }

        $projects = $this->projectService->findAll();

        $this->render('todolist/edit', [
            'list' => $list,
            'projects' => $projects,
        ]);
    }

    /**
     * Update the specified todo list
     */
    public function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectToURL('/lists');
            return;
        }

        $id = $_POST['id'] ?? '';
        $name = $_POST['name'] ?? '';
        $projectId = $_POST['project_id'] ?? '';
        $order = (int) ($_POST['order'] ?? 0);

        if (empty($id) || empty($name)) {
            $this->redirectToURL('/lists');
            return;
        }

        $list = $this->todoListService->find($id);

        if (!$list) {
            $this->render('error/not-found', [
                'message' => 'List not found',
            ], false);
            return;
        }

        $list->setName($name);
        $list->setOrder($order);

        if (!empty($projectId)) {
            $project = $this->projectService->find($projectId);
            if ($project) {
                $list->setProject($project);
            }
        }

        $this->todoListService->update($list);

        $this->redirectToURL('/lists');
    }

    /**
     * Archive a todo list
     */
    public function archive(): void
    {
        $id = $_GET['id'] ?? '';

        if (empty($id)) {
            $this->redirectToURL('/lists');
            return;
        }

        $this->todoListService->archive($id);

        $this->redirectToURL('/lists');
    }

    /**
     * Unarchive a todo list
     */
    public function unarchive(): void
    {
        $id = $_GET['id'] ?? '';

        if (empty($id)) {
            $this->redirectToURL('/lists');
            return;
        }

        $this->todoListService->unarchive($id);

        $this->redirectToURL('/lists');
    }

    /**
     * Delete a todo list
     */
    public function delete(): void
    {
        $id = $_GET['id'] ?? '';

        if (empty($id)) {
            $this->redirectToURL('/lists');
            return;
        }

        $this->todoListService->remove($id);

        $this->redirectToURL('/lists');
    }
}
