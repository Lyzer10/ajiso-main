<!-- remarks modal -->
<div class="modal fade" id="providerRemarksModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="providerRemarksModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sendNoticeModalLabel">
                    <i class="fas fa-marker fa-fw text-secondary"></i>
                    {{ __('Legal Aid Provider Remarks') }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('dispute.activity.remarks', app()->getLocale()) }}" method="POST" accept-charset="utf-8" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="form-row">
                            <input type="hidden" name="dispute" value="{{ $dispute->id }}">
                            <input type="hidden" name="activity_type" value="remarks">
                        </div>
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <label for="remarks" class="font-weight-bold">{{ __('Remarks') }}<sup class="text-danger">*</sup></label>
                                <textarea class="form-control border-text-primary @error('remarks') is-invalid @enderror"
                                    name="remarks" value="{{ old('remarks') }}" required autocomplete="remarks" style="width: 100%;"></textarea>
                                    <small class="text-muted">
                                        <i class="fas fa-exclamation-circle text-warning"></i>
                                        {{ __('Eg. The client came to seek advice...') }}</small>
                                @error('remarks')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
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
<!-- End remarks modal -->