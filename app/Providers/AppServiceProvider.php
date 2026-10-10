<?php

namespace App\Providers;

use App\Contracts\WhatsAppProviderInterface;
use App\Models\Asset;
use App\Models\AssetType;
use App\Models\Attendance;
use App\Models\CoveragePoint;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\FieldVisitReport;
use App\Models\Invoice;
use App\Models\LeaveRequest;
use App\Models\PackageChangeRequest;
use App\Models\Quotation;
use App\Models\Role;
use App\Models\ServicePackage;
use App\Models\Team;
use App\Models\Ticket;
use App\Models\TicketTitle;
use App\Models\User;
use App\Models\WorkOrder;
use App\Policies\AssetPolicy;
use App\Policies\AssetTypePolicy;
use App\Policies\AttendancePolicy;
use App\Policies\CoveragePointPolicy;
use App\Policies\CustomerPolicy;
use App\Policies\EmployeePolicy;
use App\Policies\FieldVisitReportPolicy;
use App\Policies\InvoicePolicy;
use App\Policies\LeaveRequestPolicy;
use App\Policies\PackageChangeRequestPolicy;
use App\Policies\QuotationPolicy;
use App\Policies\RolePolicy;
use App\Policies\ServicePackagePolicy;
use App\Policies\TeamPolicy;
use App\Policies\TicketPolicy;
use App\Policies\TicketTitlePolicy;
use App\Policies\UserPolicy;
use App\Policies\WorkOrderPolicy;
use App\Services\WhatsAppProviders\FonnteProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Swap this binding to switch WhatsApp providers (e.g. a future WhatsAppCloudApiProvider)
        // without touching WhatsAppService or any controller.
        $this->app->bind(WhatsAppProviderInterface::class, FonnteProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(Ticket::class, TicketPolicy::class);
        Gate::policy(TicketTitle::class, TicketTitlePolicy::class);
        Gate::policy(Customer::class, CustomerPolicy::class);
        Gate::policy(Team::class, TeamPolicy::class);
        Gate::policy(CoveragePoint::class, CoveragePointPolicy::class);
        Gate::policy(ServicePackage::class, ServicePackagePolicy::class);
        Gate::policy(Quotation::class, QuotationPolicy::class);
        Gate::policy(Invoice::class, InvoicePolicy::class);
        Gate::policy(Employee::class, EmployeePolicy::class);
        Gate::policy(AssetType::class, AssetTypePolicy::class);
        Gate::policy(Asset::class, AssetPolicy::class);
        Gate::policy(WorkOrder::class, WorkOrderPolicy::class);
        Gate::policy(Attendance::class, AttendancePolicy::class);
        Gate::policy(LeaveRequest::class, LeaveRequestPolicy::class);
        Gate::policy(FieldVisitReport::class, FieldVisitReportPolicy::class);
        Gate::policy(PackageChangeRequest::class, PackageChangeRequestPolicy::class);

        // Abilities not tied to a specific Eloquent model.
        Gate::define('dashboard.view', fn (User $user) => $user->hasPermission('dashboard.view'));
        Gate::define('audit.view', fn (User $user) => $user->hasPermission('audit.view'));
        Gate::define('whatsapp.send', fn (User $user) => $user->hasPermission('whatsapp.send'));
        Gate::define('whatsapp.template.manage', fn (User $user) => $user->hasPermission('whatsapp.template.manage'));
        Gate::define('contract.template.manage', fn (User $user) => $user->hasPermission('contract.template.manage'));
        Gate::define('settings.manage', fn (User $user) => $user->hasPermission('settings.manage'));
        Gate::define('api.manage', fn (User $user) => $user->hasPermission('api.manage'));
        Gate::define('reseller.portal.view', fn (User $user) => $user->hasPermission('reseller.portal.view'));
        Gate::define('teknisi.portal.view', fn (User $user) => $user->hasPermission('teknisi.portal.view') || $user->hasRole('teknisi') || $user->hasRole('super-admin'));
        Gate::define('database.monitor', fn (User $user) => $user->hasPermission('database.monitor') || $user->hasRole('super-admin'));
    }
}
