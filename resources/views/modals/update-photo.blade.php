
<!--Modal: modalUploadPic-->
<div class="modal fade" id="modalUploadPic" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog cascading-modal" role="document">
        <!--Content-->
        <div class="modal-content">
            <!--Header-->
            <div class="modal-header info-color darken-3 white-text">
                <h5 class=""><i class="fas fa-photo mr-2 text-primary"></i>{{ __('Upload Photo') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <!--/Header-->
            <!--Body-->
            <div class="modal-body mb-0">
                <form action="{{ route('user.update.photo', [app()->getLocale(), auth()->user()->id]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @METHOD ('PUT')
                    <input type="hidden" name="size" value="2000000">
                    <div class="">
                        <div class="input-group mb-3">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="file" name="image">
                                <label class="custom-file-label  border-input-primary" for="file">
                                    {{ __('Choose file') }}
                                </label>
                            </div>
                        </div>
                    </div>
                    <p id="error1" style="display:none; color:#FF0000;">
                        {{ __("Invalid image format, upload only 'gif' , 'jpeg' , 'jpg' , or 'png'") }}
                    </p>
                    <p id="error2" style="display:none; color:#FF0000;">
                        {{ __('Maximum File Size Limit is 2MB.') }}
                    </p>
                    <small class="help-block text-warning" id="imageValidate"></small>
                    <!--Footer-->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger" data-dismiss="modal">{{ __('Close') }}</button>
                        <input class="btn btn-primary  float-right" type="submit" value="{{ __('Upload image') }}" id="insert" name="uploadBtn">
                    </div>
                </form>
            </div>
            <!--/.Content-->
        </div>
    </div>
</div>
<!--Modal: modalUploadPic-->
