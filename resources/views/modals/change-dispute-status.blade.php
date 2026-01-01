<!-- dispute status modal -->
<div class="modal fade" id="disputeStatusModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="disputeStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sendNoticeModalLabel">
                    <i class="fas fa-edit fa-fw text-success"></i>
                    {{ __('Update Dispute Status') }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('dispute.activity.status', app()->getLocale()) }}" method="POST">
                    @csrf
                    @METHOD('PUT')
                    <div class="card-body">
                        <div class="form-row">
                            <input type="hidden" name="dispute" value="{{ $dispute->id }}">
                            <input type="hidden" name="beneficiary" value="{{ $dispute->reportedBy->id }}">
                            <input type="hidden" name="activity_type" value="status">
                        </div>
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <label for="dispute_status" class="font-weight-bold">{{ __('Dispute Status') }}<sup class="text-danger">*</sup></label>
                                <select id="dispute_status" aria-describedby="selectstatus"
                                    class="select2 select2-container--default  border-input-primary @error('dispute_status') is-invalid @enderror"
                                    name="dispute_status" required autocomplete="dispute_status" style="width: 100%;">
                                    <option hidden disabled selected value>{{ __('Choose dispute status') }}</option>
                                    @if ($dispute_statuses->count())
                                        @foreach ($dispute_statuses as $dispute_status)
                                            <option value="{{ $dispute_status->id }}"
                                                @if ($dispute_status->id === $dispute->dispute_status_id)
                                                    selected="selected"
                                                @endif
                                            >
                                                {{ $dispute_status->dispute_status }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option>{{ __('No dispute statuses found') }}</option>
                                    @endif
                                </select>
                                @error('dispute_status')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <label for="description" class="font-weight-bold">{{ __('Description') }}</label>
                                <textarea class="form-control border-text-primary @error('description') is-invalid @enderror"
                                    name="description" value="{{ old('description') }}" autocomplete="problem_description" style="width: 100%;"></textarea>
                                    <small class="text-muted">{{ __('Eg. Return for Consultation at our offices on 26th May 2022 with all documents.') }}</small>
                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary float-right mb-3">{{ __('Submit') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- End dispute status modal -->
