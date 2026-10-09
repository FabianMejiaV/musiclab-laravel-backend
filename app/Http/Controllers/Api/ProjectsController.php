<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Projects\StoreProjectRequest;
use App\Http\Requests\Projects\UpdateProjectRequest;
use App\Http\Resources\ApiResourceCollection;
use App\Models\Project;
use App\Models\User;

class ProjectsController extends Controller
{
    /**
     * Returns a list of project of an User
     */
    public function index(User $user)
    {
        return new ApiResourceCollection($user->ownedProjects()->paginate(3));
    }

    /**
     * Return an specific project of an User
     */
    public function show(User $user, Project $project)
    {
        $p = $user->ownedProjects->find($project);

        if (!$p) {
            return response()->json(['message' => 'This project can not be returned']);
        }

        return response()->json($p);
    }

    /**
     * Store a project on DDBB
     */
    public function store(StoreProjectRequest $request, User $user)
    {
        $project = $user->ownedProjects()->create($request->validated());

        $project->content()->create([
            'content' => [],
        ]);

        return response()->json($project, 201);
    }

    /**
    * Update a project
     */
    public function update(UpdateProjectRequest $request, User $user, Project $project)
    {
        $ownedProject = $user->ownedProjects()
            ->whereKey($project->getKey())
            ->firstOrFail();

        $ownedProject->update($request->validated());

        return response()->json($ownedProject->fresh());
    }
}
