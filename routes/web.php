<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::redirect('/', app()->getLocale(), 301);

/**
 * Language group
 */
Route::group([
    'prefix' => '{locale?}',
    'where' => ['locale' => '[a-zA-Z]{2}'],
    'middleware' => ['setlocale']
], function () {

    // Landing Module

    // landing
    Route::get('/', function () {
        return view('index');
    })->name('home');

    // Dashboards

    // Super Admin
    Route::get('/admin/super/home', [App\Http\Controllers\HomeController::class, 'index'])
        ->name('admin.super.home')->middleware('superadmin');

    // Admin
    Route::get('/admin/home', [App\Http\Controllers\HomeController::class, 'index'])
        ->name('admin.home')->middleware('admin');

    //Staff
    Route::get('/staff/home', [App\Http\Controllers\HomeController::class, 'staff'])->name('staff.home')->middleware('staff');

    //Beneficiary
    Route::get('/beneficiary/home', [App\Http\Controllers\HomeController::class, 'beneficiary'])->name('beneficiary.home')->middleware('beneficiary');

    // Clerk
    Route::get('/clerk/home', [App\Http\Controllers\HomeController::class, 'index'])->name('clerk.home')->middleware('clerk');

    // Login
    Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'index'])->name('login');
    Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'store']);

    // Logout
    Route::post('/logout', [App\Http\Controllers\Auth\LogoutController::class, 'index'])->name('logout');

    // Users Module
    Route::get('/users', [App\Http\Controllers\UserController::class, 'index'])->name('users.list');
    Route::get('/users/create', [App\Http\Controllers\UserController::class, 'create'])->name('user.create');
    Route::post('/users', [App\Http\Controllers\UserController::class, 'store'])->name('user.store');
    Route::put('/users/{user}/password', [App\Http\Controllers\UserController::class, 'updatePassword'])->name('user.update.password');
    Route::get('/users/{id}/profile', [App\Http\Controllers\UserController::class, 'show'])->name('user.show');
    Route::get('/users/{id}/edit', [App\Http\Controllers\UserController::class, 'edit'])->name('user.edit');
    Route::put('/users/{user}', [App\Http\Controllers\UserController::class, 'update'])->name('user.update');
    Route::put('/users/{user}/update', [App\Http\Controllers\UserController::class, 'updateProfile'])->name('user.update.profile');
    Route::put('/users/{user}/photo', [App\Http\Controllers\UserController::class, 'updatePhoto'])->name('user.update.photo');
    Route::put('/users/{user}/trash', [App\Http\Controllers\UserController::class, 'trash'])->name('user.trash');
    Route::put('/users/{user}/restore', [App\Http\Controllers\UserController::class, 'restore'])->name('user.restore');
    Route::delete('/users/{user}', [App\Http\Controllers\UserController::class, 'destroy'])->name('user.delete');

    // Staff Module
    Route::get('/staff', [App\Http\Controllers\StaffController::class, 'index'])->name('staff.list');
    Route::get('/staff/create', [App\Http\Controllers\StaffController::class, 'create'])->name('staff.create');
    Route::post('/staff', [App\Http\Controllers\StaffController::class, 'store'])->name('staff.store');
    Route::get('/staff/{staff}/profile', [App\Http\Controllers\StaffController::class, 'show'])->name('staff.show');
    Route::get('/staff/{staff}/access', [App\Http\Controllers\StaffController::class, 'access'])->name('staff.access');
    Route::get('/staff/{staff}/edit', [App\Http\Controllers\StaffController::class, 'edit'])->name('staff.edit');
    Route::put('/staff/{staff}', [App\Http\Controllers\StaffController::class, 'update'])->name('staff.update');
    Route::put('/staff/{staff}/trash', [App\Http\Controllers\StaffController::class, 'trash'])->name('staff.trash');
    Route::put('/staff/{staff}/restore', [App\Http\Controllers\StaffController::class, 'restore'])->name('staff.restore');
    Route::delete('/staff/{staff}', [App\Http\Controllers\StaffController::class, 'destroy'])->name('staff.delete');

    // Beneficiary Module
    Route::get('/beneficiaries', [App\Http\Controllers\BeneficiaryController::class, 'index'])->name('beneficiaries.list');
    Route::get('/beneficiaries/create', [App\Http\Controllers\BeneficiaryController::class, 'create'])->name('beneficiary.create');
    Route::post('/beneficiaries', [App\Http\Controllers\BeneficiaryController::class, 'store'])->name('beneficiary.store');
    Route::get('/beneficiaries/{beneficiary}/profile', [App\Http\Controllers\BeneficiaryController::class, 'show'])->name('beneficiary.show');
    Route::get('/beneficiaries/{beneficiary}/access', [App\Http\Controllers\BeneficiaryController::class, 'access'])->name('beneficiary.access');
    Route::get('/beneficiaries/{beneficiary}/edit', [App\Http\Controllers\BeneficiaryController::class, 'edit'])->name('beneficiary.edit');
    Route::put('/beneficiaries/{beneficiary}', [App\Http\Controllers\BeneficiaryController::class, 'update'])->name('beneficiary.update');
    Route::put('/beneficiaries/{beneficiary}/trash', [App\Http\Controllers\BeneficiaryController::class, 'trash'])->name('beneficiary.trash');
    Route::put('/beneficiaries/{beneficiary}/restore', [App\Http\Controllers\BeneficiaryController::class, 'restore'])->name('beneficiary.restore');
    Route::delete('/beneficiaries/{beneficiary}', [App\Http\Controllers\BeneficiaryController::class, 'destroy'])->name('beneficiary.delete');

    // Disputes Module
    Route::get('/disputes', [App\Http\Controllers\DisputeController::class, 'index'])->name('disputes.list');
    Route::get('/disputes/staff/{staff}/list', [App\Http\Controllers\DisputeController::class, 'myList'])->name('disputes.my.list');
    Route::get('/disputes/create', [App\Http\Controllers\DisputeController::class, 'create'])->name('dispute.create.new');
    Route::post('/disputes', [App\Http\Controllers\DisputeController::class, 'store'])->name('dispute.store');
    Route::get('/disputes/create/{dispute}', [App\Http\Controllers\DisputeController::class, 'createArchived'])->name('dispute.create.archive');
    Route::post('/disputes/archive', [App\Http\Controllers\DisputeController::class, 'storeArchived'])->name('dispute.store.archive');
    Route::get('/disputes/{dispute}/profile', [App\Http\Controllers\DisputeController::class, 'show'])->name('dispute.show');
    Route::get('/disputes/archive/select', [App\Http\Controllers\DisputeController::class, 'selectArchived'])->name('dispute.select.archive');
    Route::get('/disputes/archive/search', [App\Http\Controllers\DisputeController::class, 'searchArchived'])->name('dispute.search.archive');
    Route::get('/disputes/archive/search/live', [App\Http\Controllers\DisputeController::class, 'liveSearchArchived'])->name('dispute.search.live');
    Route::get('/disputes/{dispute}/assign', [App\Http\Controllers\DisputeController::class, 'assign'])->name('dispute.assign');
    Route::patch('/disputes/assign', [App\Http\Controllers\DisputeController::class, 'bindDispute'])->name('dispute.assign.bind');
    Route::get('/disputes/{dispute}/edit', [App\Http\Controllers\DisputeController::class, 'edit'])->name('dispute.edit');
    Route::put('/disputes/{dispute}', [App\Http\Controllers\DisputeController::class, 'update'])->name('dispute.update');
    Route::put('/disputes/{dispute}/trash', [App\Http\Controllers\DisputeController::class, 'trash'])->name('dispute.trash');
    Route::put('/disputes/{dispute}/restore', [App\Http\Controllers\DisputeController::class, 'restore'])->name('dispute.restore');
    Route::delete('/disputes/{dispute}', [App\Http\Controllers\DisputeController::class, 'destroy'])->name('dispute.delete');

    // Disputes Activity Module
    Route::get('/disputes/activity', [App\Http\Controllers\DisputeActivityController::class, 'index'])->name('disputes.activity.list');
    Route::get('/disputes/activity/create', [App\Http\Controllers\DisputeActivityController::class, 'create'])->name('dispute.activity.create');
    Route::post('/disputes/activity', [App\Http\Controllers\DisputeActivityController::class, 'store'])->name('dispute.activity.store');
    Route::post('/disputes/activity/notification', [App\Http\Controllers\DisputeActivityController::class, 'sendNotification'])->name('dispute.activity.notification');
    Route::post('/disputes/activity/clinic', [App\Http\Controllers\DisputeActivityController::class, 'clinicVisits'])->name('dispute.activity.clinic');
    Route::put('/disputes/activity/status', [App\Http\Controllers\DisputeActivityController::class, 'changeStatus'])->name('dispute.activity.status');
    Route::post('/disputes/activity/remarks', [App\Http\Controllers\DisputeActivityController::class, 'providerRemarks'])->name('dispute.activity.remarks');
    Route::post('/disputes/activity/attachments', [App\Http\Controllers\DisputeActivityController::class, 'addAttachment'])->name('dispute.activity.attachment');
    Route::get('/disputes/activity/attachments/{attachment}/view', [App\Http\Controllers\DisputeActivityController::class, 'viewAttachment'])->name('dispute.activity.attachment.view');
    Route::get('/disputes/activity/attachments/{attachment}/download', [App\Http\Controllers\DisputeActivityController::class, 'downloadAttachment'])->name('dispute.activity.attachment.download');
    Route::delete('/disputes/activity/attachments/{attachment}', [App\Http\Controllers\DisputeActivityController::class, 'deleteAttachment'])->name('dispute.activity.attachment.delete');
    Route::put('/disputes/activity/{dispute}', [App\Http\Controllers\DisputeActivityController::class, 'update'])->name('dispute.activity.update');

    // Disputes Assignment Requests Module
    Route::get('/disputes/request', [App\Http\Controllers\AssignmentRequestController::class, 'index'])->name('disputes.request.list');
    Route::get('/disputes/request/staff/{staff}', [App\Http\Controllers\AssignmentRequestController::class, 'myList'])->name('disputes.request.my-list');
    Route::get('/disputes/request/{dispute}/create', [App\Http\Controllers\AssignmentRequestController::class, 'create'])->name('dispute.request.create');
    Route::post('/disputes/request', [App\Http\Controllers\AssignmentRequestController::class, 'store'])->name('dispute.request.store');
    Route::put('/disputes/request/accept/{req}', [App\Http\Controllers\AssignmentRequestController::class, 'acceptRequest'])->name('dispute.request.accept');
    Route::put('/disputes/request/reject/{req}', [App\Http\Controllers\AssignmentRequestController::class, 'rejectRequest'])->name('dispute.request.reject');

    // Dispute Clinic sheets
    Route::get('/disputes/activity/clinic/{sheet}/show', [App\Http\Controllers\DisputeActivityController::class, 'clinicSheet'])->name('disputes.activity.sheet');

    // Reports

    // Admin

    // General Previews
    Route::get('/reports/general', [App\Http\Controllers\ReportController::class, 'index'])->name('reports.general');
    Route::get('/reports/general/filter', [App\Http\Controllers\ReportController::class, 'filter'])->name('reports.general.filter');

    // Summaries
    Route::get('/reports/summaries/disputes', [App\Http\Controllers\ReportController::class, 'disputesSummary'])->name('reports.summary.dispute');
    Route::get('/reports/summaries/beneficiaries', [App\Http\Controllers\ReportController::class, 'beneficiariesEnrollSummary'])->name('reports.summary.enrollment');
    Route::get('/reports/summaries/survey', [App\Http\Controllers\ReportController::class, 'surveySummary'])->name('reports.summary.survey');

    // Summaries filter
    Route::get('/reports/summaries/disputes/filter', [App\Http\Controllers\ReportController::class, 'disputesSummaryFilter'])->name('summaries.disputes.filter');
    Route::get('/reports/summaries/beneficiaries/filter', [App\Http\Controllers\ReportController::class, 'beneficiariesEnrollSummaryFilter'])->name('summaries.beneficiaries.filter');
    Route::get('/reports/summaries/survey/filter', [App\Http\Controllers\ReportController::class, 'surveySummaryFilter'])->name('summaries.survey.filter');

    // Exports
    Route::post('/reports/general/export/pdf', [App\Http\Controllers\ExportController::class, 'exportPdf'])->name('reports.export.pdf');
    Route::post('/reports/general/export/csv', [App\Http\Controllers\ExportController::class, 'exportCsv'])->name('reports.export.csv');
    Route::post('/reports/general/export/excel', [App\Http\Controllers\ExportController::class, 'exportExcel'])->name('reports.export.excel');

    // Staff

    // General Previews
    Route::get('/reports/general/staff', [App\Http\Controllers\StaffReportController::class, 'index'])->name('reports.general.staff');
    Route::get('/reports/general/staff/filter', [App\Http\Controllers\StaffReportController::class, 'filter'])->name('reports.general.filter.staff');

    // Summaries
    Route::get('/reports/summaries/staff/disputes', [App\Http\Controllers\StaffReportController::class, 'disputesSummary'])->name('reports.summary.dispute.staff');

    // Summaries filter
    Route::get('/reports/summaries/staff/disputes/filter', [App\Http\Controllers\StaffReportController::class, 'disputesSummaryFilter'])->name('summaries.disputes.filter.staff');

    // Exports
    Route::post('/reports/general/staff/export/pdf', [App\Http\Controllers\StaffExportController::class, 'exportPdf'])->name('reports.export.pdf.staff');
    Route::post('/reports/general/staff/export/csv', [App\Http\Controllers\StaffExportController::class, 'exportCsv'])->name('reports.export.csv.staff');
    Route::post('/reports/general/staff/export/excel', [App\Http\Controllers\StaffExportController::class, 'exportExcel'])->name('reports.export.excel.staff');

    // Settings

    // Settings -> Preferences Module
    Route::get('/preferences', [App\Http\Controllers\PreferenceController::class, 'index'])->name('preferences.list');
    Route::get('/preferences/create', [App\Http\Controllers\PreferenceController::class, 'create'])->name('preference.create');
    Route::post('/preferences', [App\Http\Controllers\PreferenceController::class, 'store'])->name('preference.store');
    Route::get('/preferences/edit/{preference}', [App\Http\Controllers\PreferenceController::class, 'edit'])->name('preference.edit');
    Route::put('/preferences{preference}', [App\Http\Controllers\PreferenceController::class, 'update'])->name('preference.update');


    // Manager -> Designations Module
    Route::get('/manager/designations', [App\Http\Controllers\DesignationController::class, 'index'])->name('manager.designations.list');
    Route::post('/manager/designations', [App\Http\Controllers\DesignationController::class, 'store'])->name('manager.designation.store');
    Route::put('/manager/designations/{designation}', [App\Http\Controllers\DesignationController::class, 'update'])->name('manager.designation.update');
    Route::put('/manager/designations/{designation}/trash', [App\Http\Controllers\DesignationController::class, 'trash'])->name('manager.designation.trash');

    // Manager -> Regions Module
    Route::get('/manager/regions', [App\Http\Controllers\RegionController::class, 'index'])->name('manager.regions.list');
    Route::post('/manager/regions', [App\Http\Controllers\RegionController::class, 'store'])->name('manager.region.store');
    Route::get('/manager/regions/{id}/districts', [App\Http\Controllers\RegionController::class, 'getDistricts'])->name('manager.region.districts');
    Route::put('/manager/regions/{region}', [App\Http\Controllers\RegionController::class, 'update'])->name('manager.region.update');
    Route::put('/manager/regions/{region}/trash', [App\Http\Controllers\RegionController::class, 'trash'])->name('manager.region.trash');

    // Manager -> Districts Module
    Route::get('/districts', [App\Http\Controllers\DistrictController::class, 'index'])->name('manager.districts.list');
    Route::post('/districts', [App\Http\Controllers\DistrictController::class, 'store'])->name('manager.district.store');
    Route::put('/districts/{district}', [App\Http\Controllers\DistrictController::class, 'update'])->name('manager.district.update');
    Route::put('/districts/{district}/trash', [App\Http\Controllers\DistrictController::class, 'trash'])->name('manager.district.trash');

    // Manager -> Religions Module
    Route::get('/manager/religions', [App\Http\Controllers\ReligionController::class, 'index'])->name('manager.religions.list');
    Route::post('/manager/religions', [App\Http\Controllers\ReligionController::class, 'store'])->name('manager.religion.store');
    Route::put('/manager/religions/{religion}', [App\Http\Controllers\ReligionController::class, 'update'])->name('manager.religion.update');
    Route::put('/manager/religions/{religion}/trash', [App\Http\Controllers\ReligionController::class, 'trash'])->name('manager.religion.trash');

    // Manager -> Tribes Module
    Route::get('/manager/tribes', [App\Http\Controllers\TribeController::class, 'index'])->name('manager.tribes.list');
    Route::post('/manager/tribes', [App\Http\Controllers\TribeController::class, 'store'])->name('manager.tribe.store');
    Route::put('/manager/tribes/{tribe}', [App\Http\Controllers\TribeController::class, 'update'])->name('manager.tribe.update');
    Route::put('/manager/tribes/{tribe}/trash', [App\Http\Controllers\TribeController::class, 'trash'])->name('manager.tribe.trash');

    // Manager -> Metrics Module
    Route::get('/manager/metrics', [App\Http\Controllers\MetricController::class, 'index'])->name('manager.metrics.list');
    Route::post('/manager/metrics', [App\Http\Controllers\MetricController::class, 'store'])->name('manager.metric.store');
    Route::put('/manager/metrics/{metric}', [App\Http\Controllers\MetricController::class, 'update'])->name('manager.metric.update');
    Route::put('/manager/metrics/{metric}/trash', [App\Http\Controllers\MetricController::class, 'trash'])->name('manager.metric.trash');

    // Manager -> Metric Measures Module
    Route::get('/manager/metrics/measures', [App\Http\Controllers\MetricMeasureController::class, 'index'])->name('manager.metrics.measures.list');
    Route::post('/manager/metrics/measures', [App\Http\Controllers\MetricMeasureController::class, 'store'])->name('manager.metric.measure.store');
    Route::put('/manager/metrics/measures/{measure}', [App\Http\Controllers\MetricMeasureController::class, 'update'])->name('manager.metric.measure.update');
    Route::put('/manager/metrics/measures/{measure}/trash', [App\Http\Controllers\MetricMeasureController::class, 'trash'])->name('manager.metric.measure.trash');

    // Manager -> Age Groups Module
    Route::get('/manager/age', [App\Http\Controllers\AgeGroupController::class, 'index'])->name('manager.age.list');
    Route::post('/manager/age', [App\Http\Controllers\AgeGroupController::class, 'store'])->name('manager.age.store');
    Route::put('/manager/age/{age}', [App\Http\Controllers\AgeGroupController::class, 'update'])->name('manager.age.update');
    Route::put('/manager/age/{age}/trash', [App\Http\Controllers\AgeGroupController::class, 'trash'])->name('manager.age.trash');

    // Manager -> Income Group Module
    Route::get('/manager/incomes', [App\Http\Controllers\IncomeController::class, 'index'])->name('manager.incomes.list');
    Route::post('/manager/incomes', [App\Http\Controllers\IncomeController::class, 'store'])->name('manager.income.store');
    Route::put('/manager/incomes/{income}', [App\Http\Controllers\IncomeController::class, 'update'])->name('manager.income.update');
    Route::put('/manager/incomes/{income}/trash', [App\Http\Controllers\IncomeController::class, 'trash'])->name('manager.income.trash');

    // Manager -> Survey Choices Module
    Route::get('/manager/survey/choices', [App\Http\Controllers\SurveyChoiceController::class, 'index'])->name('manager.survey.choices.list');
    Route::post('/manager/survey/choices', [App\Http\Controllers\SurveyChoiceController::class, 'store'])->name('manager.survey.choice.store');
    Route::put('/manager/survey/choices/{choice}', [App\Http\Controllers\SurveyChoiceController::class, 'update'])->name('manager.survey.choice.update');
    Route::put('/manager/survey/choices/{choice}/trash', [App\Http\Controllers\SurveyChoiceController::class, 'trash'])->name('manager.survey.choice.trash');

    // Manager -> Types of Services Module
    Route::get('/manager/services/types', [App\Http\Controllers\TypeOfServiceController::class, 'index'])->name('manager.services.types.list');
    Route::post('/manager/services/types', [App\Http\Controllers\TypeOfServiceController::class, 'store'])->name('manager.services.type.store');
    Route::put('/manager/services/types/{id}', [App\Http\Controllers\TypeOfServiceController::class, 'update'])->name('manager.services.type.update');
    Route::put('/manager/services/types/{id}/trash', [App\Http\Controllers\TypeOfServiceController::class, 'trash'])->name('manager.services.type.trash');

    // Manager -> Types of Cases Module
    Route::get('/manager/disputes/types', [App\Http\Controllers\TypeOfCaseController::class, 'index'])->name('manager.disputes.types.list');
    Route::post('/manager/disputes/types', [App\Http\Controllers\TypeOfCaseController::class, 'store'])->name('manager.disputes.type.store');
    Route::put('/manager/disputes/types/{type}', [App\Http\Controllers\TypeOfCaseController::class, 'update'])->name('manager.disputes.type.update');
    Route::put('/manager/disputes/types/{type}/trash', [App\Http\Controllers\TypeOfCaseController::class, 'trash'])->name('manager.disputes.type.trash');

    // Manager -> Marital Statuses Module
    Route::get('/manager/marital/statuses', [App\Http\Controllers\MaritalStatusController::class, 'index'])->name('manager.marital.statuses.list');
    Route::post('/manager/marital/statuses', [App\Http\Controllers\MaritalStatusController::class, 'store'])->name('manager.marital.status.store');
    Route::put('/manager/marital/statuses/{status}', [App\Http\Controllers\MaritalStatusController::class, 'update'])->name('manager.marital.status.update');
    Route::put('/manager/marital/statuses/{status}/trash', [App\Http\Controllers\MaritalStatusController::class, 'trash'])->name('manager.marital.status.trash');

    // Manager -> Marriage Forms Module
    Route::get('/manager/marital/forms', [App\Http\Controllers\MarriageFormController::class, 'index'])->name('manager.marital.forms.list');
    Route::post('/manager/marital/forms', [App\Http\Controllers\MarriageFormController::class, 'store'])->name('manager.marital.form.store');
    Route::put('/manager/marital/forms/{form}', [App\Http\Controllers\MarriageFormController::class, 'update'])->name('manager.marital.form.update');
    Route::put('/manager/marital/forms/{form}/trash', [App\Http\Controllers\MarriageFormController::class, 'trash'])->name('manager.marital.form.trash');

    // Manager -> Education Levels Module
    Route::get('/manager/education/levels', [App\Http\Controllers\EducationLevelController::class, 'index'])->name('manager.education.levels.list');
    Route::post('/manager/education/levels', [App\Http\Controllers\EducationLevelController::class, 'store'])->name('manager.education.level.store');
    Route::put('/manager/education/levels/{level}', [App\Http\Controllers\EducationLevelController::class, 'update'])->name('manager.education.level.update');
    Route::put('/manager/education/levels/{level}/trash', [App\Http\Controllers\EducationLevelController::class, 'trash'])->name('manager.education.level.trash');

    // Manager -> Dispute Statuses Module
    Route::get('/manager/disputes/statuses', [App\Http\Controllers\DisputeStatusController::class, 'index'])->name('manager.disputes.statuses.list');
    Route::post('/manager/disputes/statuses', [App\Http\Controllers\DisputeStatusController::class, 'store'])->name('manager.disputes.status.store');
    Route::put('/manager/disputes/statuses/{status}', [App\Http\Controllers\DisputeStatusController::class, 'update'])->name('manager.disputes.status.update');
    Route::put('/manager/disputes/statuses/{status}/trash', [App\Http\Controllers\DisputeStatusController::class, 'trash'])->name('manager.disputes.status.trash');

    // Manager -> Employment Statuses Module
    Route::get('/manager/employment/statuses', [App\Http\Controllers\EmploymentStatusController::class, 'index'])->name('manager.employment.statuses.list');
    Route::post('/manager/employments/statuses', [App\Http\Controllers\EmploymentStatusController::class, 'store'])->name('manager.employment.status.store');
    Route::put('/manager/employments/statuses/{status}', [App\Http\Controllers\EmploymentStatusController::class, 'update'])->name('manager.employment.status.update');
    Route::put('/manager/employments/statuses/{status}/trash', [App\Http\Controllers\EmploymentStatusController::class, 'trash'])->name('manager.employment.status.trash');


    // Notifications
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.list');
    Route::get('/notifications/create', [App\Http\Controllers\NotificationController::class, 'create'])->name('notification.create');
    Route::post('/notifications', [App\Http\Controllers\NotificationController::class, 'store'])->name('notification.store');
    Route::post('/notifications/mark', [App\Http\Controllers\NotificationController::class, 'markNotification'])->name('notification.mark');
    Route::post('/notifications/delete', [App\Http\Controllers\NotificationController::class, 'destroy'])->name('notification.delete');

    // System
    // Logs
    Route::get('/system/logs', [App\Http\Controllers\MiscController::class, 'logs'])->name('system.logs');
    Route::get('/system/logs/clean', [App\Http\Controllers\MiscController::class, 'cleanLogs'])->name('system.logs.clean');

    // Backup
    Route::get('/system/backup', [App\Http\Controllers\MiscController::class, 'backup'])->name('system.backup');
    Route::post('/system/backup/now', [App\Http\Controllers\MiscController::class, 'backupNow'])->name('system.backup.now');

    // Trash
    Route::get('/system/trash', [App\Http\Controllers\TrashController::class, 'index'])->name('system.trash');

    Route::get('/system/trash/users', [App\Http\Controllers\TrashController::class, 'trashedUsers'])->name('system.trash.users');
    Route::get('/system/trash/staff', [App\Http\Controllers\TrashController::class, 'trashedStaff'])->name('system.trash.staff');
    Route::get('/system/trash/beneficiaries', [App\Http\Controllers\TrashController::class, 'trashedBeneficiaries'])->name('system.trash.beneficiaries');
    Route::get('/system/trash/disputes', [App\Http\Controllers\TrashController::class, 'trashedDisputes'])->name('system.trash.disputes');

    // Settings Manager
    Route::get('/settings/manager', [App\Http\Controllers\MiscController::class, 'settings'])->name('settings.manager');

    // Register routes
    Auth::routes();
});
