<?php

return [
    'reports' => [
        'title' => 'System Reports',
        'actions' => [
            'view_report' => 'View report',
            'back' => 'Go back',
            'clear_filters' => 'Clear filters',
            'generate' => 'Generate report',
            'download_csv' => 'Download CSV',
        ],
        'filters' => [
            'title' => 'Report filters',
            'any_option' => 'All',
            'date_from' => 'Start date',
            'date_to' => 'End date',
            'work_type' => 'Work type',
            'organizational_unit' => 'Organizational unit',
            'final_decision' => 'Final decision',
        ],
        'messages' => [
            'apply_filters' => 'Set the filters and click "Generate report" to view the results.',
            'empty_results' => 'There are no records for the selected filters.',
        ],
        'results' => [
            'title' => 'Report results',
            'rows' => 'rows',
        ],
        'columns' => [
            'status' => 'Status',
            'unit' => 'Organizational unit',
            'total' => 'Total',
            'final_decision' => 'Final decision',
            'total_evaluations' => 'Evaluations',
            'average_score' => 'Average score',
            'average_weighted_score' => 'Weighted average',
        ],
        'summary' => [
            'total_records' => 'Total works',
            'distinct_statuses' => 'Distinct statuses',
            'distinct_units' => 'Distinct units',
            'total_evaluations' => 'Total evaluations',
            'average_total_score' => 'Overall average score',
            'average_weighted_score' => 'Overall weighted average',
        ],
        'decisions' => [
            'approve' => 'Approved',
            'approve_with_conditions' => 'Approved with conditions',
            'reject' => 'Rejected',
            'pending' => 'Pending',
        ],
        'works_by_status' => [
            'title' => 'Works by status',
            'description' => 'Distribution of works based on their current status.',
        ],
        'works_by_unit' => [
            'title' => 'Works by academic unit',
            'description' => 'Number of works registered by each organizational unit.',
        ],
        'evaluation_statistics' => [
            'title' => 'Evaluation statistics',
            'description' => 'Summary of completed evaluations, final decisions, and score averages.',
        ],
    ],
];