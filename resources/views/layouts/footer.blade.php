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
    @auth
        <div class="modal fade my-profile-modal" id="myProfileModal" tabindex="-1" role="dialog"
            aria-labelledby="myProfileModalLabel" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form id="myProfileForm" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header align-items-center border-0 position-relative">
                            <div class="d-flex flex-column">
                                <h5 class="modal-title font-titillium text-primary fs-20 fw-800" id="myProfileModalLabel">
                                    My Profile</h5>
                                <small class="font-titillium">Update your name and profile photo</small>
                            </div>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <i class="fa-solid fa-circle-xmark"></i>
                            </button>
                            <div class="form-validation-toast">
                                <div class="d-flex align-items-center">
                                    <i class="fa-light fa-triangle-exclamation text-orange fs-16 mr-2"></i>
                                    <span class="font-titillium fs-14 text-darkgrey">Field validation failed.</span>
                                </div>
                            </div>
                        </div>
                        <div class="modal-body px-4 pb-0">
                            <div class="row align-items-start my-profile-modal-layout">
                                <div class="col-md-12">
                                    <div class="border p-2 mb-3 mb-0 border-style edit-border bg-disabled">
                                        <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">Email Address / Login ID</h6>
                                        <div class="d-flex pb-1 pl-1 pt-0">
                                            <i class="fa-light fa-envelope text-grey fs-18"></i>
                                            <input type="text"
                                                class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field"
                                                id="profile_email_display" autocomplete="off"
                                                value="{{ Auth::user()->email }}" readonly>
                                        </div>
                                    </div>
                                    <div class="border p-2 mb-3 border-style edit-border">
                                        <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">First Name</h6>
                                        <div class="d-flex pb-1 pl-1 pt-0">
                                            <i class="fa-light fa-envelope text-grey fs-18"></i>
                                            <input type="text"
                                                class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field"
                                                id="profile_firstname" name="firstname" autocomplete="off"
                                                value="{{ Auth::user()->firstname }}">
                                        </div>
                                    </div>
                                    <div class="border p-2 mb-3 border-style edit-border">
                                        <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">Last Name</h6>
                                        <div class="d-flex pb-1 pl-1 pt-0">
                                            <i class="fa-light fa-envelope text-grey fs-18"></i>
                                            <input type="text"
                                                class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field"
                                                id="profile_lastname" name="lastname" autocomplete="off"
                                                value="{{ Auth::user()->lastname }}">
                                        </div>
                                    </div>
                                        <div class="my-profile-photo-column mb-3">
                                            <div class="profile-photo-absolute">
                                                <div class="profile-photo-circle" id="profile_photo_circle">
                                                    <input type="file" id="profile_photo" name="profile_photo" accept="image/*"
                                                        hidden>
    
                                                    <img id="profile_photo_preview" class="profile-photo-preview" alt="Profile photo"
                                                        src="{{ Auth::user()->user_image ? asset('public/client_logos/' . Auth::user()->user_image) : '' }}">
    
                                                    <div class="profile-photo-overlay" id="profile_photo_overlay">
                                                        <div class="profile-photo-title">Profile Photo</div>
                                                        <label for="profile_photo" class="profile-photo-browse">Browse</label>
                                                    </div>
    
                                                    <div class="profile-photo-actions" id="profile_photo_actions">
                                                        <button type="button" class="profile-photo-action" id="edit_profile_photo_btn"
                                                            title="Change photo">
                                                            <i class="fa-solid fa-pen-circle fs-20"></i>
                                                        </button>
                                                        <button type="button" class="profile-photo-action" id="remove_profile_photo_btn"
                                                            title="Remove photo">
                                                            <i class="fa-solid fa-circle-xmark fs-20"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="submit" class="btn font-titillium fw-500 py-1 new-ok-btn" id="saveMyProfileBtn">OK</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endauth

    <script>
        jQuery(function() {
            Dashmix.helpers('flatpickr', 'simplemde', 'datepicker', 'select2', 'ckeditor', 'notify', 'loader',
                'popover');
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
    @auth
        <script>
            (function() {
                let selectedProfileFile = null;
                let removeProfileImage = false;
                const existingProfileImage =
                    "{{ Auth::user()->user_image ? asset('public/client_logos/' . Auth::user()->user_image) : '' }}";
                const fallbackProfileImage = "{{ asset('public/dashboard_assets/media/avatars/avatar2.jpg') }}";

                function updateProfilePhotoUI(imageSrc) {
                    const $circle = $('#profile_photo_circle');
                    const $preview = $('#profile_photo_preview');
                    const $overlay = $('#profile_photo_overlay');

                    if (imageSrc) {
                        $preview.attr('src', imageSrc).show();
                        $overlay.hide();
                        $circle.addClass('image-set');
                    } else {
                        $preview.attr('src', '').hide();
                        $overlay.show();
                        $circle.removeClass('image-set');
                    }
                }

                $('#myProfileModal').on('show.bs.modal', function() {
                    $('#profile_firstname').val(@json(Auth::user()->firstname));
                    $('#profile_lastname').val(@json(Auth::user()->lastname));
                    $('#profile_photo').val('');
                    selectedProfileFile = null;
                    removeProfileImage = false;
                    $('#myProfileModal .form-validation-toast').hide();
                    updateProfilePhotoUI(existingProfileImage);
                });

                $(document).on('change', '#profile_photo', function() {
                    const file = this.files && this.files[0];

                    if (!file) {
                        return;
                    }

                    if (!file.type || file.type.indexOf('image/') !== 0) {
                        this.value = '';
                        return;
                    }

                    selectedProfileFile = file;
                    removeProfileImage = false;
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        updateProfilePhotoUI(e.target.result);
                    };
                    reader.readAsDataURL(file);
                });

                $(document).on('click', '#edit_profile_photo_btn', function(e) {
                    e.preventDefault();
                    $('#profile_photo').trigger('click');
                });

                $(document).on('click', '#remove_profile_photo_btn', function(e) {
                    e.preventDefault();
                    $('#profile_photo').val('');
                    selectedProfileFile = null;
                    removeProfileImage = true;
                    updateProfilePhotoUI('');
                });

                $(document).on('submit', '#myProfileForm', function(e) {
                    e.preventDefault();

                    const firstname = $('#profile_firstname').val().trim();
                    const lastname = $('#profile_lastname').val().trim();

                    if (!firstname || !lastname) {
                        $('#myProfileModal .form-validation-toast').fadeIn();
                        setTimeout(function() {
                            $('#myProfileModal .form-validation-toast').fadeOut();
                        }, 3000);
                        return;
                    }

                    const formData = new FormData();
                    formData.append('_token', $('input[name="_token"]', this).val());
                    formData.append('firstname', firstname);
                    formData.append('lastname', lastname);

                    if (selectedProfileFile) {
                        formData.append('profile_image', selectedProfileFile);
                    }

                    if (removeProfileImage) {
                        formData.append('remove_profile_image', '1');
                    }

                    $('#saveMyProfileBtn').prop('disabled', true);

                    $.ajax({
                        url: "{{ url('update-user-profile') }}",
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response && response.image_url) {
                                $('.imgAvatar').attr('src', response.image_url);
                                $('#user_iamge').val(response.image_url === fallbackProfileImage ? '' :
                                    response.image_url
                                    .split('/').pop());
                            }
                            $('#myProfileModal').modal('hide');
                            window.location.reload();
                        },
                        error: function(xhr) {
                            const message = xhr.responseJSON && xhr.responseJSON.message ?
                                xhr.responseJSON.message :
                                'Unable to update profile.';
                            alert(message);
                        },
                        complete: function() {
                            $('#saveMyProfileBtn').prop('disabled', false);
                        }
                    });
                });

                updateProfilePhotoUI(existingProfileImage);

                $("#myProfileModal").on('hidden.bs.modal', function() {
                    $('#myProfileModal .form-validation-toast').hide();
                    selectedProfileFile = null;
                    removeProfileImage = false;
                    $('#profile_photo').val('');
                    updateProfilePhotoUI(existingProfileImage);
                });
            })
            ();
        </script>
    @endauth
    </body>

    </html>
@endsection('footer')
