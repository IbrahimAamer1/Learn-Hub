<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Update courses.instructor_id foreign key from admins to users table.
     */
    public function up(): void
    {
        // Drop the old foreign key constraint
        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['instructor_id']);
        });

        // Update all instructor_id values using the mapping
        $mappings = DB::table('admin_user_mapping')->get();
        
        foreach ($mappings as $mapping) {
            DB::table('courses')
                ->where('instructor_id', $mapping->admin_id)
                ->update(['instructor_id' => $mapping->user_id]);
        }

        // Add new foreign key constraint pointing to users table
        Schema::table('courses', function (Blueprint $table) {
            $table->foreign('instructor_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the foreign key constraint to users
        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['instructor_id']);
        });

        // Revert instructor_id values back to admin_id using mapping
        $mappings = DB::table('admin_user_mapping')->get();
        
        foreach ($mappings as $mapping) {
            DB::table('courses')
                ->where('instructor_id', $mapping->user_id)
                ->update(['instructor_id' => $mapping->admin_id]);
        }

        // Add back the foreign key constraint to admins
        Schema::table('courses', function (Blueprint $table) {
            $table->foreign('instructor_id')
                ->references('id')
                ->on('admins')
                ->onDelete('cascade');
        });
    }
};
