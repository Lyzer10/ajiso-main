<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Organization;
use App\Models\Region;
use App\Models\User;
use Illuminate\Http\Request;

class OrganizationController extends Controller
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
        $query = Organization::with(['region:id,region', 'district:id,district'])
            ->latest();

        if ($name = request('name')) {
            $query->where('name', 'like', '%'.$name.'%');
        }

        if ($regionId = request('region_id')) {
            $query->where('region_id', (int) $regionId);
        }

        if ($districtId = request('district_id')) {
            $query->where('district_id', (int) $districtId);
        }

        if ($ward = request('ward')) {
            $query->where('ward', 'like', '%'.$ward.'%');
        }

        $organizations = $query->paginate(10);

        $regions = Region::get(['id', 'region']);
        $districtsAll = District::get(['id', 'district', 'region_id']);
        $districts = District::when(request('region_id'), function ($districtQuery, $regionId) {
            return $districtQuery->where('region_id', (int) $regionId);
        })->get(['id', 'district', 'region_id']);

        $wardsQuery = Organization::query()
            ->whereNotNull('ward')
            ->where('ward', '!=', '');

        if ($districtId = request('district_id')) {
            $wardsQuery->where('district_id', (int) $districtId);
        }

        $wards = $wardsQuery->select('ward')->distinct()->orderBy('ward')->get();

        return view('manager.organizations', compact('organizations', 'regions', 'districts', 'districtsAll', 'wards'));
    }

    /**
     * Display the specified organization.
     *
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Http\Response
     */
    public function show($locale, Organization $organization)
    {
        $organization->load(['region:id,region', 'district:id,district']);

        $users = User::with('role:id,role_abbreviation')
            ->where('organization_id', $organization->id)
            ->select(['id', 'name', 'first_name', 'middle_name', 'last_name', 'email', 'tel_no', 'is_active', 'user_role_id'])
            ->latest()
            ->paginate(10);

        return view('manager.organization-show', compact('organization', 'users'));
    }

    /**
     * Show the form for editing the specified organization.
     *
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Http\Response
     */
    public function edit($locale, Organization $organization)
    {
        $organization->load(['region:id,region', 'district:id,district,region_id']);

        $regions = Region::get(['id', 'region']);
        $districts = District::where('region_id', $organization->region_id)
            ->get(['id', 'district', 'region_id']);

        return view('manager.organization-edit', compact('organization', 'regions', 'districts'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'region_id' => ['required', 'integer', 'exists:regions,id'],
            'district_id' => ['required', 'integer', 'exists:districts,id'],
            'ward' => ['nullable', 'string', 'max:255'],
        ]);

        $organization = new Organization();
        $organization->name = $request->name;
        $organization->region_id = $request->region_id;
        $organization->district_id = $request->district_id;
        $organization->ward = $request->ward;
        $organization->save();

        if ($organization) {
            activity()->log('Created organization');

            return redirect()->back()
                ->with('status', 'Organization information added');
        }

        return redirect()->back()
            ->withErrors('errors', 'Adding organization information failed');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $locale, $id)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'region_id' => ['required', 'integer', 'exists:regions,id'],
            'district_id' => ['required', 'integer', 'exists:districts,id'],
            'ward' => ['nullable', 'string', 'max:255'],
        ]);

        $organization = Organization::findOrFail($id);
        $organization->name = $request->name;
        $organization->region_id = $request->region_id;
        $organization->district_id = $request->district_id;
        $organization->ward = $request->ward;
        $organization->update();

        if ($organization) {
            activity()->log('Updated organization');

            return redirect()->back()
                ->with('status', 'Organization information updated');
        }

        return redirect()->back()
            ->withErrors('errors', 'Updating organization information failed');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function trash($locate, $id)
    {
        $organization = Organization::findOrFail($id);
        $organization->delete();

        if ($organization) {
            activity()->log('Trashed organization');

            return redirect()->back()->with('status', 'Organization information trashed, successfully.');
        }

        return redirect()->back()
            ->withErrors('errors', 'Trashing organization information failed, please try again.');
    }

    /**
     * Return wards for a district.
     *
     * @param  int  $districtId
     * @return \Illuminate\Http\Response
     */
    public function getWards($locale, $districtId)
    {
        $wards = Organization::where('district_id', $districtId)
            ->whereNotNull('ward')
            ->where('ward', '!=', '')
            ->select('ward')
            ->distinct()
            ->orderBy('ward')
            ->get();

        return $wards;
    }
}
