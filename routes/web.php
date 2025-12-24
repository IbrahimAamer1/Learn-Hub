<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontHomeController;
use App\Http\Controllers\Back\BackHomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Back\CategoryController;
use App\Http\Controllers\Back\CourseController;
use App\Http\Controllers\Back\LessonController;
use App\Http\Controllers\Back\EnrollmentController;
use App\Http\Controllers\Front\EnrollmentController as FrontEnrollmentController;
use App\Http\Controllers\Front\CourseController as FrontCourseController;
use App\Http\Controllers\Front\LessonController as FrontLessonController;
use App\Http\Controllers\Instructor\DashboardController;
use App\Http\Controllers\Instructor\CourseController as InstructorCourseController;
use App\Http\Controllers\Instructor\LessonController as InstructorLessonController;
use App\Http\Controllers\Instructor\StudentController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//front routes
route::prefix('front')->name('front.')->group(function () {
    route::get('/', FrontHomeController::class)->middleware('auth')->name('index');
    route::view ('/login', 'front.auth.login')->name('login');
    route::view ('/register', 'front.auth.register')->name('register');
    route::view ('/forget-password', 'front.auth.forget-password')->name('forget-password');
    
    ##-----------------------------------courses routes (public)-----------------------------------##
    route::get('courses', [FrontCourseController::class, 'index'])->name('courses.index');
    route::get('courses/{course}', [FrontCourseController::class, 'show'])->name('courses.show');
    
    ##-----------------------------------lessons routes (public for free, authenticated for paid)-----------------------------------##
    route::get('courses/{course}/lessons/{lesson}', [FrontLessonController::class, 'show'])->name('lessons.show');
    
    ##-----------------------------------enrollments routes (students)-----------------------------------##
    route::middleware('auth')->group(function () {
        route::post('enrollments', [FrontEnrollmentController::class, 'store'])->name('enrollments.store');
        route::get('enrollments', [FrontEnrollmentController::class, 'index'])->name('enrollments.index');
        route::put('enrollments/{enrollment}', [FrontEnrollmentController::class, 'update'])->name('enrollments.update');
        
        // Mark lesson as watched (AJAX)
        route::post('lessons/{lesson}/mark-watched', [FrontLessonController::class, 'markAsWatched'])->name('lessons.mark-watched');
    });
});

require __DIR__.'/auth.php';

//back design routes
route::prefix('back')->name('back.')->group(function () {
    route::get('/', BackHomeController::class)->middleware('admin')->name('index');


    ##-----------------------------------admins routes-----------------------------------##
    route::resource('admins', AdminController::class)->middleware('admin')->names('admins');

    ##-----------------------------------roles routes-----------------------------------##
    route::resource('roles', RoleController::class)->middleware('admin')->names('roles');

    ##-----------------------------------users routes-----------------------------------##
    route::resource('users', UserController::class)->middleware('admin')->names('users');

    ##-----------------------------------categories routes-----------------------------------##
    route::resource('categories', CategoryController::class)->middleware('admin')->names('categories');

    ##-----------------------------------courses routes-----------------------------------##
    route::resource('courses', CourseController::class)->middleware('admin')->names('courses');

    ##-----------------------------------lessons routes-----------------------------------##
    route::resource('lessons', LessonController::class)->middleware('admin')->names('lessons');

    ##-----------------------------------enrollments routes (admin - read only)-----------------------------------##
    route::middleware('admin')->group(function () {
        route::get('enrollments', [EnrollmentController::class, 'index'])->name('enrollments.index');
        route::get('enrollments/{enrollment}', [EnrollmentController::class, 'show'])->name('enrollments.show');
    });

    
    require __DIR__.'/adminAuth.php';
});

//instructor routes
Route::prefix('instructor')->name('instructor.')->middleware(['admin', 'instructor'])->group(function () {
    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Courses
    Route::resource('courses', InstructorCourseController::class)->names('courses');
    
    // Lessons
    Route::resource('lessons', InstructorLessonController::class)->names('lessons');
    
    // Students
    Route::get('students', [StudentController::class, 'index'])->name('students.index');
    Route::get('students/{user}/courses/{course}', [StudentController::class, 'show'])->name('students.show');
});

