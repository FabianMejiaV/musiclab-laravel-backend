<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Projects\UpdateProjectContentRequest;
use App\Http\Requests\Request;
use App\Models\Project;
use App\Models\User;

class ProjectContentController extends Controller
{
    public function show(User $user, Project $project)
    {
        $project = $user->ownedProjects()
            ->whereKey($project->getKey())
            ->firstOrFail();

        return response()->json($project->content);
    }

    public function update(UpdateProjectContentRequest $request, User $user, Project $project)
    {
        $project = $user->ownedProjects()
            ->whereKey($project->getKey())
            ->firstOrFail();
        $content = $project->content;

        $content->update($request->validated());

        return response()->json($content->fresh());
    }
}
