<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\SecurityController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| ในเมื่อ Nginx ทำการตัด /sdv ออกให้แล้ว (Rewrite) 
| เราไม่ต้องใช้ Route::prefix('sdv') มาคลุมซ้ำซ้อนครับ
|
*/

// 1. หน้าแรกสุด (ในกรณีที่มีคนเผลอเข้าแบบไม่มี /sdv)
Route::get('/', function () {
    return redirect()->route('login');
});

// --- Guest Routes (ไม่ต้อง Login) ---
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

// --- Logged-in Routes (ต้อง Login ก่อน) ---
Route::middleware(['auth'])->group(function () {

    Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/security/check-sticker/{number}', [AdminController::class, 'checkSticker'])->name('security.check.sticker');

    // Student Group
    Route::prefix('student')->name('student.')->group(function () {
        Route::get('/view', [StudentController::class, 'view'])->name('view');
        Route::put('/update/{id}', [StudentController::class, 'update'])->name('update');
        Route::delete('/vehicle/{id}/delete', [StudentController::class, 'deleteVehicle'])->name('vehicle.delete');
        Route::get('/advisor/create', [StudentController::class, 'advisorCreate'])->name('advisor.create');
        Route::post('/advisor/store', [StudentController::class, 'storeAdvisor'])->name('advisor.store');
        Route::get('/faculty/create', [StudentController::class, 'facultyCreate'])->name('faculty.create');
        Route::post('/faculty/store', [StudentController::class, 'storeFaculty'])->name('faculty.store');
        Route::get('/major/create', [StudentController::class, 'majorCreate'])->name('major.create');
        Route::post('/major/store', [StudentController::class, 'storeMajor'])->name('major.store');
    });

    // Admin Group
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/create', [AdminController::class, 'create'])->name('create');
        Route::post('/store', [AdminController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [AdminController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [AdminController::class, 'update'])->name('update');
        Route::get('/student/sticker/{id}', [AdminController::class, 'generateSticker'])->name('student.sticker');
        Route::get('/print-bulk-stickers', [AdminController::class, 'generateBulkStickers'])->name('stickers.bulk');
        Route::delete('/vehicle/{id}/delete', [AdminController::class, 'deleteVehicle'])->name('vehicle.delete');
        Route::delete('/student/{id}/destroy', [AdminController::class, 'destroyStudent'])->name('student.destroy');
        Route::post('/update-slots', [AdminController::class, 'updateSlots'])->name('updateSlots');
        Route::get('/export-students', [AdminController::class, 'exportStudents'])->name('export');
        Route::get('/student/{id}/show', [AdminController::class, 'show'])->name('student.show');
        Route::delete('/clear-all-students', [AdminController::class, 'clearAllStudents'])->name('clearAllStudents');

        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [AdminUserController::class, 'index'])->name('index');
            Route::get('/create', [AdminUserController::class, 'create'])->name('create');
            Route::post('/', [AdminUserController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [AdminUserController::class, 'edit'])->name('edit');
            Route::put('/{id}', [AdminUserController::class, 'update'])->name('update');
            Route::delete('/{id}', [AdminUserController::class, 'destroy'])->name('destroy');
        });
    });

    // Security Group
    Route::prefix('security')->name('security.')->group(function () {
        Route::get('/dashboard', [SecurityController::class, 'dashboard'])->name('dashboard');
        Route::get('/students', [SecurityController::class, 'students'])->name('students');
        Route::get('/student/{id}/show', [SecurityController::class, 'show'])->name('student.show');
    });
});