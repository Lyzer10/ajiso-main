<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Income;
use App\Models\Region;
use App\Models\District;
use App\Models\Beneficiary;
use App\Models\Designation;
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
        // Build initial query
        $query = Beneficiary::whereHas('user')
            ->with('user')
            ->latest();

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
        // Get all the designations and bind them to the create view
        $designations = Designation::get(['id', 'designation']);

        // Get all the marital_statuses and bind them to the create view
        $marital_statuses = MaritalStatus::get(['id', 'marital_status']);

        // Get all the districts and bind them to the create view
        $districts = District::get(['id', 'district', 'region_id']);

        // Get all the regions and bind them to the create view
        $regions = Region::get(['id', 'region']);

        // Get all the education_levels and bind them to the create view
        $education_levels = EducationLevel::get(['id', 'education_level']);

        // Get all the marriage_forms and bind them to the create view
        $marriage_forms = MarriageForm::get(['id', 'marriage_form']);

        // Get all the incomes and bind them to the create view
        $incomes = Income::get(['id', 'income']);

        // Get all the employment_statuses and bind them to the create view
        $employment_statuses = EmploymentStatus::get(['id', 'employment_status']);

        // Get all the survey_choices and bind them to the create view
        $survey_choices = SurveyChoice::get(['id', 'survey_choice']);

        // Generate file number
        $currentYear = date('Y');

        // Get the last beneficiary created this year
        $lastUser = User::where('user_role_id', 4) // only beneficiaries
            ->whereYear('created_at', $currentYear)
            ->where('user_no', 'like', 'AJISO/' . $currentYear . '/%')
            ->orderBy('id', 'desc')
            ->first();

        // Determine next sequence number
        $nextNumber = 1; // default if no record this year

        if ($lastUser && $lastUser->user_no) {
            $lastNumber = (int) Str::afterLast($lastUser->user_no, '/');
            $nextNumber = $lastNumber + 1;
        }

        // Generate file number format
        $fileNo = 'AJISO/' . $currentYear . '/' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        // Return all data to the view
        return view('beneficiaries.create', compact(
            'designations',
            'marital_statuses',
            'regions',
            'districts',
            'education_levels',
            'survey_choices',
            'incomes',
            'marriage_forms',
            'employment_statuses',
            'fileNo' // ✅ make sure to pass this to the view
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
        /**
         * Get a validator for an incoming store request.
         *
         * @param  array  $request
         * @return \Illuminate\Contracts\Validation\Validator
         */
        $this->validate($request, [
            'user_no' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users'],
            'designation' => ['required'],
            'first_name' => ['required', 'min:3', 'string', 'max:50'],
            'middle_name' => ['nullable', 'string', 'max:50'],
            'last_name' => ['required', 'min:3', 'string', 'max:50'],
            'tel_no' => ['required', 'string', 'max:15'],
            'mobile_no' => ['nullable', 'string', 'max:15'],
            'image' => ['image', 'nullable', 'mimes:jpg,png,jpeg,gif,svg', 'max:2048'],
            'gender' => ['required'],
            'age' => ['required', 'max:3'],
            'disabled' => ['nullable'],
            'education_level' => ['required'],
            'address' => ['nullable', 'string', 'max:255'],
            'region' => ['required'],
            'district' => ['required'],
            'ward' => ['nullable', 'max:255'],
            'street' => ['nullable', 'max:255'],
            'survey_choice' => ['required'],
            'marital_status' => ['required'],
            'form_of_marriage' => ['nullable'],
            'marriage_date' => ['max:25', 'date_format:m/d/Y', 'nullable'],
            'no_of_children' => ['max:5'],
            'financial_capability' => ['required'],
            'employment_status' => ['required'],
            'occupation_business' => ['required', 'max:255'],
            'monthly_income' => ['required'],
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
        $user->designation_id = $request->designation;
        $user->first_name = Str::ucfirst($request->first_name);
        $user->middle_name = Str::ucfirst($request->middle_name);
        $user->last_name = Str::ucfirst($request->last_name);
        $user->tel_no = Str::replaceFirst('0', '+255', $request->tel_no);
        $user->mobile_no = Str::replaceFirst('0', '+255', $request->mobile_no);
        $user->user_role_id = '4';


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
            $beneficiary->district_id = $request->district;
            $beneficiary->ward = $request->ward;
            $beneficiary->street = $request->street;
            $beneficiary->survey_choice_id = $request->survey_choice;
            $beneficiary->marital_status_id = $request->marital_status;
            $beneficiary->marriage_form_id = $request->form_of_marriage;
            if (!is_null($request->marriage_date)) {
                $beneficiary->marriage_date = Carbon::parse($request->marriage_date)->format('Y-m-d');
            }
            $beneficiary->no_of_children = $request->no_of_children ?? 0;
            $beneficiary->financial_capability = $request->financial_capability;
            $beneficiary->employment_status_id = $request->employment_status;
            $beneficiary->occupation_business = $request->occupation_business;
            $beneficiary->income_id = $request->monthly_income;

            /**
             * Save the user to the database
             */

            $beneficiary->save();

            if ($beneficiary) {

                // Log user activity
                activity()->log('Created beneficiary account');

                // Send SMS to beneficiary
                $dest_addr = Str::remove('+', $user->tel_no);
                $recipients = ['recipient_id' => 1, 'dest_addr' => $dest_addr];

                $title = $user->designation->designation;

                $full_name = $beneficiary->user->first_name . ' ' . $beneficiary->user->middle_name . ' ' . $beneficiary->user->last_name;
                $beneficiary_no = $beneficiary->user->user_no;
                $created_at = Carbon::parse($beneficiary->created_at)->format('d/m/Y');

                $message = 'Habari, ' . $title . ' ' . $full_name .
                    ', AJISO inapenda kukutaarifu kuwa, akaunti yako yenye namba ya usajili No. ' . $beneficiary_no .
                    ' imesajiliwa rasmi leo, ' . $created_at .
                    '. Ahsante.';

                /**
                 * Send sms, email & database notification
                 */

                try {

                    if (env('SEND_NOTIFICATIONS') == TRUE) {

                        // SMS
                        $sms = new SmsService();
                        $sms->sendSMS($recipients, $message);

                        // Database & email
                        Notification::send($beneficiary, new BeneficiaryEnrolled($beneficiary, $message));

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

        // Get all the designations and bind them to the edit  view
        $designations = Designation::get(['id', 'designation']);

        // Get all the marital_statuses and bind them to the edit  view
        $marital_statuses = MaritalStatus::get(['id', 'marital_status']);

        // Get all the districts and bind them to the edit  view
        $districts = District::get(['id', 'district', 'region_id']);

        // Get all the education_levels and bind them to the edit  view
        $education_levels = EducationLevel::get(['id', 'education_level']);

        // Get all the education_levels and bind them to the create  view
        $education_levels = EducationLevel::get(['id', 'education_level']);

        // Get all the marriage_forms and bind them to the create  view
        $marriage_forms = MarriageForm::get(['id', 'marriage_form']);

        // Get all the incomes and bind them to the create  view
        $incomes = Income::get(['id', 'income']);

        // Get all the employment_statuses and bind them to the create  view
        $employment_statuses = EmploymentStatus::get(['id', 'employment_status']);

        return view(
            'beneficiaries.edit',
            compact(
                'beneficiary',
                'designations',
                'marital_statuses',
                'districts',
                'incomes',
                'education_levels',
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
        /**
         * Get a validator for an incoming store request.
         *
         * @param  array  $request
         * @return \Illuminate\Contracts\Validation\Validator
         */
        $this->validate($request, [
            'name' => ['required', 'string', 'min:3', 'max:255', 'unique:users'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users'],
            'designation' => ['required'],
            'first_name' => ['required', 'min:3', 'string', 'max:50'],
            'middle_name' => ['nullable', 'string', 'max:50'],
            'last_name' => ['required', 'min:3', 'string', 'max:50'],
            'tel_no' => ['required', 'string', 'max:15'],
            'mobile_no' => ['nullable', 'string', 'max:15'],
            'image' => ['image', 'nullable', 'mimes:jpg,png,jpeg,gif,svg', 'max:2048'],
            'gender' => ['required'],
            'age' => ['required', 'max:3'],
            'education_level' => ['required'],
            'address' => ['required', 'max:255'],
            'region' => ['required'],
            'district' => ['required'],
            'ward' => ['nullable', 'string', 'max:255'],
            'street' => ['nullable', 'string', 'max:255'],
            'marital_status' => ['required'],
            'form_of_marriage' => ['required'],
            'marriage_date' => ['max:25', 'date_format:m/d/Y', 'nullable'],
            'no_of_children' => ['max:5'],
            'financial_capability' => ['required'],
            'employment_status' => ['required'],
            'occupation_business' => ['required', 'string', 'max:255'],
            'monthly_income' => ['required'],
        ]);

        /**
         * Create a new user instance for a valid registration.
         *
         * @param  array  $user
         * @return \App\Models\User
         * @return \App\Models\Beneficiary
         */

        $beneficiary = Beneficiary::findOrFail($id);

        $user = User::findOrFail($beneficiary->user_id);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->designation_id = $request->designation;
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
            if (!is_null($request->age)) {
                $beneficiary->age = Carbon::parse($request->age)->format('Y-m-d');
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
            $beneficiary->marital_status_id = $request->marital_status;
            $beneficiary->marriage_form_id = $request->form_of_marriage;
            if (!is_null($request->marriage_date)) {
                $beneficiary->marriage_date = Carbon::parse($request->marriage_date)->format('Y-m-d');
            }
            $beneficiary->no_of_children = $request->no_of_children;
            $beneficiary->financial_capability = $request->financial_capability;
            $beneficiary->employment_status_id = $request->employment_status;
            $beneficiary->occupation_business = $request->occupation_business;
            $beneficiary->income_id = $request->monthly_income;

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

        $beneficiary->forceDelete();

        if ($beneficiary) {

            // Log user activity
            activity()->log('Deleted beneficiary account');

            return redirect()->back()->with('status', 'Beneficiary information deleted, successfully.');
        } else {
            return redirect()->back()->withErrors('errors', 'Deleting beneficiary information failed, please try again.');
        }
    }
}
