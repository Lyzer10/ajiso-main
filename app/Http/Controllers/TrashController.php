<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Staff;
use App\Models\Dispute;
use App\Models\Beneficiary;
use Illuminate\Http\Request;

class TrashController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        // Get all trashed users count
        $users_count = User::onlyTrashed()->count();

        // Get all trashed users count
        $staff_count = Staff::onlyTrashed()->count();

        // Get all trashed beneficiaries and paginate them
        $beneficiaries_count = Beneficiary::onlyTrashed()->count();

         // Get all trashed staff and paginate them
        $disputes_count = Dispute::onlyTrashed()->count();

        // Bind all counts to the view
        return view('misc.trash', compact('users_count', 'staff_count', 'beneficiaries_count', 'disputes_count'));
    }

    /**
     * Display a listing of all trashed Users.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashedUsers()
    {
        // Get all trashed users and paginate them
        $users = User::with('role:id,role_name')
                        ->onlyTrashed()
                        ->select(
                            [
                                'id','user_no','first_name','middle_name',
                                'last_name','user_role_id','created_at','deleted_at'
                            ]
                        )
                        ->paginate(10);

        // Bind the users to the view
        return view('misc.users-trash', compact('users'));
    }

    /**
     * Display a listing of all trashed Staff.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashedStaff()
    {
        // Get all trashed staff and paginate them
        $staff = Staff::onlyTrashed()
                        ->select(['id','user_id','office','created_at','deleted_at'])
                        ->paginate(10);

        // Bind the users to the view
        return view('misc.staff-trash', compact('staff'));
    }

    /**
     * Display a listing of all Beneficiaries.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashedBeneficiaries()
    {
        // Get all trashed beneficiaries and paginate them
        $beneficiaries = Beneficiary::onlyTrashed()
                                    ->select(['id','user_id','address','created_at','deleted_at'])
                                    ->paginate(10);

        // Bind the users to the view
        return view('misc.beneficiaries-trash', compact('beneficiaries'));
    }

    /**
     * Display a listing of all Disputes.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashedDisputes()
    {
        // Get all trashed staff and paginate them
        $disputes = Dispute::onlyTrashed()
                            ->select(['id', 'dispute_no','beneficiary_id', 'created_at', 'deleted_at'])
                            ->paginate(10);

        // Bind the users to the view
        return view('misc.disputes-trash', compact('disputes'));
    }

}
