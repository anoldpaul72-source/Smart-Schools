<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\LeaderController;
use App\Http\Controllers\AccountantController;
use App\Http\Controllers\LocaleController;

// Language Switcher
Route::get('/lang/{locale}', [LocaleController::class, 'switch'])->name('lang.switch');

// 1. Landing Page
Route::get('/', [HomeController::class, 'index'])->name('home');

// 2. Authentication
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

use App\Http\Controllers\TimetableController;

// School Timetable (Public view + view_timetable.php legacy alias)
Route::get('/timetable', [TimetableController::class, 'index'])->name('timetable.index');
Route::get('/view_timetable.php', [TimetableController::class, 'index']);

Route::middleware('auth')->group(function () {
    Route::get('/change-password', [AuthController::class, 'showChangePassword'])->name('password.change');
    Route::post('/change-password', [AuthController::class, 'updatePassword'])->name('password.update');

    // School Timetable management
    Route::post('/timetable/auto-generate', [TimetableController::class, 'autoGenerate'])->name('timetable.auto_generate');
    Route::post('/timetable/slot', [TimetableController::class, 'saveSlot'])->name('timetable.save_slot');
    Route::post('/timetable/slot/delete', [TimetableController::class, 'deleteSlot'])->name('timetable.delete_slot');
});

// 3. Admin Portal (role: Admin)
Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/backup/export', [AdminController::class, 'exportBackup'])->name('backup.export');
    Route::post('/schools', [AdminController::class, 'addSchool'])->name('schools.store');
    Route::put('/schools/{id}', [AdminController::class, 'updateSchool'])->name('schools.update');
    Route::delete('/schools/{id}', [AdminController::class, 'deleteSchool'])->name('schools.delete');
    
    // Subjects
    Route::post('/subjects', [AdminController::class, 'addSubject'])->name('subjects.store');
    Route::delete('/subjects/{id}', [AdminController::class, 'deleteSubject'])->name('subjects.delete');

    // Users
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('users.delete');

    // Students
    Route::get('/students', [AdminController::class, 'students'])->name('students');
    Route::post('/students', [AdminController::class, 'storeStudent'])->name('students.store');
    Route::delete('/students/{id}', [AdminController::class, 'deleteStudent'])->name('students.delete');
    Route::post('/students/upload-csv', [AdminController::class, 'uploadStudentsCsv'])->name('students.upload_csv');
    Route::get('/students/upload-csv', function () {
        return redirect()->route('admin.students')->with('error', '⚠️ Seva ilikuwa inalala au ukurasa ulifanya refresh. Tafadhali chagua faili tena na ubonyeze kitufe cha kupakia.');
    });
    Route::get('/students/download-template', [AdminController::class, 'downloadStudentTemplate'])->name('students.template');
});

// 4. Teacher Portal (role: Teacher, Admin, Academic Master, Headmaster)
Route::middleware(['auth', 'role:Teacher,Admin,Academic Master,Headmaster'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/marks', [TeacherController::class, 'marks'])->name('marks');
    Route::post('/marks/single', [TeacherController::class, 'storeSingleMark'])->name('marks.store_single');
    Route::get('/get-students', [TeacherController::class, 'getStudents'])->name('get_students');
    Route::get('/download-template', [TeacherController::class, 'downloadTemplate'])->name('download_template');
    Route::get('/upload-marks', [TeacherController::class, 'showUploadMarks'])->name('upload_marks');
    Route::post('/marks/upload-csv', [TeacherController::class, 'uploadMarksCsv'])->name('marks.upload_csv');
    Route::get('/marks/upload-csv', function () {
        return redirect()->route('teacher.upload_marks')->with('error', '⚠️ Seva ilikuwa inalala au ukurasa ulifanya refresh. Tafadhali chagua faili tena na upakie.');
    });
    Route::get('/attendance', [TeacherController::class, 'attendance'])->name('attendance');
    Route::post('/attendance', [TeacherController::class, 'storeAttendance'])->name('attendance.store');
    Route::get('/attendance/history', [TeacherController::class, 'attendanceHistory'])->name('attendance.history');
    Route::get('/timetable', [TeacherController::class, 'timetable'])->name('timetable');
    Route::get('/marks/all', [TeacherController::class, 'viewAllMarks'])->name('marks.all');
    Route::post('/marks/send-bulk-sms', [TeacherController::class, 'sendBulkReportSms'])->name('marks.send_bulk_sms');
    Route::post('/marks/send-single-sms', [TeacherController::class, 'sendSingleReportSms'])->name('marks.send_single_sms');
});

// Shared Academic & SMS Routes (accessible by Teachers, Leaders, and Admins)
Route::middleware(['auth', 'role:Teacher,Headmaster,Academic Master,Admin'])->group(function () {
    Route::get('/academic/marks/all', [TeacherController::class, 'viewAllMarks'])->name('academic.marks.all');
    Route::get('/all-marks', [TeacherController::class, 'viewAllMarks'])->name('marks.all');
    Route::post('/sms/send-bulk', [TeacherController::class, 'sendBulkReportSms'])->name('sms.send_bulk');
    Route::post('/sms/send-single', [TeacherController::class, 'sendSingleReportSms'])->name('sms.send_single');
});

// Legacy aliases
Route::get('/matokeo.php', [TeacherController::class, 'viewAllMarks']);
Route::get('/teacher_timetable.php', [TeacherController::class, 'timetable']);
Route::get('/add_attendance.php', function () {
    return redirect()->route('teacher.attendance');
});
Route::get('/view_attendance.php', function () {
    return redirect()->route('teacher.attendance.history');
});

// 5. Parent Portal (role: Parent)
Route::middleware(['auth', 'role:Parent'])->prefix('parent')->name('parent.')->group(function () {
    Route::get('/reports', [ParentController::class, 'reports'])->name('reports');
    Route::post('/reports/send-sms', [ParentController::class, 'requestReportSms'])->name('reports.send_sms');
});

// 6. Leadership Portal (role: Headmaster, Academic Master, Admin)
Route::middleware(['auth', 'role:Headmaster,Academic Master,Admin'])->prefix('leader')->name('leader.')->group(function () {
    Route::get('/dashboard', [LeaderController::class, 'dashboard'])->name('dashboard');
});

// 7. Accountant Portal (role: Accountant, Admin)
Route::middleware(['auth', 'role:Accountant,Admin'])->prefix('accountant')->name('accountant.')->group(function () {
    Route::get('/fees', [AccountantController::class, 'fees'])->name('fees');
    Route::get('/collect-payment', [AccountantController::class, 'showCollectPayment'])->name('collect_payment');
    Route::post('/fee-structure', [AccountantController::class, 'storeFeeStructure'])->name('fee_structure.store');
    Route::post('/payment', [AccountantController::class, 'recordPayment'])->name('payment.store');
});
