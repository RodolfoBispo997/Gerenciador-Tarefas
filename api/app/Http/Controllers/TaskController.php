<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $task = Task::with('user')->get();
        $user = User::all();

        return response()->json([
            'tasks' => $task,
            'users' => $user
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required|max:255|string',
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status,
            'user_id' => $request->user_id,
        ]);

        return response()->json([
            'status' => 'ok',
            'message' => 'Criado com sucesso.'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        return response()->json([
            'status' => 'ok',
            'date' => $task
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required|max:255|string',
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $task->update($request->only('title', 'description', 'status', 'user_id'));

        return response()->json([
            'status' => 'ok',
            'message' => 'Atualização realizada.',
            'task' => $task
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json([
            'status' => 'ok',
            'message' => 'Operação realizada com sucesso.'
        ]);
    }

    // Contagem de tasks por status (para gráfico de pizza)
    public function status()
    {
        $tasksByStatus = Task::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        return response()->json(
            [
                'status' => 'ok',
                'data' => $tasksByStatus
            ]
        );
    }
    // Contagem de tasks por usuário (para gráfico de barras)
    public function tasksPerUser()
    {
        $tasksByUser = User::withCount('tasks')
            ->get(['id', 'name', 'tasks_count']);

        return response()->json(
            [
                'status' => 'ok',
                'data' => $tasksByUser
            ]
        );
    }
    // Contagem de tasks por usuário separadas por status
    public function tasksByUserAndStatus()
    {
        $tasksByUserAndStatus = User::withCount([
            'tasks as pending_tasks_count' => fn($q) => $q->where('status', 'pending'),
            'tasks as in_progress_tasks_count' => fn($q) => $q->where('status', 'in_progress'),
            'tasks as completed_tasks_count' => fn($q) => $q->where('status', 'completed'),
        ])->get(['id', 'name']);

        return response()->json(
            [
                'status' => 'ok',
                'data' => $tasksByUserAndStatus
            ]
        );
    }
    // Percentual de conclusão (para indicador/progress bar)
    public function PercentageOfConclusion()
    {
        $total = Task::count();
        $completed = Task::where('status', 'completed')->count();

        $completionRate = $total > 0 ? round(($completed / $total) * 100, 2) : 0;

        return response()->json(
            [
                'status' => 'ok',
                'data' => $completionRate
            ]
        );
    }

    public function top5()
    {
        $topUsers = User::withCount([
            'tasks as completed_tasks_count' => fn($q) => $q->where('status', 'completed')
        ])
            ->orderByDesc('completed_tasks_count')
            ->take(5)
            ->get(['id', 'name']);

        return response()->json(
            [
                'status' => 'ok',
                'data' => $topUsers
            ]
        );
    }
}
