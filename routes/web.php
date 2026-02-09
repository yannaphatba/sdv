<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\SecurityController;
use App\Http\Controllers\AdvisorController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\MajorController;

// 1. Redirect หน้าแรก
Route::get('/', function () {
    return redirect('/sdv/login');
});

// ✅ 2. กลุ่มใหญ่ prefix 'sdv'
Route::prefix('sdv')->group(function () {

    // --- Guest Routes ---
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');

    Route::get('/saml2/{idp}/login', function ($idp) {
        return "<h1>ระบบ SSO มหาวิทยาลัย</h1><p>จำลองการเชื่อมต่อ...</p>";
    })->name('saml2_login');

    // --- Logged-in Routes (ต้อง Login ก่อน) ---
    Route::middleware(['auth'])->group(function () {

        // ⭐ [บรรทัดที่ริวทัก - ดึงกลับมาแล้ว]
        Route::get('/security/check-sticker/{number}', [AdminController::class, 'checkSticker'])->name('security.check.sticker');

        // [บรรทัด Logout - ดึงกลับมาแล้ว]
        Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

        // Student Group (ครบถ้วน)
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

        // Admin Group (ครบถ้วน)
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

        // Security Group (ครบถ้วน)
        Route::prefix('security')->name('security.')->group(function () {
            Route::get('/dashboard', [SecurityController::class, 'dashboard'])->name('dashboard');
            Route::get('/students', [SecurityController::class, 'students'])->name('students');
            Route::get('/student/{id}/show', [SecurityController::class, 'show'])->name('student.show');
        });
    });
});