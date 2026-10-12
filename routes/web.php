<?php

use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\ApiTokenController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\ContractTemplateController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AssetTypeController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\CoveragePointController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FieldVisitReportController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\PackageChangeRequestController;
use App\Http\Controllers\PortalKaryawanController;
use App\Http\Controllers\PortalTeknisiController;
use App\Http\Controllers\PublicTicketController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\ResellerPortalController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ServicePackageController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketTitleController;
use App\Http\Controllers\TroubleshootingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WhatsAppTemplateController;
use App\Http\Controllers\WorkOrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check() && auth()->user()->hasRole('reseller')) {
        return redirect()->route('reseller-portal.index');
    }

    return redirect()->route('dashboard');
});

// Public, unauthenticated — link shared with customers to view ticket photos.
Route::get('/p/{token}', [PublicTicketController::class, 'show'])
    ->name('public.ticket')
    ->middleware('throttle:30,1');

Route::get('/storage-link', function () {
    $target = storage_path('app/public');
    $link = public_path('storage');
    $results = [];

    // 1. Pastikan folder target ada & siap
    if (!file_exists($target)) {
        @mkdir($target, 0775, true);
        $results[] = "Membuat folder target: {$target}";
    }

    // Pastikan subfolder penting siap
    $subfolders = ['employees/photos', 'attendances'];
    foreach ($subfolders as $sub) {
        $subPath = $target . '/' . $sub;
        if (!file_exists($subPath)) {
            @mkdir($subPath, 0775, true);
            $results[] = "Membuat subfolder: {$sub}";
        }
    }

    // 2. Bersihkan symlink lama jika broken
    if (is_link($link)) {
        @unlink($link);
        $results[] = "Menghapus symlink lama pada {$link}";
    }

    // 3. Eksekusi pembuatan symlink
    try {
        \Illuminate\Support\Facades\Artisan::call('storage:link');
        $artisanOutput = trim(\Illuminate\Support\Facades\Artisan::output());
        $results[] = "Artisan storage:link output: " . ($artisanOutput ?: 'Berhasil');
    } catch (\Throwable $e) {
        $results[] = "Artisan gagal: " . $e->getMessage();
        if (!file_exists($link) && function_exists('symlink')) {
            $symlinkResult = @symlink($target, $link);
            $results[] = $symlinkResult ? "Native symlink berhasil: {$link} -> {$target}" : "Native symlink gagal dibuat.";
        }
    }

    // 4. Verifikasi status
    $linkExists = is_link($link) || file_exists($link);
    $results[] = "Status public/storage: " . ($linkExists ? "TERHUBUNG (OK)" : "TIDAK TERHUBUNG (Gagal symlink)");
    $results[] = "Izin tulis target ({$target}): " . (is_writable($target) ? "BISA MENYIMPAN GAMBAR (Writable)" : "TIDAK BISA MENYIMPAN (Read-only, ubah permission storage ke 755/775!)");

    $outputHtml = "<div style='font-family:sans-serif; max-width:650px; margin:40px auto; padding:24px; border:1px solid #e2e8f0; border-radius:10px; background:#fff; box-shadow:0 4px 6px -1px rgba(0,0,0,0.1);'>";
    $outputHtml .= "<h2 style='margin-top:0; color:#1e293b;'>Status Storage & Upload Foto</h2>";
    $outputHtml .= "<ul style='line-height:1.8; color:#334155;'>";
    foreach ($results as $res) {
        $outputHtml .= "<li>" . e($res) . "</li>";
    }
    $outputHtml .= "</ul>";
    $outputHtml .= "<p style='margin-top:20px;'><a href='/portal-karyawan' style='display:inline-block; background:#4f46e5; color:#fff; text-decoration:none; padding:10px 20px; border-radius:6px; font-weight:600;'>Buka Portal Karyawan</a></p>";
    $outputHtml .= "</div>";

    return response($outputHtml);
});

// Fallback jika symlink diblokir oleh cPanel hosting agar gambar tetap tampil 100%
Route::get('/storage/{path}', function ($path) {
    $filePath = storage_path('app/public/' . $path);
    if (!file_exists($filePath)) {
        abort(404);
    }
    return response()->file($filePath);
})->where('path', '.*');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Second login step — reached only after username/password already checked out via
// LoginController, before the session is actually authenticated. Rate-limited since
// it's an unauthenticated endpoint that accepts a guessable-length code.
Route::middleware('throttle:10,1')->group(function () {
    Route::get('/two-factor-challenge', [TwoFactorController::class, 'challenge'])->name('two-factor.challenge');
    Route::post('/two-factor-challenge', [TwoFactorController::class, 'verifyChallenge'])->name('two-factor.verify-challenge');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/security/two-factor', [TwoFactorController::class, 'setup'])->name('two-factor.setup');
    Route::post('/security/two-factor', [TwoFactorController::class, 'confirm'])->name('two-factor.confirm');
    Route::delete('/security/two-factor', [TwoFactorController::class, 'disable'])->name('two-factor.disable');

    Route::get('/security/password', [ChangePasswordController::class, 'edit'])->name('password.edit');
    Route::put('/security/password', [ChangePasswordController::class, 'update'])->name('password.update');
});

Route::middleware(['auth', \App\Http\Middleware\EnsureTwoFactorIsEnabled::class])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::post('/users/{user}/disable', [UserController::class, 'disable'])->name('users.disable');
    Route::post('/users/{user}/enable', [UserController::class, 'enable'])->name('users.enable');

    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::post('/permissions', [RoleController::class, 'storePermission'])->name('permissions.store');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');

    Route::get('/audit', [AuditController::class, 'index'])->name('audit.index');

    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('/customers/import/template', [CustomerController::class, 'downloadTemplate'])->name('customers.import.template');
    Route::post('/customers/import', [CustomerController::class, 'importSpreadsheet'])->name('customers.import');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
    Route::post('/customers/{customer}/disable', [CustomerController::class, 'disable'])->name('customers.disable');
    Route::post('/customers/{customer}/enable', [CustomerController::class, 'enable'])->name('customers.enable');
    Route::post('/customers/{customer}/activate', [CustomerController::class, 'activate'])->name('customers.activate');
    Route::post('/customers/{customer}/ip-allocations', [CustomerController::class, 'storeIpAllocation'])->name('customers.ip-allocations.store');
    Route::put('/customers/{customer}/ip-allocations/{allocation}', [CustomerController::class, 'updateIpAllocation'])->name('customers.ip-allocations.update');
    Route::delete('/customers/{customer}/ip-allocations/{allocation}', [CustomerController::class, 'destroyIpAllocation'])->name('customers.ip-allocations.destroy');

    Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');
    Route::get('/teams/create', [TeamController::class, 'create'])->name('teams.create');
    Route::post('/teams', [TeamController::class, 'store'])->name('teams.store');
    Route::get('/teams/{team}/edit', [TeamController::class, 'edit'])->name('teams.edit');
    Route::put('/teams/{team}', [TeamController::class, 'update'])->name('teams.update');
    Route::post('/teams/{team}/disable', [TeamController::class, 'disable'])->name('teams.disable');
    Route::post('/teams/{team}/enable', [TeamController::class, 'enable'])->name('teams.enable');

    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
    Route::get('/tickets/{ticket}/edit', [TicketController::class, 'edit'])->name('tickets.edit');
    Route::put('/tickets/{ticket}', [TicketController::class, 'update'])->name('tickets.update');
    Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy'])->name('tickets.destroy');
    Route::post('/tickets/{ticket}/status', [TicketController::class, 'changeStatus'])->name('tickets.status');
    Route::post('/tickets/{ticket}/assign', [TicketController::class, 'assign'])->name('tickets.assign');
    Route::post('/tickets/{ticket}/assign-team', [TicketController::class, 'assignTeam'])->name('tickets.assign-team');
    Route::get('/tickets/{ticket}/whatsapp', [TicketController::class, 'shareWhatsapp'])->name('tickets.whatsapp');
    Route::post('/tickets/{ticket}/attachments', [TicketController::class, 'storeAttachment'])->name('tickets.attachments.store');
    Route::delete('/tickets/{ticket}/attachments/{attachment}', [TicketController::class, 'destroyAttachment'])->name('tickets.attachments.destroy');
    Route::post('/tickets/{ticket}/progress-updates', [TicketController::class, 'storeProgressUpdate'])->name('tickets.progress-updates.store');
    Route::delete('/tickets/{ticket}/progress-updates/{progressUpdate}', [TicketController::class, 'destroyProgressUpdate'])->name('tickets.progress-updates.destroy');
    Route::post('/tickets/{ticket}/whatsapp-messages', [TicketController::class, 'sendWhatsappMessage'])->name('tickets.whatsapp-messages.store');

    Route::post('/tickets/{ticket}/troubleshooting', [TroubleshootingController::class, 'store'])->name('troubleshooting.store');
    Route::post('/troubleshooting-sessions/{session}/steps', [TroubleshootingController::class, 'addStep'])->name('troubleshooting.steps.store');
    Route::post('/troubleshooting-sessions/{session}/close', [TroubleshootingController::class, 'close'])->name('troubleshooting.close');

    Route::get('/ticket-titles', [TicketTitleController::class, 'index'])->name('ticket-titles.index');
    Route::get('/ticket-titles/create', [TicketTitleController::class, 'create'])->name('ticket-titles.create');
    Route::post('/ticket-titles', [TicketTitleController::class, 'store'])->name('ticket-titles.store');
    Route::get('/ticket-titles/{ticketTitle}/edit', [TicketTitleController::class, 'edit'])->name('ticket-titles.edit');
    Route::put('/ticket-titles/{ticketTitle}', [TicketTitleController::class, 'update'])->name('ticket-titles.update');
    Route::delete('/ticket-titles/{ticketTitle}', [TicketTitleController::class, 'destroy'])->name('ticket-titles.destroy');
    Route::post('/ticket-titles/{ticketTitle}/toggle', [TicketTitleController::class, 'toggle'])->name('ticket-titles.toggle');

    Route::get('/whatsapp-templates', [WhatsAppTemplateController::class, 'index'])->name('whatsapp-templates.index');
    Route::get('/whatsapp-templates/{whatsAppTemplate}/edit', [WhatsAppTemplateController::class, 'edit'])->name('whatsapp-templates.edit');
    Route::put('/whatsapp-templates/{whatsAppTemplate}', [WhatsAppTemplateController::class, 'update'])->name('whatsapp-templates.update');

    Route::get('/coverage-points', [CoveragePointController::class, 'index'])->name('coverage-points.index');
    Route::get('/coverage-points/create', [CoveragePointController::class, 'create'])->name('coverage-points.create');
    Route::post('/coverage-points', [CoveragePointController::class, 'store'])->name('coverage-points.store');
    Route::get('/coverage-points/{coveragePoint}/edit', [CoveragePointController::class, 'edit'])->name('coverage-points.edit');
    Route::put('/coverage-points/{coveragePoint}', [CoveragePointController::class, 'update'])->name('coverage-points.update');
    Route::post('/coverage-points/{coveragePoint}/disable', [CoveragePointController::class, 'disable'])->name('coverage-points.disable');
    Route::post('/coverage-points/{coveragePoint}/enable', [CoveragePointController::class, 'enable'])->name('coverage-points.enable');

    Route::get('/service-packages', [ServicePackageController::class, 'index'])->name('service-packages.index');
    Route::get('/service-packages/create', [ServicePackageController::class, 'create'])->name('service-packages.create');
    Route::post('/service-packages', [ServicePackageController::class, 'store'])->name('service-packages.store');
    Route::get('/service-packages/{servicePackage}/edit', [ServicePackageController::class, 'edit'])->name('service-packages.edit');
    Route::put('/service-packages/{servicePackage}', [ServicePackageController::class, 'update'])->name('service-packages.update');
    Route::post('/service-packages/{servicePackage}/disable', [ServicePackageController::class, 'disable'])->name('service-packages.disable');
    Route::post('/service-packages/{servicePackage}/enable', [ServicePackageController::class, 'enable'])->name('service-packages.enable');

    Route::get('/package-changes', [PackageChangeRequestController::class, 'index'])->name('package-changes.index');
    Route::get('/package-changes/create', [PackageChangeRequestController::class, 'create'])->name('package-changes.create');
    Route::post('/package-changes', [PackageChangeRequestController::class, 'store'])->name('package-changes.store');
    Route::get('/package-changes/{packageChange}', [PackageChangeRequestController::class, 'show'])->name('package-changes.show');
    Route::post('/package-changes/{packageChange}/approve', [PackageChangeRequestController::class, 'approve'])->name('package-changes.approve');
    Route::post('/package-changes/{packageChange}/reject', [PackageChangeRequestController::class, 'reject'])->name('package-changes.reject');
    Route::post('/package-changes/{packageChange}/cancel', [PackageChangeRequestController::class, 'cancel'])->name('package-changes.cancel');

    Route::get('/quotations', [QuotationController::class, 'index'])->name('quotations.index');
    Route::get('/quotations/create', [QuotationController::class, 'create'])->name('quotations.create');
    Route::post('/quotations', [QuotationController::class, 'store'])->name('quotations.store');
    Route::get('/quotations/{quotation}', [QuotationController::class, 'show'])->name('quotations.show');
    Route::get('/quotations/{quotation}/edit', [QuotationController::class, 'edit'])->name('quotations.edit');
    Route::put('/quotations/{quotation}', [QuotationController::class, 'update'])->name('quotations.update');
    Route::get('/quotations/{quotation}/pdf', [QuotationController::class, 'pdf'])->name('quotations.pdf');
    Route::get('/quotations/{quotation}/close', [QuotationController::class, 'closeForm'])->name('quotations.close-form');
    Route::post('/quotations/{quotation}/close', [QuotationController::class, 'close'])->name('quotations.close');

    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
    Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
    Route::get('/invoices/generate-monthly', [InvoiceController::class, 'generateMonthlyForm'])->name('invoices.generate-monthly-form');
    Route::post('/invoices/generate-monthly', [InvoiceController::class, 'generateMonthly'])->name('invoices.generate-monthly');
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');
    Route::post('/invoices/{invoice}/mark-paid', [InvoiceController::class, 'markPaid'])->name('invoices.mark-paid');
    Route::post('/invoices/{invoice}/mark-unpaid', [InvoiceController::class, 'markUnpaid'])->name('invoices.mark-unpaid');

    Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

    Route::get('/api-tokens', [ApiTokenController::class, 'index'])->name('api-tokens.index');
    Route::post('/api-tokens', [ApiTokenController::class, 'store'])->name('api-tokens.store');
    Route::delete('/api-tokens/{token}', [ApiTokenController::class, 'destroy'])->name('api-tokens.destroy');

    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::get('/employees/{employee}', [EmployeeController::class, 'show'])->name('employees.show');
    Route::get('/employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
    Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::post('/employees/{employee}/disable', [EmployeeController::class, 'disable'])->name('employees.disable');
    Route::post('/employees/{employee}/enable', [EmployeeController::class, 'enable'])->name('employees.enable');
    Route::get('/employees/{employee}/contract-pdf', [EmployeeController::class, 'contractPdf'])->name('employees.contract-pdf');

    Route::get('/contract-templates', [ContractTemplateController::class, 'index'])->name('contract-templates.index');
    Route::get('/contract-templates/{type}/edit', [ContractTemplateController::class, 'edit'])->name('contract-templates.edit');
    Route::put('/contract-templates/{type}', [ContractTemplateController::class, 'update'])->name('contract-templates.update');
    Route::post('/contract-templates/{type}/preview', [ContractTemplateController::class, 'preview'])->name('contract-templates.preview');

    Route::get('/asset-types', [AssetTypeController::class, 'index'])->name('asset-types.index');
    Route::get('/asset-types/create', [AssetTypeController::class, 'create'])->name('asset-types.create');
    Route::post('/asset-types', [AssetTypeController::class, 'store'])->name('asset-types.store');
    Route::get('/asset-types/{assetType}/edit', [AssetTypeController::class, 'edit'])->name('asset-types.edit');
    Route::put('/asset-types/{assetType}', [AssetTypeController::class, 'update'])->name('asset-types.update');
    Route::post('/asset-types/{assetType}/disable', [AssetTypeController::class, 'disable'])->name('asset-types.disable');
    Route::post('/asset-types/{assetType}/enable', [AssetTypeController::class, 'enable'])->name('asset-types.enable');

    Route::get('/assets', [AssetController::class, 'index'])->name('assets.index');
    Route::get('/assets/create', [AssetController::class, 'create'])->name('assets.create');
    Route::post('/assets', [AssetController::class, 'store'])->name('assets.store');
    Route::get('/assets/{asset}', [AssetController::class, 'show'])->name('assets.show');
    Route::get('/assets/{asset}/edit', [AssetController::class, 'edit'])->name('assets.edit');
    Route::put('/assets/{asset}', [AssetController::class, 'update'])->name('assets.update');
    Route::post('/assets/{asset}/change-status', [AssetController::class, 'changeStatus'])->name('assets.change-status');

    Route::get('/work-orders', [WorkOrderController::class, 'index'])->name('work-orders.index');
    Route::get('/work-orders/create', [WorkOrderController::class, 'create'])->name('work-orders.create');
    Route::post('/work-orders', [WorkOrderController::class, 'store'])->name('work-orders.store');
    Route::get('/work-orders/{workOrder}', [WorkOrderController::class, 'show'])->name('work-orders.show');
    Route::get('/work-orders/{workOrder}/edit', [WorkOrderController::class, 'edit'])->name('work-orders.edit');
    Route::put('/work-orders/{workOrder}', [WorkOrderController::class, 'update'])->name('work-orders.update');
    Route::post('/work-orders/{workOrder}/change-status', [WorkOrderController::class, 'changeStatus'])->name('work-orders.change-status');

    Route::get('/portal-karyawan', [PortalKaryawanController::class, 'index'])->name('portal-karyawan.index');
    Route::get('/portal-teknisi', [PortalTeknisiController::class, 'index'])->name('portal-teknisi.index');

    Route::get('/absensi', [AttendanceController::class, 'self'])->name('attendance.self');
    Route::post('/absensi/check-in', [AttendanceController::class, 'checkIn'])->name('attendance.check-in');
    Route::post('/absensi/check-out', [AttendanceController::class, 'checkOut'])->name('attendance.check-out');
    Route::get('/absensi/laporan', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/absensi/laporan/{attendance}/edit', [AttendanceController::class, 'edit'])->name('attendance.edit');
    Route::put('/absensi/laporan/{attendance}', [AttendanceController::class, 'update'])->name('attendance.update');

    Route::get('/cuti-izin', [LeaveRequestController::class, 'self'])->name('leave-requests.self');
    Route::post('/cuti-izin', [LeaveRequestController::class, 'store'])->name('leave-requests.store');
    Route::post('/cuti-izin/{leaveRequest}/cancel', [LeaveRequestController::class, 'cancel'])->name('leave-requests.cancel');
    Route::get('/cuti-izin/laporan', [LeaveRequestController::class, 'index'])->name('leave-requests.index');
    Route::post('/cuti-izin/{leaveRequest}/approve', [LeaveRequestController::class, 'approve'])->name('leave-requests.approve');
    Route::post('/cuti-izin/{leaveRequest}/reject', [LeaveRequestController::class, 'reject'])->name('leave-requests.reject');

    Route::get('/field-visit-reports', [FieldVisitReportController::class, 'index'])->name('field-visit-reports.index');
    Route::get('/field-visit-reports/create', [FieldVisitReportController::class, 'create'])->name('field-visit-reports.create');
    Route::post('/field-visit-reports', [FieldVisitReportController::class, 'store'])->name('field-visit-reports.store');
    Route::get('/field-visit-reports/{fieldVisitReport}', [FieldVisitReportController::class, 'show'])->name('field-visit-reports.show');
    Route::get('/field-visit-reports/{fieldVisitReport}/edit', [FieldVisitReportController::class, 'edit'])->name('field-visit-reports.edit');
    Route::put('/field-visit-reports/{fieldVisitReport}', [FieldVisitReportController::class, 'update'])->name('field-visit-reports.update');
    Route::post('/field-visit-reports/{fieldVisitReport}/photos', [FieldVisitReportController::class, 'storePhoto'])->name('field-visit-reports.photos.store');
    Route::delete('/field-visit-reports/{fieldVisitReport}/photos/{photo}', [FieldVisitReportController::class, 'destroyPhoto'])->name('field-visit-reports.photos.destroy');

    Route::get('/reseller-portal', [ResellerPortalController::class, 'index'])->name('reseller-portal.index');
    Route::get('/reseller-portal/{customer}', [ResellerPortalController::class, 'show'])->name('reseller-portal.show');

    Route::prefix('resources')->name('database-resources.')->group(function () {
        Route::get('/', [\App\Http\Controllers\DatabaseResourceController::class, 'index'])->name('index');
        Route::post('/optimize', [\App\Http\Controllers\DatabaseResourceController::class, 'optimize'])->name('optimize');
        Route::post('/optimize-all', [\App\Http\Controllers\DatabaseResourceController::class, 'optimizeAll'])->name('optimize-all');
        Route::get('/stats', [\App\Http\Controllers\DatabaseResourceController::class, 'jsonStats'])->name('stats');
    });
});
