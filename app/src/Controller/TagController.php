<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\TagService;
use App\Service\UserService;
use App\Service\TaskService;
use App\Entity\Tag;

/**
 * Controller for managing Tag entities
 * Handles CRUD operations and business logic for tags
 */
final class TagController extends AbstractController
{
    private TagService $tagService;
    private UserService $userService;
    private TaskService $taskService;

    public function __construct()
    {
        $this->tagService = new TagService();
        $this->userService = new UserService();
        $this->taskService = new TaskService();
    }

    /**
     * Display list of all tags
     */
    public function index(): void
    {
        $tags = $this->tagService->findAll();

        $this->render('tag/list', [
            'tags' => $tags,
            'totalCount' => count($tags),
        ]);
    }

    /**
     * Display form to create a new tag
     */
    public function create(): void
    {
        $users = $this->userService->findAll();

        $this->render('tag/create', [
            'users' => $users,
        ]);
    }

    /**
     * Store a newly created tag
     */
    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectToURL('/tags');
            return;
        }

        $name = $_POST['name'] ?? '';
        $color = $_POST['color'] ?? '#3498db';
        $userId = $_POST['user_id'] ?? '';

        if (empty($name) || empty($userId)) {
            $this->render('tag/create', [
                'error' => 'Name and user are required',
                'users' => $this->userService->findAll(),
            ]);
            return;
        }

        $user = $this->userService->find((int) $userId);
        if (!$user) {
            $this->render('tag/create', [
                'error' => 'User not found',
                'users' => $this->userService->findAll(),
            ]);
            return;
        }

        $tag = new Tag(
            user: $user,
            name: $name,
            color: $color
        );

        $this->tagService->create($tag);

        $this->redirectToURL('/tags');
    }

    /**
     * Display a specific tag with its tasks
     */
    public function show(): void
    {
        $id = $_GET['id'] ?? '';

        if (empty($id)) {
            $this->redirectToURL('/tags');
            return;
        }

        $tag = $this->tagService->find($id);

        if (!$tag) {
            $this->render('error/not-found', [
                'message' => 'Tag not found',
            ], false);
            return;
        }

        $this->render('tag/show', [
            'tag' => $tag,
            'tasks' => $tag->getTasks(),
            'usageCount' => $this->tagService->getUsageCount($tag),
        ]);
    }

    /**
     * Display form to edit a tag
     */
    public function edit(): void
    {
        $id = $_GET['id'] ?? '';

        if (empty($id)) {
            $this->redirectToURL('/tags');
            return;
        }

        $tag = $this->tagService->find($id);

        if (!$tag) {
            $this->render('error/not-found', [
                'message' => 'Tag not found',
            ], false);
            return;
        }

        $this->render('tag/edit', [
            'tag' => $tag,
        ]);
    }

    /**
     * Update the specified tag
     */
    public function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectToURL('/tags');
            return;
        }

        $id = $_POST['id'] ?? '';
        $name = $_POST['name'] ?? '';
        $color = $_POST['color'] ?? '#3498db';

        if (empty($id) || empty($name)) {
            $this->redirectToURL('/tags');
            return;
        }

        $tag = $this->tagService->find($id);

        if (!$tag) {
            $this->render('error/not-found', [
                'message' => 'Tag not found',
            ], false);
            return;
        }

        $tag->setName($name);
        $tag->setColor($color);

        $this->tagService->update($tag);

        $this->redirectToURL('/tags');
    }

    /**
     * Assign a tag to a task
     */
    public function assignToTask(): void
    {
        $tagId = $_GET['tag_id'] ?? '';
        $taskId = $_GET['task_id'] ?? '';

        if (empty($tagId) || empty($taskId)) {
            $this->redirectToURL('/tags');
            return;
        }

        $task = $this->taskService->find($taskId);
        if ($task) {
            $this->tagService->assignToTask($tagId, $task);
        }

        $this->redirectToURL('/tags');
    }

    /**
     * Remove a tag from a task
     */
    public function removeFromTask(): void
    {
        $tagId = $_GET['tag_id'] ?? '';
        $taskId = $_GET['task_id'] ?? '';

        if (empty($tagId) || empty($taskId)) {
            $this->redirectToURL('/tags');
            return;
        }

        $task = $this->taskService->find($taskId);
        if ($task) {
            $this->tagService->removeFromTask($tagId, $task);
        }

        $this->redirectToURL('/tags');
    }

    /**
     * Delete a tag
     */
    public function delete(): void
    {
        $id = $_GET['id'] ?? '';

        if (empty($id)) {
            $this->redirectToURL('/tags');
            return;
        }

        $this->tagService->remove($id);

        $this->redirectToURL('/tags');
    }
}
