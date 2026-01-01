<!-- Sweet Alerts for delete confirmation -->

    {{-- sweetalert --}}
    <script src="{{ asset('plugins/sweetalert/sweetalert.min.js') }}"></script>

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