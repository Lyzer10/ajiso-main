<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Navigation Access Gates

        /* define a admin user role */

        Gate::define('isSuperAdmin', function(User $user) {

            return $user->role->role_abbreviation == 'superadmin';

        });

        /* define a admin user role */

        Gate::define('isAdmin', function(User $user) {

            return $user->role->role_abbreviation == 'admin';

        });

        /* define a staff role */

        Gate::define('isStaff', function(User $user) {

            return $user->role->role_abbreviation == 'staff';

        });

        /* define a beneficiary role */

        Gate::define('isBeneficiary', function(User $user) {

            return $user->role->role_abbreviation == 'beneficiary';

        });

        /* define a beneficiary role */

        Gate::define('isClerk', function(User $user) {

            return $user->role->role_abbreviation == 'clerk';

        });
    }
}
