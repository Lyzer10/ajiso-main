<!-- Error or status message-->
<div class="row">
    <div class="col-md-10">
        @if (session('status'))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">×</button>
                {{ __(session('status')) }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ __('Ooops!') }}</strong> {{ __('Something went wrong!') }}<br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ __($error) }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</div>
