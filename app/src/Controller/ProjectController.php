<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\ProjectService;
use App\Service\UserService;
use App\Entity\Project;
use App\Entity\User;

/**
 * Controller for managing Project entities
 * Handles CRUD operations and business logic for projects
 */
final class ProjectController extends AbstractController
{
    private ProjectService $projectService;
    private UserService $userService;

    public function __construct()
    {
        $this->projectService = new ProjectService();
        $this->userService = new UserService();
    }

    /**
     * Display list of all projects
     */
    public function index(): void
    {
        $projects = $this->projectService->findAll();
        $activeProjects = $this->projectService->findActive();
        $archivedProjects = $this->projectService->findArchived();

        $this->render('project/list', [
            'projects' => $projects,
            'activeProjects' => $activeProjects,
            'archivedProjects' => $archivedProjects,
            'totalCount' => count($projects),
            'activeCount' => count($activeProjects),
            'archivedCount' => count($archivedProjects),
        ]);
    }

    /**
     * Display form to create a new project
     */
    public function create(): void
    {
        $users = $this->userService->findAll();

        $this->render('project/create', [
            'users' => $users,
        ]);
    }

    /**
     * Store a newly created project
     */
    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectToURL('/projects');
            return;
        }

        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $color = $_POST['color'] ?? '#3498db';
        $userId = $_POST['user_id'] ?? '';

        if (empty($name) || empty($userId)) {
            $this->render('project/create', [
                'error' => 'Name and user are required',
                'users' => $this->userService->findAll(),
            ]);
            return;
        }

        $user = $this->userService->find((int) $userId);
        if (!$user) {
            $this->render('project/create', [
                'error' => 'User not found',
                'users' => $this->userService->findAll(),
            ]);
            return;
        }

        $project = new Project(
            user: $user,
            name: $name,
            color: $color
        );

        if (!empty($description)) {
            $project->setDescription($description);
        }

        $this->projectService->create($project);

        $this->redirectToURL('/projects');
    }

    /**
     * Display a specific project
     */
    public function show(): void
    {
        $id = $_GET['id'] ?? '';

        if (empty($id)) {
            $this->redirectToURL('/projects');
            return;
        }

        $project = $this->projectService->find($id);

        if (!$project) {
            $this->render('error/not-found', [
                'message' => 'Project not found',
            ], false);
            return;
        }

        $this->render('project/show', [
            'project' => $project,
        ]);
    }

    /**
     * Display form to edit a project
     */
    public function edit(): void
    {
        $id = $_GET['id'] ?? '';

        if (empty($id)) {
            $this->redirectToURL('/projects');
            return;
        }

        $project = $this->projectService->find($id);

        if (!$project) {
            $this->render('error/not-found', [
                'message' => 'Project not found',
            ], false);
            return;
        }

        $users = $this->userService->findAll();

        $this->render('project/edit', [
            'project' => $project,
            'users' => $users,
        ]);
    }

    /**
     * Update the specified project
     */
    public function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectToURL('/projects');
            return;
        }

        $id = $_POST['id'] ?? '';
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $color = $_POST['color'] ?? '#3498db';

        if (empty($id) || empty($name)) {
            $this->redirectToURL('/projects');
            return;
        }

        $project = $this->projectService->find($id);

        if (!$project) {
            $this->render('error/not-found', [
                'message' => 'Project not found',
            ], false);
            return;
        }

        $project->setName($name);
        $project->setDescription($description);
        $project->setColor($color);

        $this->projectService->update($project);

        $this->redirectToURL('/projects');
    }

    /**
     * Archive a project
     */
    public function archive(): void
    {
        $id = $_GET['id'] ?? '';

        if (empty($id)) {
            $this->redirectToURL('/projects');
            return;
        }

        $this->projectService->archive($id);

        $this->redirectToURL('/projects');
    }

    /**
     * Unarchive a project
     */
    public function unarchive(): void
    {
        $id = $_GET['id'] ?? '';

        if (empty($id)) {
            $this->redirectToURL('/projects');
            return;
        }

        $this->projectService->unarchive($id);

        $this->redirectToURL('/projects');
    }

    /**
     * Delete a project
     */
    public function delete(): void
    {
        $id = $_GET['id'] ?? '';

        if (empty($id)) {
            $this->redirectToURL('/projects');
            return;
        }

        $this->projectService->remove($id);

        $this->redirectToURL('/projects');
    }
}
