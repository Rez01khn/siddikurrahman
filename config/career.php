<?php

use App\Models\Achievement;

return [
    'modules' => [
        'achievements' => ['label' => 'Key Achievements', 'model' => Achievement::class],
    ],
    'categories' => [
        'Asian Tour Victory' => 'Asian Tour Victory',
        'Historic Asian Tour Title' => 'Historic Asian Tour Title',
        'Historic Milestone' => 'Historic Milestone',
        'National Record' => 'National Record',
        'Other' => 'Other Achievement',
    ],
];
