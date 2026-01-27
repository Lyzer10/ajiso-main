<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Income;
use App\Models\Region;
use App\Models\Tribe;
use App\Models\Religion;
use App\Models\District;
use App\Models\Beneficiary;
use App\Models\Designation;
use App\Models\Organization;
use App\Notifications\CustomNotice;
use Illuminate\Support\Str;
use \App\Traits\ImageUpload;
use App\Models\MarriageForm;
use App\Models\SurveyChoice;
use App\Services\SmsService;
use Illuminate\Http\Request;
use App\Models\MaritalStatus;
use App\Models\EducationLevel;
use Illuminate\Support\Carbon;
use App\Models\EmploymentStatus;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;
use App\Notifications\BeneficiaryEnrolled;
use App\Traits\FetchAdmins;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;

class BeneficiaryController extends Controller
{

    // Initialize Traits
    use ImageUpload;
    use FetchAdmins;

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
     * Display a listing of the beneficiaries.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $organizationId = $this->getOrganizationId();

        // Build initial query
        $query = Beneficiary::whereHas('user')
            ->with('user')
            ->latest();

        if ($organizationId) {
            $query->whereHas('user', function ($q) use ($organizationId) {
                $q->where('organization_id', $organizationId);
            });
        }

        // Apply search if request has ?search=
        if ($search = request('search')) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        // Execute query + pagination
        $beneficiaries = $query->paginate(10);

        return view('beneficiaries.list', compact('beneficiaries'));
    }


    /**
     * Show the form for creating a new beneficiary.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function create()
    {
        $organizationId = $this->getOrganizationId();
        if ($this->isParalegal() && !$organizationId) {
            return redirect()->back()
                ->withErrors('errors', 'Organization not assigned.');
        }

        // Get all the marital_statuses and bind them to the create view
        $marital_statuses = MaritalStatus::get(['id', 'marital_status']);

        // Get all the districts and bind them to the create view
        $districts = District::get(['id', 'district', 'region_id']);

        // Get all the regions and bind them to the create view
        $regions = Region::get(['id', 'region']);

        // Get all the education_levels and bind them to the create view
        $education_levels = EducationLevel::get(['id', 'education_level']);

        // Get all the tribes and bind them to the create view
        $tribes = Tribe::get(['id', 'tribe']);

        // Get all the religions and bind them to the create view
        $religions = Religion::get(['id', 'religion']);

        // Get all the marriage_forms and bind them to the create view
        $marriage_forms = MarriageForm::get(['id', 'marriage_form']);

        // Get all the employment_statuses and bind them to the create view
        $employment_statuses = EmploymentStatus::get(['id', 'employment_status']);

        // Get all the survey_choices and bind them to the create view
        $survey_choices = SurveyChoice::get(['id', 'survey_choice']);

        $defaultRegionId = null;
        $defaultDistrictId = null;
        if ($this->isParalegal() && $organizationId) {
            $org = Organization::find($organizationId);
            if ($org) {
                $defaultRegionId = $org->region_id;
                $defaultDistrictId = $org->district_id;
            }
        }

        // Generate file number
        $currentYear = date('Y');
        
        // Get organization prefix
        $prefix = 'AJISO';
        if ($this->isParalegal() && $organizationId) {
            $org = Organization::find($organizationId);
            if ($org) {
                $initials = $this->getOrganizationInitials($org->name);
                if ($initials !== '') {
                    $prefix = $initials;
                }
            }
        }

        // Get the last beneficiary created this year with the same prefix
        $lastUserQuery = User::where('user_role_id', 4) // only beneficiaries
            ->whereYear('created_at', $currentYear)
            ->where('user_no', 'like', $prefix . '/' . $currentYear . '/%');

        if ($this->isParalegal() && $organizationId) {
            $lastUserQuery->where('organization_id', $organizationId);
        }

        $lastUser = $lastUserQuery
            ->orderBy('id', 'desc')
            ->first();

        // Determine next sequence number
        $nextNumber = 1; // default if no record this year

        if ($lastUser && $lastUser->user_no) {
            $lastNumber = (int) Str::afterLast($lastUser->user_no, '/');
            $nextNumber = $lastNumber + 1;
        }

        // Generate file number format
        $fileNo = $prefix . '/' . $currentYear . '/' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        // Return all data to the view
        return view('beneficiaries.create', compact(
            'marital_statuses',
            'regions',
            'districts',
            'education_levels',
            'survey_choices',
            'tribes',
            'religions',
            'marriage_forms',
            'employment_statuses',
            'fileNo',
            'defaultRegionId',
            'defaultDistrictId' // ✅ make sure to pass this to the view
        ));
    }


    /**
     * Store a newly created beneficiary in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $organizationId = $this->getOrganizationId();
        if ($this->isParalegal() && !$organizationId) {
            return redirect()->back()
                ->withErrors('errors', 'Organization not assigned.');
        }

        $marriedStatusId = MaritalStatus::where('marital_status', 'Married')->value('id');
        $isMarried = $marriedStatusId && (int) $request->marital_status === (int) $marriedStatusId;
        $defaultMarriageFormId = MarriageForm::where('marriage_form', 'N/A')->value('id')
            ?? MarriageForm::min('id')
            ?? 1;

        /**
         * Get a validator for an incoming store request.
         *
         * @param  array  $request
         * @return \Illuminate\Contracts\Validation\Validator
         */
        $registrationSourceRule = $this->isParalegal()
            ? ['sometimes', Rule::in(['office', 'paralegal'])]
            : ['required', Rule::in(['office', 'paralegal'])];

        $this->validate($request, [
            'user_no' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users'],
            'first_name' => ['required', 'min:3', 'string', 'max:50'],
            'middle_name' => ['nullable', 'string', 'max:50'],
            'last_name' => ['required', 'min:3', 'string', 'max:50'],
            'tel_no' => ['nullable', 'string', 'max:15'],  // Made optional - "Telephone No"
            'mobile_no' => ['nullable', 'string', 'max:15'],
            'image' => ['image', 'nullable', 'mimes:jpg,png,jpeg,gif,svg', 'max:2048'],
            'gender' => ['required'],
            'age' => ['required', 'max:3'],
            'disabled' => ['required', 'in:yes,no'],
            'registration_source' => $registrationSourceRule,
            'tribe' => ['required', 'integer', 'exists:tribes,id'],
            'religion' => ['required', 'integer', 'exists:religions,id'],
            'education_level' => ['required'],
            'address' => ['nullable', 'string', 'max:255'],
            'region' => ['required'],
            'district' => ['required'],
            'ward' => ['nullable', 'max:255'],
            'street' => ['nullable', 'max:255'],
            'survey_choice' => ['nullable'],  // Made optional - "How did you hear about us"
            'marital_status' => ['required'],
            'form_of_marriage' => [Rule::requiredIf($isMarried), 'nullable', 'integer', 'exists:marriage_forms,id'],
            'marriage_date' => ['max:25', 'date_format:m/d/Y', 'nullable'],
            'no_of_children' => ['max:5'],
            'financial_capability' => ['required'],
            'employment_status' => ['required'],
            'occupation_business' => ['required', 'max:255'],
        ]);

        /**
         * Create a new user instance for a valid registration.
         *
         * @param  array  $user
         * @return \App\Models\User
         * @return \App\Models\Beneficiary
         */

        $user = new User;

        $user->user_no = $request->user_no;
        $user->name = Str::lower(Str::substr($request->first_name, 0, 8) . '.' . Str::substr(Str::uuid(), 0, 3));
        $user->email = $request->email;
        $user->password = Hash::make('Alas%2021');
        $defaultDesignationId = Designation::where('name', 'Other')->value('id')
            ?? Designation::where('abbr', 'OTHER')->value('id')
            ?? Designation::min('id')
            ?? 1;
        $user->salutation_id = $request->input('designation', $defaultDesignationId);
        $user->first_name = Str::ucfirst($request->first_name);
        $user->middle_name = Str::ucfirst($request->middle_name);
        $user->last_name = Str::ucfirst($request->last_name);
        $user->tel_no = Str::replaceFirst('0', '+255', $request->tel_no);
        $user->mobile_no = Str::replaceFirst('0', '+255', $request->mobile_no);
        $user->user_role_id = '4';
        $user->organization_id = $this->isParalegal() ? $organizationId : null;


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

                // Save this image to /uploads/images/profiles folder
                $img->resize(480, 640)->save($image_path . $file_name_to_store, 100);

                // reset image (return to backup state)
                $img->reset();

                // Save this thumbnail image to /uploads/images/profiles/thumbnails folder
                $img->resize(100, 100, function ($const) {
                    $const->aspectRatio();
                })->save($image_path . 'thumbnails/' . $file_name_to_store, 100);

                $user->image = $file_name_to_store;
            } catch (\Throwable $th) {
                //throw $th;
                redirect()->back()->withErrors('errors', 'Image could not be uploaded, please try again.');
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
         *  Redirect user to beneficiaries list
         */

        if ($user) {

            $beneficiary = new Beneficiary();

            $beneficiary->user_id = $user->id;
            $beneficiary->gender = $request->gender;
            $beneficiary->disabled = $request->disabled;
            if (!is_null($request->age)) {
                $beneficiary->age = $request->age;
                $age = $beneficiary->age;

                if ($age < 18) {
                    $beneficiary->age_group = 1;
                } elseif ($age >= 18 && $age <= 45) {
                    $beneficiary->age_group = 2;
                } elseif ($age >= 46 && $age <= 59) {
                    $beneficiary->age_group = 3;
                } elseif ($age >= 60) {
                    $beneficiary->age_group = 4;
                }
            }

            $beneficiary->education_level_id = $request->education_level;
            $beneficiary->address = $request->address;
            
            // For paralegal: use organization's region and district as defaults
            if ($this->isParalegal() && $organizationId) {
                $org = Organization::find($organizationId);
                if ($org) {
                    $beneficiary->district_id = $org->district_id;
                }
            } else {
                $beneficiary->district_id = $request->district;
            }
            
            $beneficiary->ward = $request->ward;
            $beneficiary->street = $request->street;
            $beneficiary->survey_choice_id = $request->survey_choice ?? null;
            $beneficiary->tribe_id = $request->tribe;
            $beneficiary->religion_id = $request->religion;
            $beneficiary->marital_status_id = $request->marital_status;
            $beneficiary->marriage_form_id = $isMarried ? $request->form_of_marriage : $defaultMarriageFormId;
            if ($isMarried && !is_null($request->marriage_date)) {
                $beneficiary->marriage_date = Carbon::parse($request->marriage_date)->format('Y-m-d');
            } else {
                $beneficiary->marriage_date = null;
            }
            $beneficiary->no_of_children = $request->no_of_children ?? 0;
            $beneficiary->financial_capability = $request->financial_capability;
            $beneficiary->employment_status_id = $request->employment_status;
            $beneficiary->occupation_business = $request->occupation_business;
            $defaultIncomeId = Income::where('income', 'N/A')->value('id') ?? Income::min('id') ?? 1;
            $beneficiary->income_id = $request->input('monthly_income', $defaultIncomeId);
            $beneficiary->registration_source = $this->isParalegal()
                ? 'paralegal'
                : $request->registration_source;

            /**
             * Save the user to the database
             */

            $beneficiary->save();

            if ($beneficiary) {

                // Log user activity
                activity()->log('Created beneficiary account');

                // Send SMS to beneficiary
                $dest_addr = SmsService::normalizeRecipient($user->tel_no);
                $recipients = ['recipient_id' => 1, 'dest_addr' => $dest_addr];

                $title = trim((string) optional($user->designation)->name);

                $full_name = trim(implode(' ', array_filter([
                    $beneficiary->user->first_name ?? '',
                    $beneficiary->user->middle_name ?? '',
                    $beneficiary->user->last_name ?? '',
                ])));
                $display_name = $full_name;
                if ($title !== '' && strtolower($title) !== 'other') {
                    $display_name = trim($title . ' ' . $full_name);
                }
                $beneficiary_no = $beneficiary->user->user_no;
                $created_at = Carbon::parse($beneficiary->created_at)->format('d/m/Y');

                $message = 'Habari, ' . $display_name .
                    ', AJISO inapenda kukutaarifu kuwa, akaunti yako yenye namba ya usajili No. ' . $beneficiary_no .
                    ' imesajiliwa rasmi leo, ' . $created_at .
                    '. Ahsante.';

                /**
                 * Send sms, email & database notification
                 */

                if ($beneficiary->registration_source === 'office') {
                    try {
                        if (env('SEND_NOTIFICATIONS') == TRUE) {
                            if (!$this->isParalegal()) {
                                // SMS
                                $sms = new SmsService();
                                $sms->sendSMS($recipients, $message);
                            }

                            // Database & email
                            Notification::send($beneficiary->user, new BeneficiaryEnrolled($beneficiary, $message));

                            // Notify admins and superadmins
                            $admins = $this->getAdmins();
                            $superAdmins = $this->getSuperAdmins();
                            $allAdmins = $admins->merge($superAdmins);

                            $adminMessage = "New beneficiary registered: " . $full_name . " (ID: " . $beneficiary_no . ")";

                            Notification::send($allAdmins, new CustomNotice(
                                'New Beneficiary Registration',
                                'info',
                                $adminMessage
                            ));
                        }
                    } catch (\Throwable $th) {
                        throw $th;
                    }
                }
            }

            return redirect()->route('dispute.create.new', app()->getLocale())
                ->with('status', 'Beneficiary information added, successfully. Proceed to register dispute.');
        } else {
            return redirect()->back()
                ->withErrors('errors', 'Adding beneficiary information failed, please try again.');
        }
    }

    /**
     * Display the specified beneficiary.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($locale, $id)
    {
        //Find beneficiary information by Id and return a profile view
        $beneficiary = Beneficiary::has('user')
            ->with(
                'user',
                'disputes',
                'district',
            )
            ->findOrFail($id);

        $this->ensureOrganizationAccess($beneficiary);

        return view('beneficiaries.show', compact('beneficiary'));
    }

    /**
     * Show the form for editing the specified beneficiary.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($locale, $id)
    {
        // Find beneficiary information by Id and return a edit view
        $beneficiary = Beneficiary::has('user')
            ->with('user')
            ->findOrFail($id);

        $this->ensureOrganizationAccess($beneficiary);

        // Get all the marital_statuses and bind them to the edit  view
        $marital_statuses = MaritalStatus::get(['id', 'marital_status']);

        // Get all the districts and bind them to the edit  view
        $districts = District::get(['id', 'district', 'region_id']);

        // Get all the education_levels and bind them to the edit  view
        $education_levels = EducationLevel::get(['id', 'education_level']);

        // Get all the education_levels and bind them to the create  view
        $education_levels = EducationLevel::get(['id', 'education_level']);

        // Get all the tribes and bind them to the edit view
        $tribes = Tribe::get(['id', 'tribe']);

        // Get all the religions and bind them to the edit view
        $religions = Religion::get(['id', 'religion']);

        // Get all the marriage_forms and bind them to the create  view
        $marriage_forms = MarriageForm::get(['id', 'marriage_form']);

        // Get all the employment_statuses and bind them to the create  view
        $employment_statuses = EmploymentStatus::get(['id', 'employment_status']);

        return view(
            'beneficiaries.edit',
            compact(
                'beneficiary',
                'marital_statuses',
                'districts',
                'education_levels',
                'tribes',
                'religions',
                'marriage_forms',
                'employment_statuses'
            )
        );
    }

    /**
     * Update the specified beneficiary in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $locale, $id)
    {
        $beneficiary = Beneficiary::findOrFail($id);
        $user = User::findOrFail($beneficiary->user_id);
        $this->ensureOrganizationAccess($beneficiary);

        $marriedStatusId = MaritalStatus::where('marital_status', 'Married')->value('id');
        $isMarried = $marriedStatusId && (int) $request->marital_status === (int) $marriedStatusId;
        $defaultMarriageFormId = MarriageForm::where('marriage_form', 'N/A')->value('id')
            ?? MarriageForm::min('id')
            ?? 1;

        /**
         * Get a validator for an incoming store request.
         *
         * @param  array  $request
         * @return \Illuminate\Contracts\Validation\Validator
         */
        $this->validate($request, [
            'name' => ['nullable', 'string', 'min:3', 'max:255', Rule::unique('users', 'name')->ignore($user->id)],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'designation' => ['nullable', 'integer', 'exists:designations,id'],
            'first_name' => ['required', 'min:3', 'string', 'max:50'],
            'middle_name' => ['nullable', 'string', 'max:50'],
            'last_name' => ['required', 'min:3', 'string', 'max:50'],
            'tel_no' => ['required', 'string', 'max:15'],
            'mobile_no' => ['nullable', 'string', 'max:15'],
            'image' => ['image', 'nullable', 'mimes:jpg,png,jpeg,gif,svg', 'max:2048'],
            'gender' => ['required'],
            'age' => ['required', 'max:3'],
            'disabled' => ['required', 'in:yes,no'],
            'registration_source' => ['required', Rule::in(['office', 'paralegal'])],
            'tribe' => ['required', 'integer', 'exists:tribes,id'],
            'religion' => ['required', 'integer', 'exists:religions,id'],
            'education_level' => ['required'],
            'address' => ['nullable', 'max:255'],
            'region' => ['required'],
            'district' => ['required'],
            'ward' => ['nullable', 'string', 'max:255'],
            'street' => ['nullable', 'string', 'max:255'],
            'marital_status' => ['required'],
            'form_of_marriage' => [Rule::requiredIf($isMarried), 'nullable', 'integer', 'exists:marriage_forms,id'],
            'marriage_date' => ['max:25', 'date_format:m/d/Y', 'nullable'],
            'no_of_children' => ['max:5'],
            'financial_capability' => ['required'],
            'employment_status' => ['required'],
            'occupation_business' => ['required', 'string', 'max:255'],
        ]);

        /**
         * Create a new user instance for a valid registration.
         *
         * @param  array  $user
         * @return \App\Models\User
         * @return \App\Models\Beneficiary
         */

        if ($request->filled('name')) {
            $user->name = $request->name;
        }
        $user->email = $request->email;
        if ($request->filled('designation')) {
            $user->salutation_id = $request->designation;
        }
        $user->first_name = Str::ucfirst($request->first_name);
        $user->middle_name = Str::ucfirst($request->middle_name);
        $user->last_name = Str::ucfirst($request->last_name);
        if (Str::startsWith($request->tel_no, '0')) {
            $user->tel_no = Str::replaceFirst('0', '+255', $request->tel_no);
        } else {
            $user->tel_no = $request->tel_no;
        }
        if (Str::startsWith($request->mobile_no, '0')) {
            $user->mobile_no = Str::replaceFirst('0', '+255', $request->mobile_no);
        } else {
            $user->mobile_no = $request->mobile_no;
        }

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
        }
        // Else add a dummy image
        else {
            $user->image = 'avatar.png';
        }

        /**
         * Save the user to the database
         */

        $user->update();

        /**
         *  Redirect user to dashboard
         */

        if ($user) {

            $beneficiary->gender = $request->gender;
            $beneficiary->disabled = $request->disabled;
            if (!is_null($request->age)) {
                $beneficiary->age = (int) $request->age;
                $age = $beneficiary->age;

                if ($age < 18) {
                    $beneficiary->age_group = 1;
                } elseif ($age >= 18 && $age <= 45) {
                    $beneficiary->age_group = 2;
                } elseif ($age >= 46 && $age <= 59) {
                    $beneficiary->age_group = 3;
                } elseif ($age >= 60) {
                    $beneficiary->age_group = 4;
                }
            }

            $beneficiary->education_level_id = $request->education_level;
            $beneficiary->address = $request->address;
            $beneficiary->district_id = $request->district;
            $beneficiary->ward = $request->ward;
            $beneficiary->street = $request->street;
            $beneficiary->tribe_id = $request->tribe;
            $beneficiary->religion_id = $request->religion;
            $beneficiary->marital_status_id = $request->marital_status;
            $beneficiary->marriage_form_id = $isMarried ? $request->form_of_marriage : $defaultMarriageFormId;
            if ($isMarried && !is_null($request->marriage_date)) {
                $beneficiary->marriage_date = Carbon::parse($request->marriage_date)->format('Y-m-d');
            } else {
                $beneficiary->marriage_date = null;
            }
            $beneficiary->no_of_children = $isMarried ? ($request->no_of_children ?? 0) : 0;
            $beneficiary->financial_capability = $request->financial_capability;
        $beneficiary->employment_status_id = $request->employment_status;
        $beneficiary->occupation_business = $request->occupation_business;
        $beneficiary->registration_source = $request->registration_source;
            if ($request->filled('monthly_income')) {
                $beneficiary->income_id = $request->monthly_income;
            }

            /**
             * Save the user to the database
             */

            $beneficiary->update();

            // Log user activity
            activity()->log('Updated beneficiary information');


            return redirect()->route('beneficiaries.list', app()->getLocale())
                ->with('status', 'Beneficiary information updated, successfully.');
        } else {
            return redirect()->back()
                ->withErrors('errors', 'Updating beneficiary information failed, please try again.');
        }
    }

    /**
     * Remove the specified beneficiaries from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function trash($locate, $id)
    {
        //Deleting beneficiary information from the database

        $beneficiary = Beneficiary::findOrFail($id);
        $this->ensureOrganizationAccess($beneficiary);

        $beneficiary->delete();

        if ($beneficiary) {

            // Log beneficiary activity
            activity()->log('Trashed beneficiary account');

            return redirect()->back()->with('status', 'Beneficiary information trashed, successfully.');
        } else {
            return redirect()->back()->withErrors('errors', 'Trashing beneficiary information failed, please try again.');
        }
    }

    /**
     * Restoring the specified beneficiaries from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($locate, $id)
    {
        //Restoring beneficiary information from the database

        $beneficiary = Beneficiary::onlyTrashed()->findOrFail($id);
        $this->ensureOrganizationAccess($beneficiary);

        $beneficiary->restore();

        if ($beneficiary) {

            // Log beneficiary activity
            activity()->log('Restored beneficiary account');

            return redirect()->back()->with('status', 'Beneficiary information restored, successfully.');
        } else {
            return redirect()->back()->withErrors('errors', 'Restoring beneficiary information failed, please try again.');
        }
    }

    /**
     * Remove the specified beneficiaries from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($locate, $id)
    {
        //Deleting beneficiary information from the database

        $beneficiary = Beneficiary::onlyTrashed()->findOrFail($id);
        $this->ensureOrganizationAccess($beneficiary);

        $beneficiary->forceDelete();

        if ($beneficiary) {

            // Log user activity
            activity()->log('Deleted beneficiary account');

            return redirect()->back()->with('status', 'Beneficiary information deleted, successfully.');
        } else {
            return redirect()->back()->withErrors('errors', 'Deleting beneficiary information failed, please try again.');
        }
    }

    private function isParalegal()
    {
        $user = auth()->user();
        return $user && $user->role && $user->role->role_abbreviation === 'paralegal';
    }

    private function getOrganizationId()
    {
        return $this->isParalegal() ? auth()->user()->organization_id : null;
    }

    private function getOrganizationInitials($name)
    {
        $name = trim((string) $name);
        if ($name === '') {
            return '';
        }

        $parts = preg_split('/[^A-Za-z0-9]+/', $name, -1, PREG_SPLIT_NO_EMPTY);
        if (!$parts) {
            return '';
        }

        $initials = '';
        foreach ($parts as $part) {
            $initials .= Str::upper(Str::substr($part, 0, 1));
        }

        return $initials;
    }

    private function ensureOrganizationAccess(Beneficiary $beneficiary)
    {
        $organizationId = $this->getOrganizationId();
        if (!$organizationId) {
            return;
        }

        $beneficiary->loadMissing('user:id,organization_id');
        $beneficiaryOrgId = optional($beneficiary->user)->organization_id;

        if ((int) $beneficiaryOrgId !== (int) $organizationId) {
            abort(403, 'You are not authorized to access this beneficiary.');
        }
    }
}
