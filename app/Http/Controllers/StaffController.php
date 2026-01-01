<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Staff;
use App\Models\Designation;
use App\Traits\ImageUpload;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;
use PhpParser\Node\Stmt\TryCatch;
use App\Models\Dispute;

class StaffController extends Controller
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
     * Display a listing of the staff.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Start the query
        $query = Staff::has('user')
            ->with('user')
            ->latest();

        // Apply search if any
        if ($search = request('search')) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        // Pagination after filtering
        $staff = $query->paginate(10);

        return view('staff.list', compact('staff'));
    }


    /**
     * Show the form for creating a new staff.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Get all the designations and bind them to the create  view
        $designations = Designation::get(['id', 'designation']);

        return view('staff.create', compact('designations'));
    }

    /**
     * Store a newly created staff in storage.
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
            'name' => ['required', 'string', 'min:3', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'designation' => ['required'],
            'first_name' => ['required', 'min:3', 'string', 'max:50'],
            'middle_name' => ['nullable', 'min:3', 'max:50'],
            'last_name' => ['required', 'min:3', 'string', 'max:50'],
            'tel_no' => ['required', 'string', 'max:15'],
            'image' => ['image', 'nullable', 'mimes:jpg,png,jpeg,gif,svg', 'max:2048'],
            'office' => ['required', 'string', 'max:50'],
        ]);

        /**
         * Create a new user instance for a valid registration.
         *
         * @param  array  $user
         * @return \App\Models\User
         * @return \App\Models\Staff
         */

        $user = new User;

        $user->user_no = $request->user_no;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make('Alas%2021');
        $user->designation_id = $request->designation;
        $user->first_name = Str::ucfirst($request->first_name);
        $user->middle_name = Str::ucfirst($request->middle_name);
        $user->last_name = Str::ucfirst($request->last_name);
        $user->tel_no = Str::replaceFirst('0', '+255', $request->tel_no);
        $user->user_role_id = '3';

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
         *  Redirect user to dashboard
         */

        if ($user) {

            $staff = new Staff();

            $staff->user_id = $user->id;
            $staff->office = $request->office;

            /**
             * Save the user to the database
             */

            $staff->save();

            // Log user activity
            activity()->log('Created staff account');

            return redirect()->route('staff.list', app()->getLocale())
                ->with('status', 'Staff information added, successfully.');
        } else {
            return redirect()->back()
                ->withErrors('errors', 'Adding staff information failed, please try again.');
        }
    }

    /**
     * Display the specified staff.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($locale, $id)
    {
        //Find staff information by Id and return a profile view
        $staff = Staff::with('user')->findOrFail($id);

        $disputes = Dispute::with(
            'assignedTo:first_name,middle_name,last_name,user_no',
            'reportedBy:first_name,middle_name,last_name,user_no',
            'disputeStatus:id,dispute_status'
        )
            ->where('staff_id',  $staff->id)
            ->select([
                'id',
                'dispute_no',
                'beneficiary_id',
                'reported_on',
                'type_of_service_id',
                'type_of_case_id',
                'dispute_status_id'
            ])
            ->paginate(10);

        // Get count of all disputes
        $dispute_total = $staff->disputes->count();

        // Get count of proceeding disputes
        $dispute_proceed = $staff->disputes->where('dispute_status_id', '2')
            ->count();
        // Get count of resolved disputes
        $dispute_resolved = $staff->disputes->where('dispute_status_id', '3')
            ->count();

        // Compute % success
        if ($dispute_total > 0) {
            // TODO make decimal into 2 pts
            $success_rate = abs($dispute_resolved / $dispute_total) * 100;
        } else {
            $success_rate = 0;
        }

        // return view
        return view(
            'staff.show',
            compact(
                'staff',
                'disputes',
                'dispute_total',
                'dispute_proceed',
                'dispute_resolved',
                'success_rate'
            )
        );
    }

    /**
     * Show the form for editing the specified staff.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($locale, $id)
    {
        // Find staff information by Id and return a edit view
        $staff = Staff::with('user')->findOrFail($id);

        // Get all the designations and bind them to the create  view
        $designations = Designation::get(['id', 'designation']);

        return view('staff.edit', compact('staff', 'designations'));
    }

    /**
     * Update the specified staff in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $locale, $id)
    {
        /**
         * Get a validator for an incoming store request.
         *Request $request, $locale, $id
         * @param  array  $request
         * @return \Illuminate\Contracts\Validation\Validator
         */
        $this->validate($request, [
            'user_no' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'designation' => ['required'],
            'first_name' => ['required', 'min:3', 'string', 'max:50'],
            'middle_name' => ['nullable', 'min:3', 'max:50'],
            'last_name' => ['required', 'min:3', 'string', 'max:50'],
            'tel_no' => ['required', 'string', 'max:15'],
            'image' => ['image', 'nullable', 'mimes:jpg,png,jpeg,gif,svg', 'max:2048'],
            'office' => ['required', 'string', 'max:50'],
        ]);

        /**
         * Create a new user instance for a valid registration.
         *
         * @param  array  $user
         * @return \App\Models\User
         * @return \App\Models\Staff
         */

        $staff = Staff::findOrFail($id);

        $user = User::findOrFail($staff->user_id);

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

                // Image resize to given aspect     dimensions

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

        $user->update();

        /**
         *  Redirect user to dashboard
         */

        if ($user) {

            $staff->office = $request->office;

            /**
             * Save the user to the database
             */

            $staff->update();

            // Log user activity
            activity()->log('Updated staff account');

            return redirect()->route('staff.list', app()->getLocale())
                ->with('status', 'Staff information updated, successfully.');
        } else {
            return redirect()->back()
                ->withErrors('errors', 'Updating staff information failed, please try again.');
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
        //Deleting staff information from the database

        $staff = Staff::findOrFail($id);

        $staff->delete();

        if ($staff) {

            // Log user activity
            activity()->log('Trashed staff account');

            return redirect()->back()->with('status', 'Staff information trashed, successfully.');
        } else {
            return redirect()->back()->withErrors('errors', 'Trashing staff information failed, please try again.');
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
        //Restoring staff information from the database

        $staff = Staff::onlyTrashed()->findOrFail($id);

        $staff->restore();

        if ($staff) {

            // Log staff activity
            activity()->log('Restored staff account');

            return redirect()->back()->with('status', 'Staff information restored, successfully.');
        } else {
            return redirect()->back()->withErrors('errors', 'Restoring staff information failed, please try again.');
        }
    }

    /**
     * Remove the specified staffs from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($locate, $id)
    {
        //Deleting staff information from the database

        $staff = Staff::onlyTrashed()->findOrFail($id);

        $staff->forceDelete();

        if ($staff) {

            // Log user activity
            activity()->log('Deleted staff account');

            return redirect()->back()->with('status', 'Staff information deleted, successfully.');
        } else {
            return redirect()->back()->withErrors('errors', 'Deleting staff information failed, please try again.');
        }
    }
}
