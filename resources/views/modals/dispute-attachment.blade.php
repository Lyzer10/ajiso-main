<!-- Attachment modal-->
<div class="modal fade" id="disputeAttachmentModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="disputeAttachmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="disputeAttachmentModalLabel">
                    <i class="fas fa-paperclip fa-fw text-info"></i>
                    {{ __('Add Attachment') }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('dispute.activity.attachment', app()->getLocale()) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="form-row">
                            <input type="hidden" name="dispute" value="{{ $dispute->id }}">
                        </div>
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <label for="attachment_name" class="font-weight-bold">{{ __('Attachment Name') }}</label>
                                <input type="text" class="form-control border-input-primary @error('attachment_name') is-invalid @enderror"
                                    name="attachment_name" value="{{ old('attachment_name') }}" autocomplete="attachment_name" placeholder="{{ __('Optional name') }}">
                                @error('attachment_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <label for="attachment" class="font-weight-bold">{{ __('Attachment (Image or PDF)') }}<sup class="text-danger">*</sup></label>
                                <div class="input-group mb-3">
                                    <div class="custom-file">
                                        <input type="file" id="attachment" class="custom-file-input border-prepend-primary @error('attachment') is-invalid @enderror"
                                            name="attachment" accept="image/*,application/pdf" required>
                                        <label class="custom-file-label border-input-primary attachment-file-label" for="attachment">{{ __('Choose file') }}</label>
                                    </div>
                                </div>
                                <small class="form-text text-muted">
                                    <i class="fas fa-exclamation-circle text-info"></i>
                                    {{ __('Format: jpg, jpeg, png or pdf. Max: 2MB') }}
                                </small>
                                @error('attachment')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary float-right mb-3">{{ __('Upload') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- End Attachment modal-->
