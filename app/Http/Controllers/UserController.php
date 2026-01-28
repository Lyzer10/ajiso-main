<?php

namespace App\Http\Controllers;

use \App\Models\User;
use App\Models\UserRole;
use App\Models\Organization;
use App\Models\Designation;
use Illuminate\Support\Str;
use App\Services\SmsService;
use \App\Traits\ImageUpload;
use Illuminate\Http\Request;
use App\Notifications\UserCreated;
use App\Modules\Users\Queries\UserListQuery;
use App\Modules\Users\Queries\ParalegalListQuery;
use App\Modules\Users\Services\UserCountsService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Facade\FlareClient\Http\Response;
use Intervention\Image\Facades\Image;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Notification;

class UserController extends Controller
{
    // Initialize Image Upload Trait
    use ImageUpload;

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
     * Display a listing of users.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $currentUser = auth()->user();
        $isParalegalUser = $currentUser
            && $currentUser->role
            && in_array($currentUser->role->role_abbreviation, ['paralegal', 'clerk'], true);
        if ($isParalegalUser) {
            return redirect()->route('members.list', app()->getLocale());
        }
        $beneficiaryRoleId = UserRole::where('role_abbreviation', 'beneficiary')->value('id');
        $organizationId = $isParalegalUser ? $currentUser->organization_id : null;
        $users = (new UserListQuery())->build($beneficiaryRoleId, $organizationId)->paginate(10);

        $counts = (new UserCountsService())->getRoleCounts([
            'superadmin',
            'admin',
            'paralegal',
            'staff',
        ], $organizationId);
        $super_admin_count = $counts['superadmin'];
        $admin_count = $counts['admin'];
        $paralegal_count = $counts['paralegal'];
        $lap_count = $counts['staff'];

        return view('users.list', compact(
            [
                'users',
                'super_admin_count',
                'admin_count',
                'paralegal_count',
                'lap_count'
            ]
        ));
    }

    /**
     * Display a listing of paralegal users.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function paralegals()
    {
        $currentUser = auth()->user();
        $isParalegalUser = $currentUser
            && $currentUser->role
            && in_array($currentUser->role->role_abbreviation, ['paralegal', 'clerk'], true);
        $paralegalRoleIds = UserRole::whereIn('role_abbreviation', ['paralegal', 'clerk'])
            ->pluck('id')
            ->filter()
            ->values()
            ->all();
        $search = request('search');
        $organizationId = $isParalegalUser ? $currentUser->organization_id : request('organization_id');
        $users = (new ParalegalListQuery())->build($paralegalRoleIds ?: null, $search, $organizationId)
            ->paginate(10);

        $organizations = Organization::orderBy('name')->get(['id', 'name']);

        return view('users.paralegals.list', compact('users', 'organizations', 'organizationId'));
    }

    /**
     * Display a listing of paralegal members for organization admins.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function members()
    {
        $currentUser = auth()->user();
        $isParalegal = $currentUser
            && $currentUser->role
            && in_array($currentUser->role->role_abbreviation, ['paralegal', 'clerk'], true);
        if (!$isParalegal || !$currentUser->can_register_staff) {
            abort(403, 'You are not authorized to view members.');
        }

        $paralegalRoleIds = UserRole::whereIn('role_abbreviation', ['paralegal', 'clerk'])
            ->pluck('id')
            ->filter()
            ->values()
            ->all();
        $search = request('search');
        $organizationId = $currentUser->organization_id;
        $users = (new ParalegalListQuery())->build($paralegalRoleIds ?: null, $search, $organizationId)
            ->paginate(10);

        $organizations = Organization::where('id', $organizationId)->get(['id', 'name']);
        $membersMode = true;
        $listRoute = 'members.list';

        return view('users.paralegals.list', compact('users', 'organizations', 'organizationId', 'membersMode', 'listRoute'));
    }

    /**
     * Show the form for creating a new user.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $currentUser = auth()->user();
        if ($currentUser && $currentUser->role && in_array($currentUser->role->role_abbreviation, ['paralegal', 'clerk'], true)) {
            if (!$currentUser->can_register_staff) {
                abort(403, 'You are not authorized to register users.');
            }
            return redirect()->route('paralegal.create', app()->getLocale());
        }

        // Get all the designations and bind them to the create  view
        $designations = Designation::get(['id', 'name']);

        // Get all the designations and bind them to the create  view
        $user_roles = UserRole::get(['id', 'role_abbreviation']);

        $organizations = Organization::orderBy('name')
            ->get(['id', 'name']);

        return view('users.create', compact('designations', 'user_roles', 'organizations'));
    }

    /**
     * Show the form for creating a new paralegal user.
     *
     * @return \Illuminate\Http\Response
     */
    public function createParalegal()
    {
        $currentUser = auth()->user();
        $isParalegalCreator = $currentUser
            && $currentUser->role
            && in_array($currentUser->role->role_abbreviation, ['paralegal', 'clerk'], true);
        $roleAbbreviation = $currentUser ? optional($currentUser->role)->role_abbreviation : null;
        if (!$roleAbbreviation && $currentUser && $currentUser->user_role_id) {
            $roleAbbreviation = UserRole::where('id', $currentUser->user_role_id)->value('role_abbreviation');
        }
        $isAdminUser = $roleAbbreviation && in_array($roleAbbreviation, ['superadmin', 'admin'], true);

        if ($isParalegalCreator && !$currentUser->can_register_staff) {
            abort(403, 'You are not authorized to register paralegals.');
        }

        $paralegalDesignation = Designation::whereRaw('LOWER(name) = ?', ['paralegal'])->first();
        if (!$paralegalDesignation) {
            $paralegalDesignation = Designation::create([
                'name' => 'Paralegal',
                'abbr' => 'PARA',
            ]);
        }
        $designations = Designation::orderBy('name')->get(['id', 'name']);
        $organizations = Organization::orderBy('name')
            ->get(['id', 'name']);
        $lockedOrganization = null;
        $lockOrganization = false;
        $redirectOrganizationId = null;
        $defaultDesignationId = ($isAdminUser || $isParalegalCreator) && $paralegalDesignation
            ? $paralegalDesignation->id
            : null;

        if ($isParalegalCreator) {
            $lockedOrganization = Organization::find($currentUser->organization_id);
            if ($lockedOrganization) {
                $organizations = collect([$lockedOrganization]);
                $lockOrganization = true;
            } else {
                $organizations = collect();
            }
        } elseif ($isAdminUser) {
            $organizationId = request('organization_id') ?: session('paralegal_organization_id');
            if ($organizationId) {
                $lockedOrganization = Organization::find($organizationId);
                if ($lockedOrganization) {
                    $organizations = collect([$lockedOrganization]);
                    $lockOrganization = true;
                    $redirectOrganizationId = $lockedOrganization->id;
                }
            }
        }
        $paralegalRoleId = UserRole::where('role_abbreviation', 'paralegal')->value('id')
            ?? UserRole::where('role_abbreviation', 'clerk')->value('id');

        return view('users.create-paralegal', compact(
            'designations',
            'organizations',
            'paralegalRoleId',
            'isParalegalCreator',
            'lockedOrganization',
            'lockOrganization',
            'redirectOrganizationId',
            'defaultDesignationId'
        ));
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $currentUser = auth()->user();
        $isParalegalCreator = $currentUser
            && $currentUser->role
            && in_array($currentUser->role->role_abbreviation, ['paralegal', 'clerk'], true);
        $roleAbbreviation = $currentUser ? optional($currentUser->role)->role_abbreviation : null;
        if (!$roleAbbreviation && $currentUser && $currentUser->user_role_id) {
            $roleAbbreviation = UserRole::where('id', $currentUser->user_role_id)->value('role_abbreviation');
        }
        $isAdminUser = $roleAbbreviation && in_array($roleAbbreviation, ['superadmin', 'admin'], true);
        $isParalegalCreatorForSms = $roleAbbreviation && in_array($roleAbbreviation, ['paralegal', 'clerk'], true);
        if ($isParalegalCreator && !$currentUser->can_register_staff) {
            abort(403, 'You are not authorized to register paralegals.');
        }

        $redirectOrganizationId = $request->input('redirect_organization_id');
        if ($isAdminUser && $redirectOrganizationId) {
            $request->merge(['organization_id' => $redirectOrganizationId]);
        }

        /**
         * Get a validator for an incoming store request.
         *
         * @param  array  $request
         * @return \Illuminate\Contracts\Validation\Validator
         */
        $rules = [
            'user_no' => ['required', 'string', 'max:255', 'unique:users'],
            'name' => ['required', 'string', 'min:3', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'designation' => ['required'],
            'first_name' => ['required', 'min:3', 'string', 'max:50'],
            'middle_name' => ['nullable', 'min:3', 'max:50'],
            'last_name' => ['required', 'min:3', 'string', 'max:50'],
            'tel_no' => ['required', 'string', 'max:15'],
            'image' => ['image', 'nullable', 'mimes:jpg,png,jpeg,gif,svg', 'max:2048'],
            'user_role' => ['required'],
        ];

        $role = UserRole::find($request->user_role);
        $isTargetParalegal = $role && in_array($role->role_abbreviation, ['paralegal', 'clerk'], true);

        if ($isParalegalCreator && !$isTargetParalegal) {
            return redirect()->back()
                ->withErrors('errors', 'You can only register paralegals.');
        }

        if ($isTargetParalegal && $isParalegalCreator) {
            $rules['organization_id'] = ['nullable', 'integer', 'exists:organizations,id'];
            $rules['email'] = ['nullable', 'string', 'email', 'max:255', 'unique:users'];
            $rules['tel_no'] = ['nullable', 'string', 'max:15'];
            $rules['password'] = ['nullable', 'string', 'min:8'];
        } else {
            $rules['organization_id'] = $isTargetParalegal
                ? ['required', 'integer', 'exists:organizations,id']
                : ['nullable', 'integer', 'exists:organizations,id'];
        }

        $this->validate($request, $rules);

        /**
         * Create a new user instance for a valid registration.
         *
         * @param  array  $user
         * @return \App\Models\User
         */

        $user = new User;

        $user->user_no = $request->user_no;
        $user->name = $request->name;
        $generatedPassword = null;
        if ($isTargetParalegal && $isParalegalCreator) {
            $generatedPassword = str_pad((string) random_int(0, 99999999), 8, '0', STR_PAD_LEFT);
        }
        $passwordToUse = $request->password ?: $generatedPassword;

        $user->email = $request->email;
        $user->password = Hash::make($passwordToUse);
        $user->salutation_id = $request->designation;
        $user->first_name = Str::ucfirst($request->first_name);
        $user->middle_name = Str::ucfirst($request->middle_name);
        $user->last_name = Str::ucfirst($request->last_name);
        $user->tel_no = $request->tel_no
            ? Str::replaceFirst('0', '+255', $request->tel_no)
            : '';
        $user->user_role_id = $request->user_role;
        if ($isTargetParalegal) {
            $user->organization_id = $isParalegalCreator
                ? $currentUser->organization_id
                : $request->organization_id;
        } else {
            $user->organization_id = null;
        }
        
        // Set permissions based on who is creating the paralegal
        if ($isTargetParalegal) {
            // Check if current user is an admin
            $isAdmin = $currentUser && $currentUser->role && in_array($currentUser->role->role_abbreviation, ['superadmin', 'admin'], true);

            if ($isAdmin) {
                // Admin-created paralegals can access and can register other paralegals
                $user->has_system_access = true;
                $user->can_register_staff = true;
                $user->added_by_admin = true;
            } elseif ($isParalegalCreator) {
                // Paralegal-created paralegals have no access and cannot register others
                $user->has_system_access = false;
                $user->can_register_staff = false;
                $user->added_by_admin = false;
            }
        }

        /**
         *  Preparing Image for Upload
         */
        if ($request->hasFile('image')) {

            try {

                // Initialize image path
                $image_path = 'storage/app/public/uploads/images/profiles/';

                $image = $request->file('image');

                //Handle file name and url tweaking
                $file_name_to_store = $this->UserImageUpload($image, $user->name);

                $img = Image::make($image->path());

                // backup status
                $img->backup();

                // Image resize to given aspect dimensions

                // Save this image to /uploads/profiles folder
                $img->resize(480, 640)->save($image_path . $file_name_to_store, 100);

                // reset image (return to backup state)
                $img->reset();

                // Save this thumbnail image to /uploads/profiles/thumbnails folder
                $img->resize(100, 100, function ($const) {
                    $const->aspectRatio();
                })->save($image_path . 'thumbnails/' . $file_name_to_store, 100);

                $user->image = $file_name_to_store;
            } catch (\Throwable $th) {
                //throw $th;
                redirect()->back()
                    ->withErrors('errors', 'Image could not be uploaded, please try again.');
            }
        }
        // Else add a dummy image
        else {
            $user->image = 'avatar.png';
        }

        /**
         * Save the user to the database
         */

        $user->save();

        /**
         *  Redirect user to dashboard
         */
        if ($user) {

            // Log user activity
            activity()->log('Created user account');

            /**
             * Send email & database notification
             */

            try {
                if ($isTargetParalegal) {
                    if (env('SEND_NOTIFICATIONS') == TRUE) {
                        if (!$isParalegalCreatorForSms) {
                            $dest_addr = SmsService::normalizeRecipient($user->tel_no);
                            if ($dest_addr) {
                                $recipients = ['recipient_id' => 1, 'dest_addr' => $dest_addr];
                                $message = 'Habari ' . trim($user->first_name . ' ' . $user->last_name) .
                                    ', akaunti yako ya paralegal imeundwa. Tumia barua pepe: ' . $user->email .
                                    ' na nywila: ' . $passwordToUse .
                                    ' kuingia kwenye mfumo. Ahsante.';
                                $sms = new SmsService();
                                $sms->sendSMS($recipients, $message);
                            }
                        }
                    }
                } else {
                    $shouldNotify = true;
                    if ($isParalegalCreator) {
                        $shouldNotify = false;
                    }
                    if ($shouldNotify) {
                        // Database & email
                        Notification::send($user, new UserCreated($user, $request->password));
                    }
                }
            } catch (\Throwable $th) {
                throw $th;
            }

            if ($role && in_array($role->role_abbreviation, ['paralegal', 'clerk'], true)) {
                $route = $isParalegalCreator ? 'members.list' : 'paralegals.list';
                $routeParams = ['locale' => app()->getLocale()];
                if (!$isParalegalCreator && $isAdminUser && $redirectOrganizationId) {
                    $routeParams['organization_id'] = $redirectOrganizationId;
                }
                $statusMessage = __('Paralegal information added, successfully.');
                return redirect()->route($route, $routeParams)
                    ->with('status', $statusMessage);
            }

            return redirect()->route('users.list', app()->getLocale())
                ->with('status', 'User information added, successfully.');
        } else {
            return redirect()->back()
                ->withErrors('errors', 'Adding user information failed, please try again.');
        }
    }

    /**
     * Update the specified users in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Request $request, $locale, $id)
    {
        //create a policy who update the password should be the current user

        /**
         * Get a validator for an incoming store request.
         *
         * @param  array  $request
         * @return \Illuminate\Contracts\Validation\Validator
         */
        $request->validate(
            [
                'current_password' => ['required', 'string', 'min:8'],
                'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->letters()->numbers()],
            ]
        );

        /**
         * Create a new user instance for a valid registration.
         *
         * @param  array  $user
         * @return \App\Models\User
         */

        $user = User::findOrFail($id);

        // Check that new password doesn't match the old password
        if ($request->current_password == $request->password) {
            return redirect()->back()
                ->withErrors('errors', 'Can’t be the same as a previous password, please try again., please try again.');
        }

        // Check if current password matches password in Database
        if (!Hash::check($request->current_password, $user->password)) {

            return redirect()->back()
                ->withErrors('errors', 'Current password is not a match, please try again.');
        } else {

            $user->password = Hash::make($request->password);
        }

        //Saving changes to the database

        $user->update();

        if ($user) {

            return redirect()->back()
                ->with('status', 'Your password was changed successfully, use your new password on your next login.');
        } else {

            return redirect()->back()
                ->withErrors('errors', 'Updating user password failed, please try again.');
        }
    }

    /**
     * Display the specified users.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($locale, $id)
    {
        //Find user information by Id and return a profile view
        $user = User::where('name', $id)->firstOrFail();

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified users.
     *
     * @param  String  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($locale, $id)
    {

        //Find user information by Id and return  edit view
        $user = User::where('name', $id)->firstOrFail();

        // Get all the designations and bind them to the create  view
        $designations = Designation::get(['id', 'name']);

        // Get all the designations and bind them to the create  view
        $user_roles = UserRole::get(['id', 'role_abbreviation']);

        $organizations = Organization::orderBy('name')
            ->get(['id', 'name']);

        return view('users.edit', compact('user', 'designations', 'user_roles', 'organizations'));
    }

    /**
     * Update the specified users in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request, $locale, $id)
    {

        /**
         * Get a validator for an incoming store request.
         *
         * @param  array  $request
         * @return \Illuminate\Contracts\Validation\Validator
         */
        $this->validate($request, [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'first_name' => ['required', 'min:3', 'string', 'max:50'],
            'middle_name' => ['nullable', 'min:3', 'max:50'],
            'last_name' => ['required', 'min:3', 'string', 'max:50'],
            'tel_no' => ['required', 'string', 'max:15'],
        ]);

        /**
         * Create a new user instance for a valid registration.
         *
         * @param  array  $user
         * @return \App\Models\User
         */

        $user = User::findOrFail($id);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->first_name = Str::ucfirst($request->first_name);
        $user->middle_name = Str::ucfirst($request->middle_name);
        $user->last_name = Str::ucfirst($request->last_name);

        if (Str::startsWith($request->tel_no, '0')) {
            $user->tel_no = Str::replaceFirst('0', '+255', $request->tel_no);
        } else {
            $user->tel_no = $request->tel_no;
        }

        //Saving changes to the database

        $user->update();

        if ($user) {

            // Log user activity
            activity()->log('Updated user profile');

            return redirect()->back()
                ->with('status', 'Profile information updated, successfully.');
        } else {

            return redirect()->back()
                ->withErrors('errors', 'Updating profile information failed, please try again.');
        }
    }

    /**
     * Update the image of specified users in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updatePhoto(Request $request, $locale, $id)
    {
        /**
         * Get a validator for an incoming store request.
         *
         * @param  array  $request
         * @return \Illuminate\Contracts\Validation\Validator
         */
        $this->validate($request, [
            'image' => ['image', 'required', 'mimes:jpg,png,jpeg,gif,svg', 'max:2048'],
        ]);

        /**
         * Create a new user instance for a valid registration.
         *
         * @param  array  $user
         * @return \App\Models\User
         */

        $user = User::findOrFail($id);

        /**
         *  Preparing Image for Upload
         */
        if ($request->hasFile('image')) {

            try {

                // Initialize image path
                $image_path = 'storage/app/public/uploads/images/profiles/';

                //Check if image already exists and delete it.
                $destination = $image_path . $user->image;
                $destinationThumb = $image_path . 'thumbnails/' . $user->image;

                if (File::exists($destination)) {

                    File::delete($destination);
                } elseif (File::exists($destinationThumb)) {

                    File::delete($destinationThumb);
                }

                $image = $request->file('image');

                //Handle file name and url tweaking
                $file_name_to_store = $this->UserImageUpload($image, $user->name);

                $img = Image::make($image->path());

                // backup status
                $img->backup();

                // Image resize to given aspect dimensions

                // Save this image to /uploads/profiles folder
                $img->resize(480, 640)->save($image_path . $file_name_to_store, 100);

                // reset image (return to backup state)
                $img->reset();

                // Save this thumbnail image to /uploads/profiles/thumbnails folder
                $img->resize(100, 100, function ($const) {
                    $const->aspectRatio();
                })->save($image_path . 'thumbnails/' . $file_name_to_store, 100);

                $user->image = $file_name_to_store;
            } catch (\Throwable $th) {
                //throw $th;
                redirect()->back()->withErrors('errors', 'Image could not be uploaded, please try again.');
            }
        } else {
            $user->image = "avatar.png";
        }

        //Saving changes to the database

        $user->update();


        if ($user) {

            // Log user activity
            activity()->log('Changed profile photo');

            return redirect()->back()
                ->with('status', 'User information updated, successfully.');
        } else {

            return redirect()->back()
                ->withErrors('errors', 'Updating user information failed, please try again.');
        }
    }

    /**
     * Update the specified users in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $locale, $id)
    {
        /**
         * Get a validator for an incoming store request.
         *
         * @param  array  $request
         * @return \Illuminate\Contracts\Validation\Validator
         */
        $rules = [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'designation' => ['required'],
            'first_name' => ['required', 'min:3', 'string', 'max:50'],
            'middle_name' => ['nullable', 'min:1', 'max:50'],
            'last_name' => ['required', 'min:3', 'string', 'max:50'],
            'tel_no' => ['required', 'string', 'max:15'],
            'image' => ['image', 'nullable', 'mimes:jpg,png,jpeg,gif,svg', 'max:2048'],
            'user_role' => ['required'],
            'status' => ['required'],
        ];

        $role = UserRole::find($request->user_role);
        $rules['organization_id'] = ($role && in_array($role->role_abbreviation, ['paralegal', 'clerk'], true))
            ? ['required', 'integer', 'exists:organizations,id']
            : ['nullable', 'integer', 'exists:organizations,id'];

        $this->validate($request, $rules);

        /**
         * Create a new user instance for a valid registration.
         *
         * @param  array  $user
         * @return \App\Models\User
         */

        $user = User::findOrFail($id);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->salutation_id = $request->designation;
        $user->first_name = Str::ucfirst($request->first_name);
        $user->middle_name = Str::ucfirst($request->middle_name);
        $user->last_name = Str::ucfirst($request->last_name);

        if (Str::startsWith($request->tel_no, '0')) {
            $user->tel_no = Str::replaceFirst('0', '+255', $request->tel_no);
        } else {
            $user->tel_no = $request->tel_no;
        }
        $user->user_role_id = $request->user_role;
        $user->is_active = $request->status;
        $user->organization_id = ($role && in_array($role->role_abbreviation, ['paralegal', 'clerk'], true))
            ? $request->organization_id
            : null;


        /**
         *  Preparing Image for Upload
         */
        if ($request->hasFile('image')) {

            try {

                // Initialize image path
                $image_path = 'storage/app/public/uploads/images/profiles/';

                //Check if image already exists and delete it.
                $destination = $image_path . $user->image;
                $destinationThumb = $image_path . 'thumbnails/' . $user->image;

                if (File::exists($destination)) {

                    File::delete($destination);
                } elseif (File::exists($destinationThumb)) {

                    File::delete($destinationThumb);
                }

                $image = $request->file('image');

                //Handle file name and url tweaking
                $file_name_to_store = $this->UserImageUpload($image, $user->name);

                $img = Image::make($image->path());

                // backup status
                $img->backup();

                // Image resize to given aspect dimensions

                // Save this image to /uploads/profiles folder
                $img->resize(480, 640)->save($image_path . $file_name_to_store, 100);

                // reset image (return to backup state)
                $img->reset();

                // Save this thumbnail image to /uploads/profiles/thumbnails folder
                $img->resize(100, 100, function ($const) {
                    $const->aspectRatio();
                })->save($image_path . 'thumbnails/' . $file_name_to_store, 100);

                $user->image = $file_name_to_store;
            } catch (\Throwable $th) {
                //throw $th;
                redirect()->back()->withErrors('errors', 'Image could not be uploaded, please try again.');
            }
        } else {
            $user->image = "avatar.png";
        }

        //Saving changes to the database
        $user->update();

        if ($user) {

            // Log user activity
            activity()->log('Updated user account');

            return redirect()->route('users.list', app()->getLocale())
                ->with('status', 'User information updated, successfully.');
        } else {

            return redirect()->back()
                ->withErrors('errors', 'Updating user information failed, please try again.');
        }
    }

    /**
     * Remove the specified users from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function trash($locate, $id)
    {
        //Deleting user information from the database

        $user = User::findOrFail($id);

        $user->delete();

        if ($user) {

            // Log user activity
            activity()->log('Trashed user account');

            return redirect()->back()->with('status', 'User information trashed, successfully.');
        } else {
            return redirect()->back()->withErrors('errors', 'Trashing user information failed, please try again.');
        }
    }

    /**
     * Restoring the specified users from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($locate, $id)
    {
        //Restoring user information from the database

        $user = User::onlyTrashed()->findOrFail($id);

        $user->restore();

        if ($user) {

            // Log user activity
            activity()->log('Restored user account');

            return redirect()->back()->with('status', 'User information restored, successfully.');
        } else {
            return redirect()->back()->withErrors('errors', 'Restoring user information failed, please try again.');
        }
    }

    /**
     * Remove the specified users from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($locate, $id)
    {
        //Deleting user information from the database

        $user = User::onlyTrashed()->findOrFail($id);

        // Initialize image path
        $image_path = 'storage/app/public/uploads/images/profiles/';

        //Check if image already exists and delete it.
        $destination = $image_path . $user->image;
        $destinationThumb = $image_path . 'thumbnails/' . $user->image;

        if (File::exists($destination)) {

            File::delete($destination);
        } elseif (File::exists($destinationThumb)) {

            File::delete($destinationThumb);
        }

        $user->forceDelete();

        if ($user) {

            // Log user activity
            activity()->log('Deleted user account');

            return redirect()->back()->with('status', 'User information deleted, successfully.');
        } else {
            return redirect()->back()->withErrors('errors', 'Deleting user information failed, please try again.');
        }
    }
}
