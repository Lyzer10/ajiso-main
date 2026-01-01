        <!-- Logout modal -->
        <div class="modal fade" id="modalLogout">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">
                            <i class="fas fa-exclamation-triangle fa-fw text-danger"></i>
                            {{ __('Logout Confirmation') }}
                        </h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>{{ __('Any unsaved work will be lost, do you want proceed?') }}</p>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-outline-danger" data-dismiss="modal">
                            {{ __('Close') }}
                        </button>
                        <form action="{{ route('logout', app()->getLocale()) }}" method="post">
                            @csrf
                            <button type="submit" class="btn btn-success">{{ __('Yes, Logout') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- /logout modal -->
