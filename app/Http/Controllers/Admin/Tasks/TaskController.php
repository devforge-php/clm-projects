<?php

namespace App\Http\Controllers\Admin\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskRequest;
use App\Services\TaskService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = $this->taskService->getAllTasks();
        return response()->json(['data' => $tasks]);
    }

    /**
     * Show a single task by ID
     */
    public function show(string $id)
    {
        $task = $this->taskService->getTaskById($id);
        return response()->json(['task' => $task]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskRequest $request)
    {
        $task = $this->taskService->createTask($request);
        return response()->json([
            'message' => 'Task created successfully',
            'task' => $task
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaskRequest $request, string $id)
    {
        $task = $this->taskService->updateTask($request, $id);
        return response()->json(['message' => 'Task updated successfully', 'task' => $task]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->taskService->deleteTask($id);
        return response()->json(['message' => 'Task deleted successfully']);
    }
}
