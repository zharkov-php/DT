<?php

namespace App\Http\Services;

class AnalyticService
{
    public function calculateTaskAnalytics(array $tasks): array
    {
        $statusSummary = [
            'todo' => 0,
            'in_progress' => 0,
            'done' => 0,
        ];

        $prioritySummary = [
            'low' => 0,
            'medium' => 0,
            'high' => 0,
        ];

        $memberTaskSummary = [];

        foreach ($tasks as $task) {
            if (isset($statusSummary[$task['status']])) {
                $statusSummary[$task['status']]++;
            }

            if (isset($prioritySummary[$task['priority']])) {
                $prioritySummary[$task['priority']]++;
            }

            $assignedTo = $task['assigned_to'] ?? null;
            if ($assignedTo) {
                if (!isset($memberTaskSummary[$assignedTo])) {
                    $memberTaskSummary[$assignedTo] = 0;
                }
                $memberTaskSummary[$assignedTo]++;
            }
        }

        $memberTaskSummaryFormatted = [];
        foreach ($memberTaskSummary as $userId => $taskCount) {
            $memberTaskSummaryFormatted[] = [
                'user_id' => $userId,
                'task_count' => $taskCount,
            ];
        }

        return [
            'status_summary' => $statusSummary,
            'priority_summary' => $prioritySummary,
            'member_task_summary' => $memberTaskSummaryFormatted,
        ];
    }
}
