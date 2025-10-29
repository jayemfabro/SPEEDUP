<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check if we already have a teacher user with ID 2
$teacherUser = \App\Models\User::find(2);

if (!$teacherUser) {
    // Create a teacher user
    $teacherUser = \App\Models\User::create([
        'name' => 'Test Teacher',
        'first_name' => 'Test',
        'last_name' => 'Teacher',
        'email' => 'teacher@speeduptutorial.com',
        'username' => 'teacher',
        'password' => \Illuminate\Support\Facades\Hash::make('Teacher123!'),
        'email_verified_at' => now(),
        'role' => 'teacher',
        'status' => 'active',
    ]);

    echo "Created teacher user: ID " . $teacherUser->id . "\n";
} else {
    echo "Teacher user already exists: ID " . $teacherUser->id . "\n";
    echo "Role: " . ($teacherUser->role instanceof \App\Models\UserRole ? $teacherUser->role->value : $teacherUser->role) . "\n";
    echo "Is Teacher: " . ($teacherUser->isTeacher() ? 'Yes' : 'No') . "\n";
}

// Update the existing class to make sure it belongs to this teacher
$class = \App\Models\Admin\ClassModel::find(1);
if ($class) {
    $class->teacher_id = $teacherUser->id;
    $class->save();
    echo "Updated class 1 to belong to teacher user ID " . $teacherUser->id . "\n";
}