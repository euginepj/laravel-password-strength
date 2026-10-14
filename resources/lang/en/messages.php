<?php

return [
    'min_length' => 'The :attribute must be at least :min characters.',
    'letters' => 'The :attribute must contain at least one letter.',
    'mixed_case' => 'The :attribute must contain both uppercase and lowercase letters.',
    'numbers' => 'The :attribute must contain at least one number.',
    'symbols' => 'The :attribute must contain at least one special symbol.',
    'common' => 'The :attribute is too common. Please choose a more secure password.',
    'sequential' => 'The :attribute cannot contain consecutive or repetitive character sequences.',
    'score' => 'The :attribute is too weak. Please choose a stronger password (at least :level).',

    'levels' => [
        0 => 'Very Weak',
        1 => 'Weak',
        2 => 'Medium',
        3 => 'Strong',
        4 => 'Very Strong',
    ],
];
