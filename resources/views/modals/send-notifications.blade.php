<!-- Notification modal-->
<div class="modal fade" id="sendNoticeModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="sendNoticeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sendNoticeModalLabel">
                    <i class="fas fa-paper-plane fa-fw text-info"></i>
                    {{ __('Send Notification to Beneficiary') }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('dispute.activity.notification', app()->getLocale()) }}" method="POST">
                    @csrf
                    <div class="card-body">
                            <div class="form-row">
                                <input type="hidden" name="dispute" value="{{ $dispute->id }}">
                                <input type="hidden" name="beneficiary" value="{{ $dispute->reportedBy->id }}">
                                <input type="hidden" name="activity_type" value="notification">
                            </div>
                            <div class="form-row">
                                <div class="col-md-12 mb-3">
                                    <label for="message" class="font-weight-bold">{{ __('Message') }}<sup class="text-danger">*</sup></label>
                                    <textarea class="form-control border-text-primary @error('message') is-invalid @enderror"
                                        name="message" value="{{ old('message') }}" required autocomplete="problem_message" style="width: 100%;"></textarea>
                                        <small class="text-muted">{{ __('Eg. Return for Consultation at our offices on 26th May 2022 with all documents.') }}</small>
                                    @error('message')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary float-right mb-3">{{ __('Send') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- End Notification modal-->