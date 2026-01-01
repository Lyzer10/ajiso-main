<?php

namespace App\Traits;

use App\Models\User;

trait FetchAdmins
{
    public function getAdmins()
    {

        // Get users who are admins
        $admins = User::whereHas('role', function ($query) {
                                $query->where('id', 2);
                            }
                        )
                        ->get();

        return  $admins;
    }
    public function getSuperAdmins()
    {

        // Get users who are super admins
        $admins = User::whereHas('role', function ($query) {
                                $query->where('id', 1);
                            }
                        )
                        ->get();

        return  $admins;
    }
}

?>
