<!-- clinic modal -->
<div class="modal fade" id="clinicProgressModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="clinicProgressModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="clinicProgressModalLabel">
                    <i class="fas fa-comment-medical fa-fw text-primary"></i>
                    {{ __('Legal Aid Advice Counseling Clinic') }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('dispute.activity.clinic', app()->getLocale()) }}" method="POST" accept-charset="utf-8" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-row">
                            <input type="hidden" name="dispute" value="{{ $dispute->id }}">
                            <input type="hidden" name="activity_type" value="clinic">
                        </div>
                        <div class="form-row">
                            <div class="col-md-3 mb-3">
                                <label for="attended_at" class="font-weight-bold">{{ __('Date Attended') }}<sup class="text-danger">*</sup></label>
                                <div class="form-group">
                                    <div class="input-group date" id="attended_at" data-target-input="nearest">
                                        <input type="text" id="attended_at"
                                            class="form-control datetimepicker-input border-prepend-primary @error('attended_at') is-invalid @enderror"
                                            name="attended_at" value="{{ old('attended_at') }}" required autocomplete="attended_at" data-target="#attended_at"
                                            data-toggle="datetimepicker"/>
                                        <div class="input-group-append" data-target="#attended_at">
                                            <div class="input-group-text  border-append-primary bg-prepend-primary">
                                                <i class="fas fa-calendar"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @error('attended_at')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="time_in" class="font-weight-bold">{{ __('Time In') }}<sup class="text-danger">*</sup></label>
                                <div class="form-group">
                                    <div class="input-group date" id="time_in" data-target-input="nearest">
                                        <input type="text" class="form-control border-prepend-primary @error('time_in') is-invalid @enderror" 
                                            name="time_in" value="{{ old('time_in') }}" required autocomplete="time_in"
                                            data-target="#time_in" data-toggle="datetimepicker"/>
                                        <div class="input-group-append" data-target="#time_in" data-toggle="datetimepicker">
                                            <div class="input-group-text border-append-primary bg-prepend-primary">
                                                <i class="fas fa-clock-o"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @error('time_in')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="time_out" class="font-weight-bold">{{ __('Time Out') }}<sup class="text-danger">*</sup></label>
                                <div class="form-group">
                                    <div class="input-group date" id="time_out" data-target-input="nearest">
                                        <input type="text" class="form-control border-prepend-primary @error('time_out') is-invalid @enderror" 
                                            name="time_out" value="{{ old('time_out') }}" required autocomplete="time_out"    
                                            data-target="#time_out" data-toggle="datetimepicker"/>
                                        <div class="input-group-append" data-target="#time_out" data-toggle="datetimepicker">
                                            <div class="input-group-text border-append-primary bg-prepend-primary">
                                                <i class="fas fa-clock-o"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @error('time_out')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="appointment" class="font-weight-bold">{{ __('Appointment') }}<sup class="text-danger">*</sup></label>
                                <select id="appointment" aria-describedby="selectAppointment"
                                    class="select2 select2-container--default  border-input-primary @error('appointment') is-invalid @enderror"
                                    name="appointment" required autocomplete="appointment" style="width: 100%;">
                                    <option hidden disabled selected value>{{ __('Choose appointment type') }}</option>
                                    <option value="open" {{ old('appointment') == 'open' ? ' selected="selected"' : '' }}>
                                        {{ __('Opened') }}
                                    </option>
                                    <option value="close" {{ old('appointment') == 'close' ? ' selected="selected"' : '' }}>
                                        {{ __('Closed') }}
                                    </option>
                                </select>
                                @error('appointment')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <label for="advice_given" class="font-weight-bold">{{ __('Steps/Advice Given') }}<sup class="text-danger">*</sup></label>
                                <textarea class="form-control border-text-primary @error('advice_given') is-invalid @enderror"
                                    name="advice_given" value="{{ old('advice_given') }}" required autocomplete="advice_given" style="width: 100%;"></textarea>
                                    <small class="text-muted">
                                        <i class="fas fa-exclamation-circle text-warning"></i>
                                        {{ __('Eg. The client came to seek advice...') }}</small>
                                @error('advice_given')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-row increment row-to-remove">
                            <div class="col-md-4 mb-3">
                                <label for="files_names" class="font-weight-bold">{{ __('File Name') }}</label>
                                <div class="form-group">
                                    <input type="text" class="form-control border-prepend-primary @error('files_names') is-invalid @enderror"
                                        name="files_names[]" placeholder="{{ __('Enter a file name') }}" value="{{ old('files_names[]') }}" autocomplete="files_names"/>
                                </div>
                                <small class="form-text text-muted">
                                    <i class="fas fa-exclamation-circle text-info"></i>
                                    {{ __('Fill file name') }}
                                </small>
                                @error('files_names')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-md-7 mb-3">
                                <label for="files" class="font-weight-bold">{{ __('File Attachments (optional)') }} </label>
                                <div class="input-group mb-3">
                                    <div class="custom-file">
                                        <input type="file" id="files[]" class="custom-file-input border-prepend-primary @error('files') is-invalid @enderror"
                                            name="files[]" value="{{ old('files') }}" autocomplete="files">
                                        <label class="custom-file-label  border-input-primary" for="files">{{ __('Choose file') }}</label>
                                    </div>
                                </div>
                                <small class="form-text text-muted">
                                    <i class="fas fa-exclamation-circle text-info"></i>
                                    {{ __('Format: jpg, jpeg, pdf, txt, doc, docx, xls, xlsx or csv. Max: 2MB') }}
                                </small>
                                @error('files')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-md-1 mb-3">
                                <div class="pt-4 mt-1">
                                    <button class="btn btn-success" id="add-row" type="button">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="clone d-none">
                            <div class="form-row row-to-remove">
                                <div class="col-md-4 mb-3">
                                    <div class="form-group">
                                        <input type="text" class="form-control border-prepend-primary @error('files_names') is-invalid @enderror"
                                            name="files_names[]" placeholder="{{ __('Enter a file name') }}" value="{{ old('files_names[]') }}" autocomplete="files_names" disabled/>
                                    </div>
                                    @error('files_names')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-7 mb-3">
                                    <div class="input-group mb-3">
                                        <div class="custom-file">
                                            <input type="file" id="files[]" class="custom-file-input border-prepend-primary @error('files') is-invalid @enderror"
                                                name="files[]" value="{{ old('files') }}" autocomplete="files" disabled />
                                            <label class="custom-file-label  border-input-primary" for="files">{{ __('Choose file') }}</label>
                                        </div>
                                    </div>
                                    @error('files')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-1 mb-3">
                                    <div class="">
                                        <button class="btn btn-danger remove-row" type="button">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary float-right">{{ __('Submit') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- End clinic modal -->

@push('scripts')

    {{-- Script for auto incrementing firld rows on click --}}
    <script type="text/javascript">

        $(document).ready(function() {

            $("#add-row").click(function(){ 

                // Make the clone class disabled so that NULL values wont be sent over with the request

                $('.clone').find('input[type=text],input[type=file]').each(function() {
                    $(this).attr("disabled", false)
                });

                var row = $(".clone").html();

                // Add a row after the first increment class row
                $(".increment").after(row);

                // Make the increment class read so that NULL values wont be sent over with the request

                $('.increment').find('input[type=text],input[type=file]').each(function() {
                    $(this).attr("disabled", false);
                });

                // Make the clone class disabled so that NULL values wont be sent over with the request

                $('.clone').find('input[type=text],input[type=file]').each(function() {
                    $(this).attr("disabled", true);
                });

            });

            // remove the last row from the parent div

            $("body").on("click",".remove-row",function(){ 

                $(this).parents(".row-to-remove").remove();

            });

        });

    </script>
    
@endpush