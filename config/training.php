<?php

use App\Models\CoachingTip;
use App\Models\Equipment;

return [
    'equipment' => ['label' => 'In The Bag', 'model' => Equipment::class, 'public' => 'in-the-bag'],
    'coaching' => ['label' => 'Coaching & Tips', 'model' => CoachingTip::class, 'public' => 'coaching-tips'],
];
