@section('footer')
    <!-- Footer -->

    <!-- END Footer -->
    </div>
    <!-- END Page Container -->


    <script src="{{ asset('public/dashboard_assets/js/dashmix.core.min.js') }}"></script>

    <!--
                        Dashmix JS

                        Custom functionality including Blocks/Layout API as well as other vital and optional helpers
                        webpack is putting everything together at assets/_js/main/app.js
                    -->
    <script src="{{ asset('public/dashboard_assets/js/dashmix.app.min.js') }}"></script>

    <!-- Page JS Plugins -->
    <script src="{{ asset('public/dashboard_assets/js/plugins/chart.js/Chart.bundle.min.js') }}"></script>

    <!-- Page JS Code -->
    <script src="{{ asset('public/dashboard_assets/js/pages/be_pages_dashboard.min.js') }}"></script>
    <!-- Page JS Plugins -->
    <script src="{{ asset('public/dashboard_assets/js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('public/dashboard_assets/js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('public/dashboard_assets/js/plugins/datatables/buttons/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('public/dashboard_assets/js/plugins/datatables/buttons/buttons.print.min.js') }}"></script>
    <script src="{{ asset('public/dashboard_assets/js/plugins/datatables/buttons/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('public/dashboard_assets/js/plugins/datatables/buttons/buttons.flash.min.js') }}"></script>
    <script src="{{ asset('public/dashboard_assets/js/plugins/datatables/buttons/buttons.colVis.min.js') }}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    {{-- <script src="{{ asset('public/dashboard_assets/js/plugins/select2/js/select2.full.min.js') }}"></script> --}}
    <script src="{{ asset('public/dashboard_assets/js/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('public/dashboard_assets/js/plugins/jquery-validation/additional-methods.js') }}"></script>

    <!-- Page JS Helpers (Select2 plugin) -->

    <!-- Page JS Code -->
    <script src="{{ asset('public/dashboard_assets/js/pages/be_forms_validation.min.js') }}"></script>


    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    {{-- <script src="{{ asset('public/dashboard_assets/js/plugins/select2/js/select2.full.min.js') }}"></script> --}}
    <!-- Page JS Code -->
    <script src="{{ asset('public/dashboard_assets/js/pages/be_tables_datatables.min.js') }}"></script>
    <script src="{{ asset('public/dashboard_assets/js/plugins/summernote/summernote-bs4.min.js') }}"></script>

    <!-- <script src="https://kit.fontawesome.com/88c065a148.js" crossorigin="anonymous"></script> -->
    <script src="https://kit.fontawesome.com/d9714b9e98.js" crossorigin="anonymous"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    <script src="{{ asset('public/dashboard_assets/js/plugins/ckeditor/ckeditor.js') }}"></script>
    <script defer="" src="{{ asset('public/dashboard_assets/js/plugins/flatpickr/flatpickr.min.js') }}"></script>
    <script defer
        src="{{ asset('public/dashboard_assets/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}">
    </script>
    <script src="{{ asset('public/js/dropify.js') }}"></script>
    <script src="{{ asset('public/js/jquery.floatThead.js') }}"></script>
    <script src="{{ asset('public/js/filepond.min.js') }}"></script>
    <script src="{{ asset('public/js/filepond.jquery.js') }}"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-exif-orientation/dist/filepond-plugin-image-exif-orientation.js">
    </script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-edit/dist/filepond-plugin-image-edit.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="{{ asset('public/dashboard_assets/js/plugins/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
    <script>
        jQuery(function() {
            Dashmix.helpers('flatpickr', 'simplemde', 'datepicker', 'select2', 'ckeditor', 'notify', 'loader', 'popover');
        });
    </script>

    <script>
        $('.dropify').dropify()



        $(function() {
            $('[rel="tooltip"]').on('click', function() {
                $(this).tooltip('hide')
            })
            $('.selectpicker').selectpicker();
            var $table = $('.floathead');
            $table.floatThead({
                top: 67,

                responsiveContainer: function($table) {
                    return $table.closest('.table-responsive');
                }
            });
            var $table1 = $('.floathead1');
            $table1.floatThead({
                top: 67,

                responsiveContainer: function($table) {
                    return $table.closest('.table-responsive');
                }
            });
            $('#accordion2_h1 a').click(function() {

                setTimeout(function() {
                    var reinit = $table.floatThead('destroy');
                    reinit();
                }, 320)
                // ... later you want to re-float the headers with same options

            })
            $('.nav-main-link').mouseover(function() {
                var src = $(this).find('.nav-main-link-icon').attr('src');
                var datasrc = $(this).find('.nav-main-link-icon').attr('data-src');
                $(this).find('.nav-main-link-icon').attr('src', datasrc).attr('data-src', src)

            })
            $('.nav-main-link').mouseout(function() {
                var src = $(this).find('.nav-main-link-icon').attr('src');
                var datasrc = $(this).find('.nav-main-link-icon').attr('data-src');
                $(this).find('.nav-main-link-icon').attr('src', datasrc).attr('data-src', src)

            })
            @if (!Request::is('add-contract-new/support'))
                $('.select2').select2();
            @endif
            // $('#ip_dns_address').select2({
            //             tags: true,
            //             createTag: function (params) {
            //                 // Trim the input
            //                 let term = $.trim(params.term);

            //                 // Prevent creating empty tags
            //                 if (term === '') {
            //                     return null;
            //                 }

            //                 // Check if the value already exists
            //                 let exists = $('#ip_dns_address option').filter(function () {
            //                     return $(this).val() === term;
            //                 }).length;

            //                 if (exists) {
            //                     return null; // Don't create duplicates
            //                 }

            //                 // Create a new tag
            //                 return {
            //                     id: term,
            //                     text: term,
            //                     newOption: true // Mark as new
            //                 };
            //             },
            //             insertTag: function (data, tag) {
            //                 // Add the tag only if it’s new
            //                 if (tag.newOption) {
            //                     data.push(tag);
            //                 }
            //             }
            //         });
            //         $('#ip_dns_address').on('select2:select', function (e) {
            //         let selectedValue = e.params.data.id;

            //         let exists = $('#edit_ip_dns_address option[value="' + selectedValue + '"]').length;

            //         if (!exists) {
            //             $('#edit_ip_dns_address').append(new Option(selectedValue, selectedValue));
            //         }
            //     });
            //         $('#edit_ip_dns_address').on('select2:select', function (e) {
            //         let selectedValue = e.params.data.id;

            //         let exists = $('#ip_dns_address option[value="' + selectedValue + '"]').length;

            //         if (!exists) {
            //             $('#ip_dns_address').append(new Option(selectedValue, selectedValue));
            //         }
            //     });
            // $('#edit_ip_dns_address').select2({
            //             tags: true,
            //             createTag: function (params) {
            //                 let term = $.trim(params.term);

            //                 if (term === '') {
            //                     return null;
            //                 }

            //                 let exists = $('#edit_ip_dns_address option').filter(function () {
            //                     return $(this).val() === term;
            //                 }).length;

            //                 if (exists) {
            //                     return null; // Don't create duplicates
            //                 }

            //                 // Create a new tag
            //                 return {
            //                     id: term,
            //                     text: term,
            //                     newOption: true // Mark as new
            //                 };
            //             },
            //             insertTag: function (data, tag) {
            //                 // Add the tag only if it’s new
            //                 if (tag.newOption) {
            //                     data.push(tag);
            //                 }
            //             }
            //         });
            // $('#network_adapter_mac_address').select2({
            //     tags: true,
            //     createTag: function(params) {
            //         let term = $.trim(params.term);

            //         if (term === '') {
            //             return null;
            //         }

            //         let exists = $('#network_adapter_mac_address option').filter(function() {
            //             return $(this).val() === term;
            //         }).length;

            //         if (exists) {
            //             return null; // Don't create duplicates
            //         }

            //         // Create a new tag
            //         return {
            //             id: term,
            //             text: term,
            //             newOption: true // Mark as new
            //         };
            //     },
            //     insertTag: function(data, tag) {
            //         // Add the tag only if it’s new
            //         if (tag.newOption) {
            //             data.push(tag);
            //         }
            //     }
            // });
            // $('#edit_network_adapter_mac_address').select2({
            //     tags: true,
            //     createTag: function(params) {
            //         let term = $.trim(params.term);

            //         if (term === '') {
            //             return null;
            //         }

            //         let exists = $('#edit_network_adapter_mac_address option').filter(function() {
            //             return $(this).val() === term;
            //         }).length;

            //         if (exists) {
            //             return null; // Don't create duplicates
            //         }

            //         // Create a new tag
            //         return {
            //             id: term,
            //             text: term,
            //             newOption: true // Mark as new
            //         };
            //     },
            //     insertTag: function(data, tag) {
            //         // Add the tag only if it’s new
            //         if (tag.newOption) {
            //             data.push(tag);
            //         }
            //     }
            // });

            // $('#network_adapter_mac_address').on('select2:select', function(e) {
            //     let selectedValue = e.params.data.id;

            //     let exists = $('#edit_network_adapter_mac_address option[value="' + selectedValue + '"]')
            //         .length;

            //     if (!exists) {
            //         $('#edit_network_adapter_mac_address').append(new Option(selectedValue, selectedValue));
            //     }
            // });
            // $('#edit_network_adapter_mac_address').on('select2:select', function(e) {
            //     let selectedValue = e.params.data.id;

            //     let exists = $('#network_adapter_mac_address option[value="' + selectedValue + '"]').length;

            //     if (!exists) {
            //         $('#network_adapter_mac_address').append(new Option(selectedValue, selectedValue));
            //     }
            // });
            // $('.select2').not('#ip_dns_address').select2();


            $("#example1").DataTable({
                'paging': false,
                "responsive": false,
                "lengthChange": false,
                "autoWidth": false,
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');


            $("#example3").DataTable({
                'paging': true,
                "responsive": false,
                "lengthChange": false,
                "autoWidth": false,
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
            }).buttons().container().appendTo('#example3_wrapper .col-md-6:eq(0)');


            $("#example4").DataTable({
                'paging': true,
                "responsive": false,
                "lengthChange": false,
                "autoWidth": false,
                "buttons": ["excel", "colvis"]
            }).buttons().container().appendTo('#example4_wrapper .col-md-6:eq(0)');


            $("#example5").DataTable({
                'paging': false,
                ordering: false,
                "responsive": false,
                "lengthChange": false,
                "autoWidth": false,
                'searching': false,
                "buttons": ["copy", "csv", "excel", "pdf", "print"]
            }).buttons().container().appendTo('#example5_wrapper .col-md-6:eq(0)');


            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,

            });
            var push = 0;
            $('#pushbtn').click(function() {
                if (push == 0) {
                    push = 1;
                    $('.brand-link').addClass('d-none');

                } else {
                    $('.brand-link').removeClass('d-none');
                    push = 0;
                }
            })
            $('#CommentModal').on('shown.bs.modal', function() {
                $('textarea[name=comment]').focus();
            });
            $('#CommentModalEdit').on('shown.bs.modal', function() {
                $('textarea[name=comment_edit]').focus();
            });

            // Prevent closing modals when clicking outside or pressing Esc (global default).
            if ($.fn.modal && $.fn.modal.Constructor && $.fn.modal.Constructor.Default) {
                $.fn.modal.Constructor.Default.backdrop = 'static';
                $.fn.modal.Constructor.Default.keyboard = false;
            }
        });
    </script>
    </body>

    </html>
@endsection('footer')
