<!-- Sweet Alerts for delete confirmation -->

    {{-- sweetalert --}}
    <script src="{{ asset('plugins/sweetalert/sweetalert.min.js') }}"></script>

    <!-- Restore -->
    <script type="text/javascript">
        $('.show_restore').click(function(event) {

            var form =  $(this).closest("form");

            var name = $(this).data("name");

            event.preventDefault();

            swal({

                title: "{{ _('Restore Confirmation') }}",

                text: "{{ _('You are restoring a record, you want to proceed?') }}",

                icon: "warning",

                buttons: true,

                dangerMode: true,

            })

            .then((willRestore) => {

            if (willRestore) {

                form.submit();
            }

            });

        });
    </script>

    <!-- Delete -->
    <script type="text/javascript">
        $('.show_delete').click(function(event) {

            var form =  $(this).closest("form");

            var name = $(this).data("name");

            event.preventDefault();

            swal({

                title: "{{ _('Delete Confirmation') }}",

                text: "{{ _('You are deleting a record, you want to proceed?') }}",

                icon: "warning",

                buttons: true,

                dangerMode: true,

            })

            .then((willDelete) => {

            if (willDelete) {

                form.submit();
            }

            });

        });
    </script>