<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomAuthController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;

// Route::get('deals-dashboard', [CustomAuthController::class, 'deals-dashboard']);
// Route::get('index', [CustomAuthController::class, 'index'])->name('index');
// Route::post('custom-login', [CustomAuthController::class, 'customLogin'])->name('login.custom');
// Route::get('register', [CustomAuthController::class, 'register'])->name('register-user');
// Route::post('custom-registration', [CustomAuthController::class, 'customRegistration'])->name('register.custom');
// Route::get('signout', [CustomAuthController::class, 'signOut'])->name('signout');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
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

    Route::get('/leads-dashboard', function () {
        return view('leads-dashboard');
    })->name('leads-dashboard');

    Route::get('/project-dashboard', function () {
        return view('project-dashboard');
    })->name('project-dashboard');

    Route::get('/notifications', function () {
        return view('notifications');
    })->name('notifications');

    // Super Admin modules

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/company', function () {
        return view('company');
    })->name('company');

    Route::get('/subscription', function () {
        return view('subscription');
    })->name('subscription');

    Route::get('/packages', function () {
        return view('packages');
    })->name('packages');

    Route::get('/domain', function () {
        return view('domain');
    })->name('domain');

    Route::get('/purchase-transaction', function () {
        return view('purchase-transaction');
    })->name('purchase-transaction');

    // Layout Modules

    Route::get('/layout-mini', function () {
        return view('layout-mini');
    })->name('layout-mini');

    Route::get('/layout-hoverview', function () {
        return view('layout-hoverview');
    })->name('layout-hoverview');

    Route::get('/layout-hidden', function () {
        return view('layout-hidden');
    })->name('layout-hidden');

    Route::get('/layout-fullwidth', function () {
        return view('layout-fullwidth');
    })->name('layout-fullwidth');

    Route::get('/layout-rtl', function () {
        return view('layout-rtl');
    })->name('layout-rtl');

    Route::get('/layout-dark', function () {
        return view('layout-dark');
    })->name('layout-dark');

    // CRM Modules

    Route::get('/contacts', function () {
        return view('contacts');
    })->name('contacts');

    Route::get('/contacts-list', function () {
        return view('contacts-list');
    })->name('contacts-list');

    Route::get('/contact-details', function () {
        return view('contact-details');
    })->name('contact-details');

    Route::get('/companies', function () {
        return view('companies');
    })->name('companies');

    Route::get('/companies-list', function () {
        return view('companies-list');
    })->name('companies-list');

    Route::get('/company-details', function () {
        return view('company-details');
    })->name('company-details');

    Route::get('/deals', function () {
        return view('deals');
    })->name('deals');

    Route::get('/deals-list', function () {
        return view('deals-list');
    })->name('deals-list');

    Route::get('/deals-details', function () {
        return view('deals-details');
    })->name('deals-details');

    Route::get('/leads', function () {
        return view('leads');
    })->name('leads');

    Route::get('/leads-list', function () {
        return view('leads-list');
    })->name('leads-list');

    Route::get('/leads-details', function () {
        return view('leads-details');
    })->name('leads-details');

    Route::get('/pipeline', function () {
        return view('pipeline');
    })->name('pipeline');

    Route::get('/campaign', function () {
        return view('campaign');
    })->name('campaign');

    Route::get('/campaign-complete', function () {
        return view('campaign-complete');
    })->name('campaign-complete');

    Route::get('/campaign-archieve', function () {
        return view('campaign-archieve');
    })->name('campaign-archieve');

    Route::get('/projects', function () {
        return view('projects');
    })->name('projects');

    Route::get('/projects-list', function () {
        return view('projects-list');
    })->name('projects-list');

    Route::get('/project-details', function () {
        return view('project-details');
    })->name('project-details');

    Route::get('/tasks', function () {
        return view('tasks');
    })->name('tasks');

    Route::get('/tasks-completed', function () {
        return view('tasks-completed');
    })->name('tasks-completed');

    Route::get('/tasks-important', function () {
        return view('tasks-important');
    })->name('tasks-important');

    Route::get('/proposals', function () {
        return view('proposals');
    })->name('proposals');

    Route::get('/proposals-list', function () {
        return view('proposals-list');
    })->name('proposals-list');

    Route::get('/contracts', function () {
        return view('contracts');
    })->name('contracts');

    Route::get('/contracts-list', function () {
        return view('contracts-list');
    })->name('contracts-list');

    Route::get('/estimations', function () {
        return view('estimations');
    })->name('estimations');

    Route::get('/estimations-list', function () {
        return view('estimations-list');
    })->name('estimations-list');

    Route::get('/invoices', function () {
        return view('invoices');
    })->name('invoices');

    Route::get('/invoice-list', function () {
        return view('invoice-list');
    })->name('invoice-list');

    Route::get('/invoices-details', function () {
        return view('invoices-details');
    })->name('invoices-details');

    Route::get('/payments', function () {
        return view('payments');
    })->name('payments');

    Route::get('/analytics', function () {
        return view('analytics');
    })->name('analytics');

    Route::get('/activities', function () {
        return view('activities');
    })->name('activities');

    Route::get('/activity-calls', function () {
        return view('activity-calls');
    })->name('activity-calls');

    Route::get('/activity-mail', function () {
        return view('activity-mail');
    })->name('activity-mail');

    Route::get('/activity-meeting', function () {
        return view('activity-meeting');
    })->name('activity-meeting');

    Route::get('/activity-task', function () {
        return view('activity-task');
    })->name('activity-task');

    // Report Modules

    Route::get('/lead-reports', function () {
        return view('lead-reports');
    })->name('lead-reports');

    Route::get('/deal-reports', function () {
        return view('deal-reports');
    })->name('deal-reports');

    Route::get('/contact-reports', function () {
        return view('contact-reports');
    })->name('contact-reports');

    Route::get('/company-reports', function () {
        return view('company-reports');
    })->name('company-reports');

    Route::get('/project-reports', function () {
        return view('project-reports');
    })->name('project-reports');

    Route::get('/task-reports', function () {
        return view('task-reports');
    })->name('task-reports');

    // CRM Settings Modules

    Route::get('/sources', function () {
        return view('sources');
    })->name('sources');

    Route::get('/lost-reason', function () {
        return view('lost-reason');
    })->name('lost-reason');

    Route::get('/contact-stage', function () {
        return view('contact-stage');
    })->name('contact-stage');

    Route::get('/industry', function () {
        return view('industry');
    })->name('industry');

    Route::get('/calls', function () {
        return view('calls');
    })->name('calls');

    // User management Modules

    Route::get('/manage-users', function () {
        return view('manage-users');
    })->name('manage-users');

    Route::get('/roles-permissions', function () {
        return view('roles-permissions');
    })->name('roles-permissions');



    Route::get('/delete-request', function () {
        return view('delete-request');
    })->name('delete-request');

    // Membership Modules

    Route::get('/membership-plans', function () {
        return view('membership-plans');
    })->name('membership-plans');

    Route::get('/membership-addons', function () {
        return view('membership-addons');
    })->name('membership-addons');

    Route::get('/membership-transactions', function () {
        return view('membership-transactions');
    })->name('membership-transactions');

    // Content Modules

    Route::get('/pages', function () {
        return view('pages');
    })->name('pages');

    Route::get('/add-page', function () {
        return view('add-page');
    })->name('add-page');

    Route::get('/edit-page', function () {
        return view('edit-page');
    })->name('edit-page');

    Route::get('/blogs', function () {
        return view('blogs');
    })->name('blogs');

    Route::get('/blog-categories', function () {
        return view('blog-categories');
    })->name('blog-categories');

    Route::get('/blog-comments', function () {
        return view('blog-comments');
    })->name('blog-comments');

    Route::get('/blog-tags', function () {
        return view('blog-tags');
    })->name('blog-tags');

    Route::get('/blog-details', function () {
        return view('blog-details');
    })->name('blog-details');

    Route::get('/add-blog', function () {
        return view('add-blog');
    })->name('add-blog');

    Route::get('/edit-blog', function () {
        return view('edit-blog');
    })->name('edit-blog');

    Route::get('/countries', function () {
        return view('countries');
    })->name('countries');

    Route::get('/states', function () {
        return view('states');
    })->name('states');

    Route::get('/cities', function () {
        return view('cities');
    })->name('cities');

    Route::get('/testimonials', function () {
        return view('testimonials');
    })->name('testimonials');

    Route::get('/faq', function () {
        return view('faq');
    })->name('faq');

    // Support Modules

    Route::get('/contact-messages', function () {
        return view('contact-messages');
    })->name('contact-messages');

    Route::get('/tickets', function () {
        return view('tickets');
    })->name('tickets');

    Route::get('/ticket-details', function () {
        return view('ticket-details');
    })->name('ticket-details');

    // Applications Modules

    Route::get('/chat', function () {
        return view('chat');
    })->name('chat');

    Route::get('/video-call', function () {
        return view('video-call');
    })->name('video-call');

    Route::get('/audio-call', function () {
        return view('audio-call');
    })->name('audio-call');

    Route::get('/call-history', function () {
        return view('call-history');
    })->name('call-history');

    Route::get('/calendar', function () {
        return view('calendar');
    })->name('calendar');

    Route::get('/email', function () {
        return view('email');
    })->name('email');

    Route::get('/email-reply', function () {
        return view('email-reply');
    })->name('email-reply');

    Route::get('/invoice', function () {
        return view('invoice');
    })->name('invoice');

    Route::get('/add-invoices', function () {
        return view('add-invoices');
    })->name('add-invoices');

    Route::get('/edit-invoices', function () {
        return view('edit-invoices');
    })->name('edit-invoices');

    Route::get('/invoice-details', function () {
        return view('invoice-details');
    })->name('invoice-details');

    Route::get('/todo', function () {
        return view('todo');
    })->name('todo');

    Route::get('/todo-list', function () {
        return view('todo-list');
    })->name('todo-list');

    Route::get('/notes', function () {
        return view('notes');
    })->name('notes');

    Route::get('/kanban-view', function () {
        return view('kanban-view');
    })->name('kanban-view');

    Route::get('/file-manager', function () {
        return view('file-manager');
    })->name('file-manager');

    Route::get('/social-feed', function () {
        return view('social-feed');
    })->name('social-feed');

    // Settings Modules

    // 1) General settings

    Route::get('/profile-settings', [ProfileController::class, 'index'])->name('profile-settings');

    Route::get('/security-settings', function () {
        return view('security-settings');
    })->name('security-settings');

    Route::get('/notifications-settings', function () {
        return view('notifications-settings');
    })->name('notifications-settings');

    Route::get('/connected-apps', function () {
        return view('connected-apps');
    })->name('connected-apps');

    // 2) Webiste Settings

    Route::get('/company-settings', function () {
        return view('company-settings');
    })->name('company-settings');

    Route::get('/localization-settings', function () {
        return view('localization-settings');
    })->name('localization-settings');

    Route::get('/prefixes-settings', function () {
        return view('prefixes-settings');
    })->name('prefixes-settings');

    Route::get('/preference-settings', function () {
        return view('preference-settings');
    })->name('preference-settings');

    Route::get('/appearance-settings', function () {
        return view('appearance-settings');
    })->name('appearance-settings');

    Route::get('/language-settings', function () {
        return view('language-settings');
    })->name('language-settings');

    Route::get('/language-web', function () {
        return view('language-web');
    })->name('language-web');

    Route::get('/language-web-edit', function () {
        return view('language-web-edit');
    })->name('language-web-edit');

    // 3)App Settings

    Route::get('/invoice-settings', function () {
        return view('invoice-settings');
    })->name('invoice-settings');

    Route::get('/printers-settings', function () {
        return view('printers-settings');
    })->name('printers-settings');

    Route::get('/custom-fields-setting', function () {
        return view('custom-fields-setting');
    })->name('custom-fields-setting');

    // 4) System settings

    Route::get('/email-settings', function () {
        return view('email-settings');
    })->name('email-settings');

    Route::get('/sms-gateways', function () {
        return view('sms-gateways');
    })->name('sms-gateways');

    Route::get('/gdpr-cookies', function () {
        return view('gdpr-cookies');
    })->name('gdpr-cookies');

    // 5) Finance Settings

    Route::get('/payment-gateways', function () {
        return view('payment-gateways');
    })->name('payment-gateways');

    Route::get('/bank-accounts', function () {
        return view('bank-accounts');
    })->name('bank-accounts');

    Route::get('/tax-rates', function () {
        return view('tax-rates');
    })->name('tax-rates');

    Route::get('/currencies', function () {
        return view('currencies');
    })->name('currencies');

    // 6) Other settings

    Route::get('/sitemap', function () {
        return view('sitemap');
    })->name('sitemap');

    Route::get('/clear-cache', function () {
        return view('clear-cache');
    })->name('clear-cache');

    Route::get('/storage', function () {
        return view('storage');
    })->name('storage');

    Route::get('/cronjob', function () {
        return view('cronjob');
    })->name('cronjob');

    Route::get('/ban-ip-address', function () {
        return view('ban-ip-address');
    })->name('ban-ip-address');

    Route::get('/system-backup', function () {
        return view('system-backup');
    })->name('system-backup');

    Route::get('/database-backup', function () {
        return view('database-backup');
    })->name('database-backup');

    Route::get('/system-update', function () {
        return view('system-update');
    })->name('system-update');

    // Layout Pages

    Route::get('/layout-full-width', function () {
        return view('layout-full-width');
    })->name('layout-full-width');

    Route::get('/layout-hidden', function () {
        return view('layout-hidden');
    })->name('layout-hidden');

    Route::get('/layout-hover-view', function () {
        return view('layout-hover-view');
    })->name('layout-hover-view');

    Route::get('/layout-mini', function () {
        return view('layout-mini');
    })->name('layout-mini');

    Route::get('/layout-rtl', function () {
        return view('layout-rtl');
    })->name('layout-rtl');

    Route::get('/memorandum', [\App\Http\Controllers\MemorandumController::class, 'index'])->name('memorandum.index');
    Route::get('/memorandum/print-sp/{id}', [\App\Http\Controllers\MemorandumController::class, 'print_sp'])->name('memorandum.print_sp');
    Route::get('/memorandum/verify/{type}/{id}', [\App\Http\Controllers\MemorandumController::class, 'verify'])->name('memorandum.verify');
    Route::get('/memorandum/st-detail/{id}', [\App\Http\Controllers\MemorandumController::class, 'st_detail'])->name('memorandum.st_detail');
    Route::get('/memorandum/print-st/{id}', [\App\Http\Controllers\MemorandumController::class, 'print_st'])->name('memorandum.print_st');
    Route::get('/training', [\App\Http\Controllers\TrainingController::class, 'index'])->name('training.index');
    Route::get('/training/{id}/detail', [\App\Http\Controllers\TrainingController::class, 'detail'])->name('training.detail');
    Route::post('/training/{id}/report', [\App\Http\Controllers\TrainingController::class, 'submitReport'])->name('training.report');
});



// Pages modules

// Route::get('/login', function () {
//     return view('login');
// })->name('login');

Route::get('/register', function () {
    return view('register');
})->name('register');

Route::get('/forgot-password', function () {
    return view('forgot-password');
})->name('forgot-password');

Route::get('/reset-password', function () {
    return view('reset-password');
})->name('reset-password');

Route::get('/success', function () {
    return view('success');
})->name('success');

Route::get('/email-verification', function () {
    return view('email-verification');
})->name('email-verification');

Route::get('/two-step-verification', function () {
    return view('two-step-verification');
})->name('two-step-verification');

Route::get('/error-404', function () {
    return view('error-404');
})->name('error-404');

Route::get('/error-500', function () {
    return view('error-500');
})->name('error-500');

Route::get('/blank-page', function () {
    return view('blank-page');
})->name('blank-page');

Route::get('/coming-soon', function () {
    return view('coming-soon');
})->name('coming-soon');

Route::get('/under-maintenance', function () {
    return view('under-maintenance');
})->name('under-maintenance');



// Authentication Pages

Route::get('/login-cover', function () {
    return view('login-cover');
})->name('login-cover');

Route::get('/login-illustration', function () {
    return view('login-illustration');
})->name('login-illustration');

Route::get('/login-basic', function () {
    return view('login-basic');
})->name('login-basic');

// Route::get('/login', function () {
//     return view('login');
// })->name('login');

Route::get('/register-cover', function () {
    return view('register-cover');
})->name('register-cover');

Route::get('/register-illustration', function () {
    return view('register-illustration');
})->name('register-illustration');

Route::get('/register-basic', function () {
    return view('register-basic');
})->name('register-basic');

Route::get('/forgot-password-cover', function () {
    return view('forgot-password-cover');
})->name('forgot-password-cover');

Route::get('/forgot-password-illustration', function () {
    return view('forgot-password-illustration');
})->name('forgot-password-illustration');

Route::get('/forgot-password-basic', function () {
    return view('forgot-password-basic');
})->name('forgot-password-basic');

Route::get('/reset-password-cover', function () {
    return view('reset-password-cover');
})->name('reset-password-cover');

Route::get('/reset-password-illustration', function () {
    return view('reset-password-illustration');
})->name('reset-password-illustration');

Route::get('/reset-password-basic', function () {
    return view('reset-password-basic');
})->name('reset-password-basic');

Route::get('/email-verification-cover', function () {
    return view('email-verification-cover');
})->name('email-verification-cover');

Route::get('/email-verification-illustration', function () {
    return view('email-verification-illustration');
})->name('email-verification-illustration');

Route::get('/email-verification-basic', function () {
    return view('email-verification-basic');
})->name('email-verification-basic');

Route::get('/success-cover', function () {
    return view('success-cover');
})->name('success-cover');

Route::get('/success-illustration', function () {
    return view('success-illustration');
})->name('success-illustration');

Route::get('/success-basic', function () {
    return view('success-basic');
})->name('success-basic');

Route::get('/two-step-verification-cover', function () {
    return view('two-step-verification-cover');
})->name('two-step-verification-cover');

Route::get('/two-step-verification-illustration', function () {
    return view('two-step-verification-illustration');
})->name('two-step-verification-illustration');

Route::get('/two-step-verification-basic', function () {
    return view('two-step-verification-basic');
})->name('two-step-verification-basic');

Route::get('/lock-screen', function () {
    return view('lock-screen');
})->name('lock-screen');

Route::get('/error-404', function () {
    return view('error-404');
})->name('error-404');

Route::get('/error-500', function () {
    return view('error-500');
})->name('error-500');

Route::middleware('auth')->group(function () {
    // UI Charts

    Route::get('/chart-apex', function () {
        return view('chart-apex');
    })->name('chart-apex');

    Route::get('/chart-js', function () {
        return view('chart-js');
    })->name('chart-js');

    Route::get('/chart-morris', function () {
        return view('chart-morris');
    })->name('chart-morris');

    Route::get('/chart-flot', function () {
        return view('chart-flot');
    })->name('chart-flot');

    Route::get('/chart-peity', function () {
        return view('chart-peity');
    })->name('chart-peity');

    Route::get('/chart-c3', function () {
        return view('chart-c3');
    })->name('chart-c3');

    // UI - Forms

    Route::get('/form-basic-inputs', function () {
        return view('form-basic-inputs');
    })->name('form-basic-inputs');

    Route::get('/form-checkbox-radios', function () {
        return view('form-checkbox-radios');
    })->name('form-checkbox-radios');

    Route::get('/form-editors', function () {
        return view('form-editors');
    })->name('form-editors');

    Route::get('/form-fileupload', function () {
        return view('form-fileupload');
    })->name('form-fileupload');

    Route::get('/form-floating-labels', function () {
        return view('form-floating-labels');
    })->name('form-floating-labels');

    Route::get('/form-grid-gutters', function () {
        return view('form-grid-gutters');
    })->name('form-grid-gutters');

    Route::get('/form-horizontal', function () {
        return view('form-horizontal');
    })->name('form-horizontal');

    Route::get('/form-input-groups', function () {
        return view('form-input-groups');
    })->name('form-input-groups');

    Route::get('/form-mask', function () {
        return view('form-mask');
    })->name('form-mask');

    Route::get('/form-pickers', function () {
        return view('form-pickers');
    })->name('form-pickers');

    Route::get('/form-select', function () {
        return view('form-select');
    })->name('form-select');

    Route::get('/form-validation', function () {
        return view('form-validation');
    })->name('form-validation');

    Route::get('/form-vertical', function () {
        return view('form-vertical');
    })->name('form-vertical');

    Route::get('/form-wizard', function () {
        return view('form-wizard');
    })->name('form-wizard');

    // UI - Icons

    Route::get('/icon-bootstrap', function () {
        return view('icon-bootstrap');
    })->name('icon-bootstrap');

    Route::get('/icon-fontawesome', function () {
        return view('icon-fontawesome');
    })->name('icon-fontawesome');

    Route::get('/icon-feather', function () {
        return view('icon-feather');
    })->name('icon-feather');

    Route::get('/icon-ionic', function () {
        return view('icon-ionic');
    })->name('icon-ionic');

    Route::get('/icon-material', function () {
        return view('icon-material');
    })->name('icon-material');

    Route::get('/icon-pe7', function () {
        return view('icon-pe7');
    })->name('icon-pe7');

    Route::get('/icon-simpleline', function () {
        return view('icon-simpleline');
    })->name('icon-simpleline');

    Route::get('/icon-themify', function () {
        return view('icon-themify');
    })->name('icon-themify');

    Route::get('/icon-weather', function () {
        return view('icon-weather');
    })->name('icon-weather');

    Route::get('/icon-typicon', function () {
        return view('icon-typicon');
    })->name('icon-typicon');

    Route::get('/icon-flag', function () {
        return view('icon-flag');
    })->name('icon-flag');

    Route::get('/icon-remix', function () {
        return view('icon-remix');
    })->name('icon-remix');

    Route::get('/icon-tabler', function () {
        return view('icon-tabler');
    })->name('icon-tabler');

    // UI - Maps

    Route::get('/maps-leaflet', function () {
        return view('maps-leaflet');
    })->name('maps-leaflet');

    Route::get('/maps-vector', function () {
        return view('maps-vector');
    })->name('maps-vector');

    // UI - Tables

    Route::get('/tables-basic', function () {
        return view('tables-basic');
    })->name('tables-basic');

    Route::get('/data-tables', function () {
        return view('data-tables');
    })->name('data-tables');

    // UI - UI Components

    Route::get('/ui-accordion', function () {
        return view('ui-accordion');
    })->name('ui-accordion');

    Route::get('/ui-alerts', function () {
        return view('ui-alerts');
    })->name('ui-alerts');

    Route::get('/ui-avatar', function () {
        return view('ui-avatar');
    })->name('ui-avatar');

    Route::get('/ui-badges', function () {
        return view('ui-badges');
    })->name('ui-badges');

    Route::get('/ui-breadcrumb', function () {
        return view('ui-breadcrumb');
    })->name('ui-breadcrumb');

    Route::get('/ui-buttons-group', function () {
        return view('ui-buttons-group');
    })->name('ui-buttons-group');

    Route::get('/ui-buttons', function () {
        return view('ui-buttons');
    })->name('ui-buttons');

    Route::get('/ui-cards', function () {
        return view('ui-cards');
    })->name('ui-cards');

    Route::get('/ui-carousel', function () {
        return view('ui-carousel');
    })->name('ui-carousel');

    // UI - Clipboard, Collapse, Dragula, Dropdowns, Grid, Images, Lightbox, Links, List Group, Modals, Nav Tabs, Offcanvas, Pagination, Placeholders, Popovers, Progress, Rangeslider, Rating, Ratio, Scrollbar, Scrollspy, Spinner, Sweetalerts, Toasts, Tooltips, Typography, Utilities

    Route::get('/ui-clipboard', function () {
        return view('ui-clipboard');
    })->name('ui-clipboard');

    Route::get('/ui-collapse', function () {
        return view('ui-collapse');
    })->name('ui-collapse');

    Route::get('/ui-dragula', function () {
        return view('ui-dragula');
    })->name('ui-dragula');

    Route::get('/ui-dropdowns', function () {
        return view('ui-dropdowns');
    })->name('ui-dropdowns');

    Route::get('/ui-grid', function () {
        return view('ui-grid');
    })->name('ui-grid');

    Route::get('/ui-images', function () {
        return view('ui-images');
    })->name('ui-images');

    Route::get('/ui-lightbox', function () {
        return view('ui-lightbox');
    })->name('ui-lightbox');

    Route::get('/ui-links', function () {
        return view('ui-links');
    })->name('ui-links');

    Route::get('/ui-list-group', function () {
        return view('ui-list-group');
    })->name('ui-list-group');

    Route::get('/ui-modals', function () {
        return view('ui-modals');
    })->name('ui-modals');

    Route::get('/ui-nav-tabs', function () {
        return view('ui-nav-tabs');
    })->name('ui-nav-tabs');

    Route::get('/ui-offcanvas', function () {
        return view('ui-offcanvas');
    })->name('ui-offcanvas');

    Route::get('/ui-pagination', function () {
        return view('ui-pagination');
    })->name('ui-pagination');

    Route::get('/ui-placeholders', function () {
        return view('ui-placeholders');
    })->name('ui-placeholders');

    Route::get('/ui-popovers', function () {
        return view('ui-popovers');
    })->name('ui-popovers');

    Route::get('/ui-progress', function () {
        return view('ui-progress');
    })->name('ui-progress');

    Route::get('/ui-rangeslider', function () {
        return view('ui-rangeslider');
    })->name('ui-rangeslider');

    Route::get('/ui-rating', function () {
        return view('ui-rating');
    })->name('ui-rating');

    Route::get('/ui-ratio', function () {
        return view('ui-ratio');
    })->name('ui-ratio');

    Route::get('/ui-scrollbar', function () {
        return view('ui-scrollbar');
    })->name('ui-scrollbar');

    Route::get('/ui-scrollspy', function () {
        return view('ui-scrollspy');
    })->name('ui-scrollspy');

    Route::get('/ui-spinner', function () {
        return view('ui-spinner');
    })->name('ui-spinner');

    Route::get('/ui-sweetalerts', function () {
        return view('ui-sweetalerts');
    })->name('ui-sweetalerts');

    Route::get('/ui-toasts', function () {
        return view('ui-toasts');
    })->name('ui-toasts');

    Route::get('/ui-tooltips', function () {
        return view('ui-tooltips');
    })->name('ui-tooltips');

    Route::get('/ui-typography', function () {
        return view('ui-typography');
    })->name('ui-typography');

    Route::get('/ui-utilities', function () {
        return view('ui-utilities');
    })->name('ui-utilities');
});
