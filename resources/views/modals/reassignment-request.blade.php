<div class="modal fade" id="reassignmentRequestModal" tabindex="-1" aria-labelledby="reassignmentRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reassignmentRequestModalLabel">
                    {{ $isParalegalUser ? __('Reassign To AJISO Admin') : __('Request Legal Aid Assistance') }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('dispute.request.store', app()->getLocale()) }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="dispute" value="{{ $dispute->id }}">
                    <div class="form-row">
                        <div class="col-md-12 mb-3">
                            <label class="font-weight-bold">{{ __('Dispute') }}</label>
                            <input type="text" class="form-control border-input-primary" value="{{ $dispute->dispute_no }}" readonly>
                        </div>
                    </div>
                    @if (!empty($requiresTargetStaff))
                        @php
                            $currentStaffId = optional(auth()->user()->staff)->id;
                        @endphp
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <label for="target_staff_id" class="font-weight-bold">{{ __('Request assistance from') }}<sup class="text-danger">*</sup></label>
                                <select id="target_staff_id" name="target_staff_id"
                                    class="select2 select2-container--default border-input-primary @error('target_staff_id') is-invalid @enderror"
                                    style="width: 100%;" required>
                                    <option hidden disabled selected value>{{ __('Choose legal aid provider') }}</option>
                                    @forelse ($availableStaff as $staffMember)
                                        @continue($currentStaffId && (int) $staffMember->id === (int) $currentStaffId)
                                        <option value="{{ $staffMember->id }}" {{ old('target_staff_id') == $staffMember->id ? 'selected' : '' }}>
                                            {{ $staffMember->user->first_name.' '
                                                .$staffMember->user->middle_name.' '
                                                .$staffMember->user->last_name.' | '
                                                .$staffMember->center->name }}
                                        </option>
                                    @empty
                                        <option disabled>{{ __('No legal aid providers found') }}</option>
                                    @endforelse
                                </select>
                                @error('target_staff_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    @endif
                    <div class="form-row">
                        <div class="col-md-12 mb-3">
                            <label for="reason_description" class="font-weight-bold">{{ __('Request Reason') }}<sup class="text-danger">*</sup></label>
                            <textarea name="reason_description" id="reason_description"
                                class="form-control border-text-primary @error('reason_description') is-invalid @enderror"
                                placeholder="{{ __('Describe request reason here...') }}" rows="4">{{ old('reason_description') }}</textarea>
                            @error('reason_description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Send Request') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
