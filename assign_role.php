<?php
$user = App\Models\User::where('email', 'test@example.com')->first(); 
if ($user) { 
    $user->assignRole('employee'); 
    echo 'Role employee berhasil ditambahkan!'; 
}
