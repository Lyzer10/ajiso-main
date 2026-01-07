<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Staff;
use App\Models\Dispute;
use App\Models\Beneficiary;
use App\Models\DisputeFile;
use App\Models\DisputeAttachment;
use Illuminate\Support\Str;
use App\Services\SmsService;
use Illuminate\Http\Request;
use App\Models\DisputeStatus;
use Illuminate\Support\Carbon;
use App\Models\CounselingSheet;
use App\Models\DisputeActivity;
use App\Mail\beneficiaryEnrolled;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Notifications\UpdateDisputeStatus;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SendClientNotification;

class DisputeActivityController extends Controller
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
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendNotification(Request $request)
    {
        /**
         * Get a validator for an incoming store request.
         *
         * @param  array  $request
         * @return \Illuminate\Contracts\Validation\Validator
         */
        $this->validate($request, [
            'dispute' => ['required'],
            'beneficiary' => ['required'],
            'activity_type' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
        ]);
        /**
         * Create a new dispute activity instance for a valid registration.
         *
         * @param  array  $activity
         * @return \App\Models\DisputeActivity
         */

        // Get the authenticated staff
        $curr_staff = optional(auth()->user()->staff)->id;

        $activity = new DisputeActivity();

        $activity->dispute_activity = "Notification to Client";
        $activity->activity_type = $request->activity_type;
        $activity->description = $request->message;
        $activity->dispute_id = $request->dispute;
        $activity->staff_id = $curr_staff ?? NULL;

        /**
         * Save the dispute to the database
         */

        $activity->save();

        /**
         *  Redirect user to dispute profile
         */

        if ($activity) {
            
            // Get user information
            $user = User::with('designation:id,name')
                            ->select(['id','email', 'first_name','middle_name','last_name','tel_no','salutation_id'])
                            ->findOrFail($request->beneficiary);

            /**
             *  Extract beneficiary details
             */

            // Get tel no and add it to the recipient lits
            $dest_addr = SmsService::normalizeRecipient($user->tel_no);
            $recipients = ['recipient_id' => 1, 'dest_addr'=> $dest_addr];

            // Get title of the beneficiary
            $title = optional($user->designation)->name;

            // Get full name of the beneficiary
            $full_name = trim(implode(' ', array_filter([
                $user->first_name,
                $user->middle_name,
                $user->last_name,
            ])));

            $title = trim((string) $title);
            $display_name = $full_name;
            if ($title !== '' && strtolower($title) !== 'other') {
                $display_name = trim($title.' '.$full_name);
            }

            // Prepare message to be sent
            $message = 'Habari, '.$display_name.
                        '. '.$activity->description.
                        '. Ahsante.';

            /**
             * Send sms, email & database notification
            */ 

            try {
                if(env('SEND_NOTIFICATIONS') == TRUE)
                {
                
                    // SMS
                    $sms = new SmsService();
                    $sms->sendSMS($recipients, $message);

                    // Database & email
                    Notification::send($user, new SendClientNotification($user, $message));
                    

                    // Log user activity
                    activity()->log('Sent notification to beneficiary');

                }

            } catch (\Throwable $th) {
                throw $th;
            }

            return redirect()->back()
                            ->with('status', 'Notification sent to beneficiary, successfully.')
                            ->with('prompt_status_update', true);

        } else {
            return redirect()->back()
                            ->withErrors('errors', 'Sending notification to beneficiary information failed, please try again.');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function changeStatus(Request $request)
    {
        /**
         * Get a validator for an incoming store request.
         *
         * @param  array  $request
         * @return \Illuminate\Contracts\Validation\Validator
         */
        $this->validate($request, [
            'dispute' => ['required'],
            'beneficiary' => ['required'],
            'dispute_status' => ['required', 'string'],
            'activity_type' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        /**
         * Create a new dispute instance for a valid registration.
         *
         * @param  array  $dispute
         * @return \App\Models\Dispute
         */

        // Getting the dispute that matches the current id
        $dispute = Dispute::findOrFail($request->dispute);

        /**
         * Create a new dispute activity instance for a valid registration.
         *
         * @param  array  $activity
         * @return \App\Models\DisputeActivity
         */

        // Register dispute activity on dispute status update
        $activity = new DisputeActivity();

        // Check if the requested status matches the one in the database.
        if ($dispute->dispute_status_id === $request->dispute_status) {
            return redirect()->back()
                            ->with('status', 'No changes made, please change the dispute status and try again.');

        }else {


            // Get the requested status name
            $status = DisputeStatus::select(['dispute_status'])
                                    ->findOrFail($request->dispute_status);

            switch ($status->dispute_status) {
                case 'resolved':
                    $activity->dispute_activity = 'Dispute Resolved';
                    break;
                case 'discontinued':
                    $activity->dispute_activity = 'Dispute Discontinued';
                    break;
                case 'referred':
                    $activity->dispute_activity = 'Dispute Referred';
                    break;
                case 'continue':
                    $activity->dispute_activity = 'Dispute Continued';
                    break;

                default:
                    $activity->dispute_activity = 'Dispute Updated';
                    break;
            }

        }

        // Update the dispute status
        $dispute->dispute_status_id = (int) $request->dispute_status;

        $dispute->update();

        // Get the authenticated staff
        $curr_staff = optional(auth()->user()->staff)->id;

        $activity->activity_type = $request->activity_type;
        $activity->description = $request->description ?? '';
        $activity->dispute_id = (int) $request->dispute;
        $activity->staff_id = $curr_staff ?? NULL;

        /**
         * Save the dispute to the database
         */

        /**
         *  Redirect user to dispute profile
         */

        if ($dispute) {

            // Log user activity
            activity()->log('Changed dispute status');

            // Save dispute activity
            $activity->save();

            // Get current beneficiary model
            $beneficiary = Beneficiary::has('user')
                                    ->with(
                                        'user:id,tel_no,first_name,middle_name,last_name,salutation_id'
                                        )
                                    ->select(['id', 'user_id'])
                                    ->findOrFail($dispute->beneficiary_id);
            // Beneficiary
            /**
             *  Extract beneficiary details
             */

            // Get tel no and add it to the recipient lits
            $dest_addr = SmsService::normalizeRecipient($beneficiary->user->tel_no);
            $recipients = ['recipient_id' => 1, 'dest_addr'=> $dest_addr];

            // Get full name of the beneficiary
            $beneficiary_name = trim(implode(' ', array_filter([
                $beneficiary->user->first_name,
                $beneficiary->user->middle_name,
                $beneficiary->user->last_name,
            ])));

            $beneficiary_title = trim((string) optional($beneficiary->user->designation)->name);
            $beneficiary_display_name = $beneficiary_name;
            if ($beneficiary_title !== '' && strtolower($beneficiary_title) !== 'other') {
                $beneficiary_display_name = trim($beneficiary_title.' '.$beneficiary_name);
            }

            // Get date when the dispute was reported
            $reported_at = Carbon::parse($dispute->reported_on)->format('d/m/Y');

            // Get today date
            $updated_at = Carbon::now()->format('d/m/Y');

            if($activity) {

                $message = null;
                $statusName = $status->dispute_status ?? optional($dispute->disputeStatus)->dispute_status;

                switch ($activity->dispute_activity) {
                    case 'Dispute Resolved':
                        $message = 'Habari, '.$beneficiary_display_name.
                                    '. AJISO inapenda kukutaarifu kuwa, kesi yako uliyo ripoti tarehe '.$reported_at.
                                    ' yenye namba ya usajili No. '.$dispute->dispute_no.
                                    ' imefanikiwa kusuluhishwa leo tarehe '.$updated_at.
                                    '. Ahsante.';

                        break;
                    case 'Dispute Discontinued':
                        $message = 'Habari, '.$beneficiary_display_name.
                                    '. AJISO inakutaarifu kuwa, kesi yako uliyo ripoti tarehe '.$reported_at.
                                    ' yenye namba ya usajili No. '.$dispute->dispute_no.
                                    ' sitishwa leo tarehe '.$updated_at.
                                    '. Tafadhali wasiliana na mtoa huduma za kisheria iliyepangiwa kwa maelezo zaidi
                                    . Ahsante.';

                        break;
                    case 'Dispute Referred':
                        $message = 'Habari, '.$beneficiary_display_name.
                                    '. AJISO inakutaarifu kuwa, kesi yako uliyo ripoti tarehe '.$reported_at.
                                    ' yenye namba ya usajili No. '.$dispute->dispute_no.
                                    ' imepewa rufaa leo tarehe '.$updated_at.
                                    '. Tafadhali wasiliana na mtoa huduma za kisheria iliyepangiwa kwa maelezo zaidi
                                    . Ahsante.';

                        break;
                    case 'Dispute Continued':
                        $message = 'Habari, '.$beneficiary_display_name.
                                    '. AJISO inapenda kukutaarifu kuwa, kesi yako uliyo ripoti tarehe '.$reported_at.
                                    ' yenye namba ya usajili No. '.$dispute->dispute_no.
                                    ' imeendelezwa leo tarehe '.$updated_at.
                                    '. Ahsante.';
                        break;
                    default:
                        $message = 'Habari, '.$beneficiary_display_name.
                                    '. AJISO inapenda kukutaarifu kuwa, kesi yako uliyo ripoti tarehe '.$reported_at.
                                    ' yenye namba ya usajili No. '.$dispute->dispute_no.
                                    ' imesasishwa leo tarehe '.$updated_at.'.'.
                                    ($statusName ? ' Hali ya kesi ni: '.$statusName.'.' : '').
                                    ' Ahsante.';
                        break;
                }
            
                /**
                 * Send sms, email & database notification
                */ 

                try {
                    if(env('SEND_NOTIFICATIONS') == TRUE)
                    {
                        // SMS
                        $sms = new SmsService();
                        if ($message) {
                            $sms->sendSMS($recipients, $message);
                        }

                        // Database & email
                        if ($message) {
                            Notification::send($beneficiary->user, new UpdateDisputeStatus($beneficiary, $message));
                        }
                    
                    }

                } catch (\Throwable $th) {
                    throw $th;
                }

            }

            return redirect()->back()
                            ->with('status', 'Dispute information updated, successfully.');

        } else {
            return redirect()->back()
                            ->withErrors('errors', 'Updating dispute information failed, please try again.');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function clinicVisits(Request $request)
    {
        /**
         * Get a validator for an incoming store request.
         *
         * @param  array  $request
         * @return \Illuminate\Contracts\Validation\Validator
         */
        $this->validate($request, [
            'dispute' => ['required', 'string'],
            'attended_at' => ['required', 'string'],
            'appointment' => ['required', 'string'],
            'time_in' => ['required', 'string'],
            'time_out' => ['required', 'string'],
            'activity_type' => ['required', 'string'],
            'advice_given' => ['required', 'string'],
            'files_names' => ['nullable'],
            'files_names.*' => ['string', 'max:190'],
            'files' => ['nullable'],
            'files.*' => ['mimes:jpg,jpeg,csv,txt,xlx,xls,xlxs,doc,docx,pdf', 'max:2048']
        ]);

        /**
         * Create a new dispute activity instance for a valid registration.
         *
         * @param  array  $activity
         * @return \App\Models\DisputeActivity
         */

        // Get the authenticated staff
        $curr_staff = optional(auth()->user()->staff)->id;

        // Create new DisputeActivity instance
        $activity = new DisputeActivity();

        $activity->activity_type = $request->activity_type;
        $activity->dispute_activity = "LAAC Clinic Visit";
        $activity->description = Str::substr($request->advice_given, 0, 20).'...';
        $activity->dispute_id = $request->dispute;
        $activity->staff_id = $curr_staff ?? NULL; 

        /**
         * Save the dispute activity to the database
         */

        $activity->save();

        /**
         *  Redirect user to dispute profile
         */

        if ($activity) {

            // Log user activity
            activity()->log('Attended beneficiary LAAC clinic');

            /**
             *  Record the Disputes counselling sheet
             */ 

            // Create new CounsellingSheet instance
            $sheet = new CounselingSheet();

            $sheet->attended_at = Carbon::parse($request->attended_at)->format('Y-m-d');
            $sheet->time_in = Carbon::parse($request->time_in)->format('H:i:s');
            $sheet->time_out = Carbon::parse($request->time_out)->format('H:i:s');
            $sheet->is_open = ($request->appointment === 'open') ? 1 : 0;
            $sheet->advice_given = $request->advice_given;
            $sheet->dispute_activity_id = $activity->id;

            /**
             * Save the sheet to the database
             */

            $sheet->save();

            if ($sheet && $request->hasFile('files')) {
                try {
                    // Initialize image path
                    $image_path = 'public/uploads/documents/dispute_files';
                    $dispute_files = [];

                    foreach ($request->file('files') as $key => $file) {
                        if (!$file) {
                            continue;
                        }

                        $file_name = $request->files_names[$key] ?? '';
                        $file_name = trim($file_name);
                        if ($file_name === '') {
                            $file_name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) ?: 'Document';
                        }

                        $ext = $file->getClientOriginalExtension();
                        $db_name = 'file-' . time() . '-' . rand(1, 100) . '.' . $ext;
                        $path = $file->storeAs($image_path, $db_name);

                        $dispute_files[] = [
                            'name' => $file_name,
                            'path' => $path,
                            'file_type' => $ext,
                            'counseling_sheet_id' => $sheet->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }

                    if ($dispute_files) {
                        DisputeFile::insert($dispute_files);
                    }
                } catch (\Throwable $th) {
                    return redirect()->back()
                        ->withErrors('errors', 'Files could not be uploaded, please try again.');
                }
            }
            return redirect()->back()
                            ->with('status', 'LAAC clinic information recorded, successfully.')
                            ->with('prompt_status_update', true);

        } else {
            return redirect()->back()
                            ->withErrors('errors', 'Recording LAAC clinic information failed, please try again.');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function providerRemarks(Request $request)
    {
        /**
         * Get a validator for an incoming store request.
         *
         * @param  array  $request
         * @return \Illuminate\Contracts\Validation\Validator
         */
        $this->validate($request, [
            'dispute' => ['required', 'string'],
            'activity_type' => ['required', 'string', 'max:255'],
            'remarks' => ['required', 'string'],
        ]);
        /**
         * Create a new dispute activity instance for a valid registration.
         *
         * @param  array  $activity
         * @return \App\Models\DisputeActivity
         */

         // Get the authenticated staff
        $curr_staff = optional(auth()->user()->staff)->id;

        // Create new DisputeActivity instance
        $activity = new DisputeActivity();

        $activity->dispute_activity = "Legal Aid Provider Remarks";
        $activity->activity_type = $request->activity_type;
        $activity->description = $request->remarks;
        $activity->dispute_id = $request->dispute;
        $activity->staff_id = $curr_staff ?? NULL;

        /**
         * Save the dispute to the database
         */

        $activity->save();

        /**
         *  Redirect user to dispute profile
         */

        if ($activity) {

            // Log user activity
            activity()->log('Made remarks on dispute');

            return redirect()->back()
                            ->with('status', 'Dispute remarks added, successfully.')
                            ->with('prompt_status_update', true);

        } else {
            return redirect()->back()
                            ->withErrors('errors', 'Adding dispute remarks failed, please try again.');
        }
    }

    /**
     * Store a newly created dispute attachment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addAttachment(Request $request)
    {
        /**
         * Get a validator for an incoming store request.
         *
         * @param  array  $request
         * @return \Illuminate\Contracts\Validation\Validator
         */
        $this->validate($request, [
            'dispute' => ['required', 'integer', 'exists:disputes,id'],
            'attachment_name' => ['nullable', 'string', 'max:255'],
            'attachment' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);

        $dispute = Dispute::findOrFail($request->dispute);

        $file = $request->file('attachment');
        $ext = $file->getClientOriginalExtension();
        $db_name = 'attachment-' . time() . '-' . rand(1, 100) . '.' . $ext;
        $path = $file->storeAs('uploads/documents/dispute_attachments', $db_name, 'public');

        $name = $request->attachment_name;
        if (!$name) {
            $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) ?: 'Attachment';
        }

        $attachment = DisputeAttachment::create([
            'dispute_id' => $dispute->id,
            'name' => $name,
            'path' => $path,
            'file_type' => $ext,
        ]);

        if ($attachment) {
            activity()->log('Added dispute attachment');

            $curr_staff = optional(auth()->user()->staff)->id;

            $activity = new DisputeActivity();
            $activity->dispute_activity = 'Attachment Added';
            $activity->activity_type = 'attachment';
            $activity->description = $name;
            $activity->dispute_id = $dispute->id;
            $activity->staff_id = $curr_staff;
            $activity->save();

            return redirect()->back()
                ->with('status', 'Attachment added, successfully.')
                ->with('prompt_status_update', true);
        }

        return redirect()->back()
            ->withErrors('errors', 'Adding attachment failed, please try again.');
    }

    /**
     * View a dispute attachment.
     *
     * @param  string  $locale
     * @param  \App\Models\DisputeAttachment  $attachment
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
     */
    public function viewAttachment($locale, DisputeAttachment $attachment)
    {
        $diskPath = $this->getAttachmentDiskPath($attachment);
        $disk = Storage::disk('public');

        if (!$disk->exists($diskPath)) {
            return redirect()->back()
                ->withErrors('errors', 'Attachment file not found.');
        }

        return $disk->response($diskPath);
    }

    /**
     * Download a dispute attachment.
     *
     * @param  string  $locale
     * @param  \App\Models\DisputeAttachment  $attachment
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
     */
    public function downloadAttachment($locale, DisputeAttachment $attachment)
    {
        $diskPath = $this->getAttachmentDiskPath($attachment);
        $disk = Storage::disk('public');

        if (!$disk->exists($diskPath)) {
            return redirect()->back()
                ->withErrors('errors', 'Attachment file not found.');
        }

        $downloadName = $attachment->name;
        $ext = $attachment->file_type;

        if (!Str::endsWith(Str::lower($downloadName), '.' . Str::lower($ext))) {
            $downloadName .= '.' . $ext;
        }

        return $disk->download($diskPath, $downloadName);
    }

    /**
     * Delete a dispute attachment.
     *
     * @param  string  $locale
     * @param  \App\Models\DisputeAttachment  $attachment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteAttachment($locale, DisputeAttachment $attachment)
    {
        $name = $attachment->name;
        $disputeId = $attachment->dispute_id;

        $diskPath = $this->getAttachmentDiskPath($attachment);
        $disk = Storage::disk('public');

        if ($disk->exists($diskPath)) {
            $disk->delete($diskPath);
        }

        $deleted = $attachment->delete();

        if ($deleted) {
            activity()->log('Deleted dispute attachment');

            $curr_staff = optional(auth()->user()->staff)->id;

            $activity = new DisputeActivity();
            $activity->dispute_activity = 'Attachment Deleted';
            $activity->activity_type = 'attachment';
            $activity->description = $name;
            $activity->dispute_id = $disputeId;
            $activity->staff_id = $curr_staff;
            $activity->save();

            return redirect()->back()
                ->with('status', 'Attachment deleted, successfully.');
        }

        return redirect()->back()
            ->withErrors('errors', 'Deleting attachment failed, please try again.');
    }

    private function getAttachmentDiskPath(DisputeAttachment $attachment)
    {
        $path = $attachment->path;

        if (Str::startsWith($path, 'public/')) {
            return Str::after($path, 'public/');
        }

        return $path;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function clinicSheet($locale, $id)
    {
        //Find sheet information by Id and return a activity profile view
        $sheet = CounselingSheet::with('files')
                                    ->findOrFail($id);

        return view('dispute-activity.show', compact('sheet'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($locale, $id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
