<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Migrate instructor data from admins table to users table.
     * Only admins who have courses (instructors) will be migrated.
     */
    public function up(): void
    {
        // Create temporary mapping table to store admin_id -> user_id mapping
        Schema::create('admin_user_mapping', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            
            $table->unique('admin_id');
            $table->index('user_id');
        });

        // Get all admins who have courses (instructors)
        $instructorAdmins = DB::table('admins')
            ->join('courses', 'admins.id', '=', 'courses.instructor_id')
            ->select('admins.*')
            ->distinct()
            ->get();

        $mapping = [];

        foreach ($instructorAdmins as $admin) {
            // Check if user with same email exists
            $existingUser = DB::table('users')->where('email', $admin->email)->first();

            if ($existingUser) {
                // Update existing user type to instructor
                DB::table('users')
                    ->where('id', $existingUser->id)
                    ->update(['type' => 'instructor']);

                $userId = $existingUser->id;
            } else {
                // Create new user with instructor data
                $userId = DB::table('users')->insertGetId([
                    'name' => $admin->name,
                    'email' => $admin->email,
                    'password' => $admin->password, // Keep same password hash
                    'email_verified_at' => $admin->email_verified_at,
                    'type' => 'instructor',
                    'created_at' => $admin->created_at ?? now(),
                    'updated_at' => $admin->updated_at ?? now(),
                ]);
            }

            // Store mapping
            $mapping[] = [
                'admin_id' => $admin->id,
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert mappings
        if (!empty($mapping)) {
            DB::table('admin_user_mapping')->insert($mapping);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert users type back to student for migrated instructors
        $mappedUserIds = DB::table('admin_user_mapping')->pluck('user_id');
        
        if ($mappedUserIds->isNotEmpty()) {
            DB::table('users')
                ->whereIn('id', $mappedUserIds)
                ->update(['type' => 'student']);
        }

        // Drop mapping table
        Schema::dropIfExists('admin_user_mapping');
    }
};
