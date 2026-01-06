@php
    $beneficiaryName = trim(implode(' ', array_filter([
        optional($dispute->reportedBy)->first_name,
        optional($dispute->reportedBy)->middle_name,
        optional($dispute->reportedBy)->last_name,
    ])));
    $caseType = optional($dispute->typeOfCase)->type_of_case ?? __('N/A');
    $disputeNo = $dispute->dispute_no ?? __('N/A');
    $letterDate = \Carbon\Carbon::now()->format('d/m/Y');
@endphp

<!-- Generate Letter modal -->
<div class="modal fade letter-modal" id="generateLetterModal" tabindex="-1" aria-labelledby="generateLetterModalLabel" aria-hidden="true"
    data-dispute-no="{{ $disputeNo }}"
    data-beneficiary="{{ $beneficiaryName }}"
    data-case-type="{{ $caseType }}"
    data-letter-date="{{ $letterDate }}">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="generateLetterModalLabel">
                    <i class="fas fa-file-alt fa-fw text-primary"></i>
                    {{ __('Generate Letter') }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="letter-info-card">
                            <small class="font-weight-bold">{{ __('Auto-filled from Case') }}</small>
                            <div><strong>{{ __('Dispute No') }}:</strong> {{ $disputeNo }}</div>
                            <div><strong>{{ __('Beneficiary') }}:</strong> {{ $beneficiaryName }}</div>
                            <div><strong>{{ __('Case Type') }}:</strong> {{ $caseType }}</div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="letterType">{{ __('Letter Type') }}</label>
                                <select id="letterType" class="form-control select2" style="width: 100%;">
                                    <option value="wito">BARUA YA WITO</option>
                                    <option value="reminder">KUMBUSHO LA WITO</option>
                                    <option value="referral">REFFERAL FORM</option>
                                    <option value="feedback">FOMU YA MAONI YA MTEJA - 2018</option>
                                    <option value="registration">FOMU YA USAJILI WA MTEJA.</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="letterLanguage">{{ __('Language') }}</label>
                                <select id="letterLanguage" class="form-control select2" style="width: 100%;">
                                    <option value="en">{{ __('English') }}</option>
                                    <option value="sw">{{ __('Swahili') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group" id="recipientNameGroup">
                            <label for="recipientName" id="recipientNameLabel">{{ __('Recipient Name') }} <sup class="text-danger">*</sup></label>
                            <input type="text" class="form-control" id="recipientName" placeholder="{{ __('Enter recipient full name') }}">
                        </div>
                        <div class="form-group" id="recipientAddressGroup">
                            <label for="recipientAddress">{{ __('Recipient Address') }}</label>
                            <textarea class="form-control" id="recipientAddress" rows="3" placeholder="{{ __('Enter recipient address') }}"></textarea>
                        </div>

                        <div id="referralFields" class="d-none">
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="referral_age">{{ __('Age') }}</label>
                                    <input type="text" class="form-control" id="referral_age" placeholder="{{ __('Age') }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="referral_sex">{{ __('Sex') }}</label>
                                    <select id="referral_sex" class="form-control select2" style="width: 100%;">
                                        <option value="">{{ __('Select') }}</option>
                                        <option value="F">F</option>
                                        <option value="M">M</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="referral_district">{{ __('District') }}</label>
                                    <input type="text" class="form-control" id="referral_district" placeholder="{{ __('District') }}">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="referral_village">{{ __('Village/Ward') }}</label>
                                    <input type="text" class="form-control" id="referral_village" placeholder="{{ __('Village/Ward') }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="referral_dispute_from">{{ __('Dispute From') }}</label>
                                    <input type="text" class="form-control" id="referral_dispute_from" placeholder="{{ __('Dispute From') }}">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="referral_dispute_to">{{ __('Dispute To') }}</label>
                                    <input type="text" class="form-control" id="referral_dispute_to" placeholder="{{ __('Dispute To') }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-4" id="meetingDateGroup">
                                <label for="meeting_date" id="meetingDateLabel">{{ __('Meeting Date') }}</label>
                                <div class="input-group">
                                    <input type="text" class="form-control border-input-primary" id="meeting_date" placeholder="dd/mm/yyyy" readonly inputmode="none">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-4" id="meetingTimeGroup">
                                <label for="meeting_time" id="meetingTimeLabel">{{ __('Time') }}</label>
                                <div class="input-group">
                                    <input type="text" class="form-control border-input-primary" id="meeting_time" placeholder="hh:mm" readonly inputmode="none">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="far fa-clock"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-4" id="meetingDayGroup">
                                <label for="meeting_day" id="meetingDayLabel">{{ __('Day') }}</label>
                                <select id="meeting_day" class="form-control select2" style="width: 100%;">
                                    <option value="">{{ __('Select') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="letterNotes">{{ __('Additional Notes') }}</label>
                            <textarea class="form-control" id="letterNotes" rows="3" placeholder="{{ __('Any additional notes...') }}"></textarea>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="letter-preview-card">
                            <div class="letter-preview-header">
                                <i class="fas fa-file-alt text-primary"></i>
                                {{ __('Letter Preview') }}
                            </div>
                            <div class="letter-preview-body" id="letterPreview"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-outline-primary" id="previewLetter">
                    {{ __('Preview Letter') }}
                </button>
                <button type="button" class="btn btn-primary" id="printLetter">
                    <i class="fas fa-print mr-1"></i>
                    {{ __('Print / Download') }}
                </button>
            </div>
        </div>
    </div>
</div>
<!-- End Generate Letter modal -->
