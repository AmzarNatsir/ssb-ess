<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'must_change_password'])->group(function () {
    Route::get('/change-password', [AuthController::class, 'showChangePasswordForm'])->name('password.change');
    Route::post('/change-password', [AuthController::class, 'updatePassword'])->name('password.update');

    Route::get('/', function () {
        return view('index');
    })->name('home');

    Route::get('/index', function () {
        return view('index');
    })->name('home');

    Route::get('/my-profile', [ProfileController::class, 'show'])->name('my-profile');
    Route::get('/leave', [\App\Http\Controllers\LeaveController::class, 'index'])->name('leave.index');
    Route::get('/leave/create', [\App\Http\Controllers\LeaveController::class, 'create'])->name('leave.create');
    Route::post('/leave/store', [\App\Http\Controllers\LeaveController::class, 'store'])->name('leave.store');
    Route::get('/leave/{id}/edit', [\App\Http\Controllers\LeaveController::class, 'edit'])->name('leave.edit');
    Route::put('/leave/{id}/update', [\App\Http\Controllers\LeaveController::class, 'update'])->name('leave.update');
    Route::get('/leave/remaining-entitlement/{id_jenis_cuti}', [\App\Http\Controllers\LeaveController::class, 'getRemainingEntitlement'])->name('leave.remaining-entitlement');
    Route::get('/leave/{id}', [\App\Http\Controllers\LeaveController::class, 'show'])->name('leave.show');
    Route::post('/leave/cancel/{id}', [\App\Http\Controllers\LeaveController::class, 'cancel'])->name('leave.cancel');

    Route::get('/permission', [\App\Http\Controllers\PermissionController::class, 'index'])->name('permission.index');
    Route::get('/permission/create', [\App\Http\Controllers\PermissionController::class, 'create'])->name('permission.create');
    Route::post('/permission/store', [\App\Http\Controllers\PermissionController::class, 'store'])->name('permission.store');
    Route::get('/permission/{id}/edit', [\App\Http\Controllers\PermissionController::class, 'edit'])->name('permission.edit');
    Route::put('/permission/{id}/update', [\App\Http\Controllers\PermissionController::class, 'update'])->name('permission.update');
    Route::get('/permission/{id}', [\App\Http\Controllers\PermissionController::class, 'show'])->name('permission.show');
    Route::post('/permission/cancel/{id}', [\App\Http\Controllers\PermissionController::class, 'cancel'])->name('permission.cancel');

    Route::get('/overtime', [\App\Http\Controllers\OvertimeController::class, 'index'])->name('overtime.index');
    Route::get('/overtime/create', [\App\Http\Controllers\OvertimeController::class, 'create'])->name('overtime.create');
    Route::post('/overtime/store', [\App\Http\Controllers\OvertimeController::class, 'store'])->name('overtime.store');
    Route::get('/overtime/{id}/edit', [\App\Http\Controllers\OvertimeController::class, 'edit'])->name('overtime.edit');
    Route::put('/overtime/{id}/update', [\App\Http\Controllers\OvertimeController::class, 'update'])->name('overtime.update');
    Route::get('/overtime/{id}', [\App\Http\Controllers\OvertimeController::class, 'show'])->name('overtime.show');
    Route::delete('/overtime/{id}', [\App\Http\Controllers\OvertimeController::class, 'destroy'])->name('overtime.destroy');

    Route::get('/attendence', [\App\Http\Controllers\AttendenceController::class, 'index'])->name('attendence.index');
    Route::get('/attendence/data', [\App\Http\Controllers\AttendenceController::class, 'getAttendenceData'])->name('attendence.data');

    Route::get('/perdis', [\App\Http\Controllers\PerdisController::class, 'index'])->name('perdis.index');
    Route::get('/perdis/{id}/details', [\App\Http\Controllers\PerdisController::class, 'getDetails'])->name('perdis.details');
    Route::post('/perdis/{id}/accountability', [\App\Http\Controllers\PerdisController::class, 'updateAccountability'])->name('perdis.accountability');
    // Super Admin modules

    // Route::get('/dashboard', function () {
    //     return view('dashboard');
    // })->name('dashboard');

    // 1) General settings

    Route::get('/profile-settings', [ProfileController::class, 'index'])->name('profile-settings');

    Route::get('/memorandum', [\App\Http\Controllers\MemorandumController::class, 'index'])->name('memorandum.index');
    Route::get('/memorandum/print-sp/{id}', [\App\Http\Controllers\MemorandumController::class, 'print_sp'])->name('memorandum.print_sp');
    Route::get('/memorandum/verify/{type}/{id}', [\App\Http\Controllers\MemorandumController::class, 'verify'])->name('memorandum.verify');
    Route::get('/memorandum/st-detail/{id}', [\App\Http\Controllers\MemorandumController::class, 'st_detail'])->name('memorandum.st_detail');
    Route::get('/memorandum/print-st/{id}', [\App\Http\Controllers\MemorandumController::class, 'print_st'])->name('memorandum.print_st');
    Route::get('/training', [\App\Http\Controllers\TrainingController::class, 'index'])->name('training.index');
    Route::get('/training/{id}/detail', [\App\Http\Controllers\TrainingController::class, 'detail'])->name('training.detail');
    Route::post('/training/{id}/report', [\App\Http\Controllers\TrainingController::class, 'submitReport'])->name('training.report');
    Route::get('/training/{id}/get-report', [\App\Http\Controllers\TrainingController::class, 'getReport'])->name('training.get_report');

    // Pinjaman Karyawan
    Route::get('/pinjaman', [\App\Http\Controllers\PinjamanController::class, 'index'])->name('pinjaman.index');
    Route::get('/pinjaman/create', [\App\Http\Controllers\PinjamanController::class, 'create'])->name('pinjaman.create');
    Route::post('/pinjaman', [\App\Http\Controllers\PinjamanController::class, 'store'])->name('pinjaman.store');
    Route::post('/pinjaman/{id}/cancel', [\App\Http\Controllers\PinjamanController::class, 'cancel'])->name('pinjaman.cancel');
    Route::get('/pinjaman/{id}/detail', [\App\Http\Controllers\PinjamanController::class, 'detail'])->name('pinjaman.detail');
    Route::get('/pinjaman/gaji-info', [\App\Http\Controllers\PinjamanController::class, 'gajiInfo'])->name('pinjaman.gaji_info');

    // Resign
    Route::get('/resign', [\App\Http\Controllers\ResignController::class, 'index'])->name('resign.index');
    Route::get('/resign/create', [\App\Http\Controllers\ResignController::class, 'create'])->name('resign.create');
    Route::post('/resign', [\App\Http\Controllers\ResignController::class, 'store'])->name('resign.store');
    Route::get('/resign/{id}/edit', [\App\Http\Controllers\ResignController::class, 'edit'])->name('resign.edit');
    Route::put('/resign/{id}', [\App\Http\Controllers\ResignController::class, 'update'])->name('resign.update');
    Route::post('/resign/{id}/cancel', [\App\Http\Controllers\ResignController::class, 'cancel'])->name('resign.cancel');
    Route::get('/resign/{id}/detail', [\App\Http\Controllers\ResignController::class, 'detail'])->name('resign.detail');

    // Exit Interview
    Route::get('/exit-interview/{id_resign}/create', [\App\Http\Controllers\ExitInterviewController::class, 'create'])->name('exit-interview.create');
    Route::post('/exit-interview', [\App\Http\Controllers\ExitInterviewController::class, 'store'])->name('exit-interview.store');
    Route::get('/exit-interview/{id}/edit', [\App\Http\Controllers\ExitInterviewController::class, 'edit'])->name('exit-interview.edit');
    Route::put('/exit-interview/{id}', [\App\Http\Controllers\ExitInterviewController::class, 'update'])->name('exit-interview.update');
    Route::post('/exit-interview/{id}/cancel', [\App\Http\Controllers\ExitInterviewController::class, 'cancel'])->name('exit-interview.cancel');
    Route::get('/exit-interview/{id}/detail', [\App\Http\Controllers\ExitInterviewController::class, 'detail'])->name('exit-interview.detail');

    // Payroll
    Route::get('/payroll', [\App\Http\Controllers\PayrollController::class, 'index'])->name('payroll.index');
    Route::get('/payroll/{id}/detail', [\App\Http\Controllers\PayrollController::class, 'show'])->name('payroll.show');
    Route::get('/payroll/{id}/print', [\App\Http\Controllers\PayrollController::class, 'print'])->name('payroll.print');
});
