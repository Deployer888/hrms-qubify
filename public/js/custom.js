/**
 *
 * You can write your JS code here, DO NOT touch the default style file
 * because it will make it harder for you to update.
 *
 */

"use strict";

$(document).ready(function () {

    /* if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/firebase-messaging-sw.js')
            .then((registration) => {
                console.log('Service Worker registered:', registration);
            })
            .catch((error) => {
                console.error('Service Worker registration failed:', error);
            });
    } */
    
    
    

    if ($(".dataTable").length > 0) {
        $(".dataTable").dataTable({
            language: dataTabelLang,
        });
    }
    

    if ($(".minDatepicker").length > 0) {
        $('.minDatepicker').daterangepicker({
            singleDatePicker: true,
            minDate: new Date(),
            locale: date_picker_locale,
        });
    }
    
    if ($(".datepicker").length > 0) {
        $('.datepicker').daterangepicker({
            singleDatePicker: true,
            minDate: '0000-01-01',
            format: 'YYYY-MM-DD',
            locale: {
                date_picker_locale,
                format: 'YYYY-MM-DD'
            },
            singleDatePicker: true,
            autoUpdateInput: false  // This prevents auto-filling the input
        });
    
        // Handle the date selection to update input only when a date is chosen
        $('.datepicker').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD'));
            $(this).trigger('change');
        });
    
        // Handle cancel to clear the input
        $('.datepicker').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
        
        $('.datepicker').on('focus', function() {
            $(this).data('daterangepicker').show();
        });
        
        $('.datepicker').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD'));
        });
    }
    
    if ($(".maxDatepicker").length > 0) {
        $('.maxDatepicker').daterangepicker({
            singleDatePicker: true,
            maxDate: new Date(),
            locale: date_picker_locale,
        });
    }
    
    if ($(".timepicker_format").length > 0) {
        $('.timepicker_format').timepicker({
            showMeridian: false,
            minuteStep: 5,

        });
    }

    if ($(".select2").length > 0) {
        $(".select2").select2({
            disableOnMobile: false,
            nativeOnMobile: false
        });
    }

    if ($(".summernote-simple").length) {
        $('.summernote-simple').summernote();
    }

    // for Choose file
    $(document).on('change', 'input[type=file]', function () {
        var fileclass = $(this).attr('data-filename');
        var finalname = $(this).val().split('\\').pop();
        $('.' + fileclass).html(finalname);
    });
})

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

function show_toastr(title, message, type) {
    var o, i;
    var icon = '';
    var cls = '';

    if (type == 'success') {
        icon = 'fas fa-check-circle';
        cls = 'success';
    } else {
        icon = 'fas fa-times-circle';
        cls = 'danger';
    }

    $.notify({icon: icon, title: " " + title, message: message, url: ""}, {
        element: "body",
        type: cls,
        allow_dismiss: !0,
        placement: {
            from: 'top',
            align: toster_pos
        },
        offset: {x: 15, y: 15},
        spacing: 10,
        z_index: 1080,
        delay: 2500,
        timer: 2000,
        url_target: "_blank",
        mouse_over: !1,
        animate: {enter: o, exit: i},
        template: '<div class="alert alert-{0} alert-icon alert-group alert-notify" data-notify="container" role="alert"><div class="alert-group-prepend alert-content"><span class="alert-group-icon"><i data-notify="icon"></i></span></div><div class="alert-content"><strong data-notify="title">{1}</strong><div data-notify="message">{2}</div></div><button type="button" class="close" data-notify="dismiss" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>'
    });
}

$(document).on('click', 'a[data-ajax-popup="true"], button[data-ajax-popup="true"], div[data-ajax-popup="true"]', function () {

    // return false;
    var title = $(this).data('title');
    var size = ($(this).data('size') == '') ? 'md' : $(this).data('size');
    var url = $(this).data('url');


    $("#commonModal .modal-title").html(title);
    $("#commonModal .modal-dialog").addClass('modal-' + size);
    $.ajax({
        url: url,
        success: function (data) {


            // alert('title');
            // console.log(data.length);
            // return false;
            if (data.length) {
                $('#commonModal .modal-body').html(data);
                $("#commonModal").modal('show');
                common_bind();
                // ddatetime_range();

                // common_bind_select();
            } else {
                show_toastr('Error', 'Permission denied', 'error');
                $("#commonModal").modal('hide');
            }
        },
        error: function (data) {

            data = data.responseJSON;
            show_toastr('Error', data.error, 'error');
        }
    });
});

$(document).on('click', '.fc-day-grid-event', function(e) {
    // if (!$(this).hasClass('project')) {
    e.preventDefault();
    var event = $(this);
    var title = $(this).find('.fc-content .fc-title').html();
    var size = 'md';
    var url = $(this).attr('href');
    $("#commonModal .modal-title").html(title);
    $("#commonModal .modal-dialog").addClass('modal-' + size);
    $.ajax({
        url: url,
        success: function(data) {
            $('#commonModal .modal-body').html(data);
            $("#commonModal").modal('show');
            common_bind();
        },
        error: function(data) {
            data = data.responseJSON;
            toastrs('Error', data.error, 'error')
        }
    });
    // }
});



(function ($, window, i) {
    // Bootstrap 4 Modal
    $.fn.fireModal = function (options) {
        var options = $.extend({
            size: 'modal-md',
            center: false,
            animation: true,
            title: 'Modal Title',
            closeButton: false,
            header: true,
            bodyClass: '',
            footerClass: '',
            body: '',
            buttons: [],
            autoFocus: true,
            created: function () {
            },
            appended: function () {
            },
            onFormSubmit: function () {
            },
            modal: {}
        }, options);
        this.each(function () {
            i++;
            var id = 'fire-modal-' + i,
                trigger_class = 'trigger--' + id,
                trigger_button = $('.' + trigger_class);
            $(this).addClass(trigger_class);
            // Get modal body
            let body = options.body;
            if (typeof body == 'object') {
                if (body.length) {
                    let part = body;
                    body = body.removeAttr('id').clone().removeClass('modal-part');
                    part.remove();
                } else {
                    body = '<div class="text-danger">Modal part element not found!</div>';
                }
            }
            // Modal base template
            var modal_template = '   <div class="modal' + (options.animation == true ? ' fade' : '') + '" tabindex="-1" role="dialog" id="' + id + '">  ' +
                '     <div class="modal-dialog ' + options.size + (options.center ? ' modal-dialog-centered' : '') + '" role="document">  ' +
                '       <div class="modal-content">  ' +
                ((options.header == true) ?
                    '         <div class="modal-header">  ' +
                    '           <h5 class="modal-title mx-auto">' + options.title + '</h5>  ' +
                    ((options.closeButton == true) ?
                        '           <button type="button" class="close" data-dismiss="modal" aria-label="Close">  ' +
                        '             <span aria-hidden="true">&times;</span>  ' +
                        '           </button>  '
                        : '') +
                    '         </div>  '
                    : '') +
                '         <div class="modal-body text-center text-dark">  ' +
                '         </div>  ' +
                (options.buttons.length > 0 ?
                    '         <div class="modal-footer mx-auto">  ' +
                    '         </div>  '
                    : '') +
                '       </div>  ' +
                '     </div>  ' +
                '  </div>  ';
            // Convert modal to object
            var modal_template = $(modal_template);
            // Start creating buttons from 'buttons' option
            var this_button;
            options.buttons.forEach(function (item) {
                // get option 'id'
                let id = "id" in item ? item.id : '';
                // Button template
                this_button = '<button type="' + ("submit" in item && item.submit == true ? 'submit' : 'button') + '" class="' + item.class + '" id="' + id + '">' + item.text + '</button>';
                // add click event to the button
                this_button = $(this_button).off('click').on("click", function () {
                    // execute function from 'handler' option
                    item.handler.call(this, modal_template);
                });
                // append generated buttons to the modal footer
                $(modal_template).find('.modal-footer').append(this_button);
            });
            // append a given body to the modal
            $(modal_template).find('.modal-body').append(body);
            // add additional body class
            if (options.bodyClass) $(modal_template).find('.modal-body').addClass(options.bodyClass);
            // add footer body class
            if (options.footerClass) $(modal_template).find('.modal-footer').addClass(options.footerClass);
            // execute 'created' callback
            options.created.call(this, modal_template, options);
            // modal form and submit form button
            let modal_form = $(modal_template).find('.modal-body form'),
                form_submit_btn = modal_template.find('button[type=submit]');
            // append generated modal to the body
            $("body").append(modal_template);
            // execute 'appended' callback
            options.appended.call(this, $('#' + id), modal_form, options);
            // if modal contains form elements
            if (modal_form.length) {
                // if `autoFocus` option is true
                if (options.autoFocus) {
                    // when modal is shown
                    $(modal_template).on('shown.bs.modal', function () {
                        // if type of `autoFocus` option is `boolean`
                        if (typeof options.autoFocus == 'boolean')
                            modal_form.find('input:eq(0)').focus(); // the first input element will be focused
                        // if type of `autoFocus` option is `string` and `autoFocus` option is an HTML element
                        else if (typeof options.autoFocus == 'string' && modal_form.find(options.autoFocus).length)
                            modal_form.find(options.autoFocus).focus(); // find elements and focus on that
                    });
                }
                // form object
                let form_object = {
                    startProgress: function () {
                        modal_template.addClass('modal-progress');
                    },
                    stopProgress: function () {
                        modal_template.removeClass('modal-progress');
                    }
                };
                // if form is not contains button element
                if (!modal_form.find('button').length) $(modal_form).append('<button class="d-none" id="' + id + '-submit"></button>');
                // add click event
                form_submit_btn.click(function () {
                    modal_form.submit();
                });
                // add submit event
                modal_form.submit(function (e) {
                    // start form progress
                    form_object.startProgress();
                    // execute `onFormSubmit` callback
                    options.onFormSubmit.call(this, modal_template, e, form_object);
                });
            }
            $(document).on("click", '.' + trigger_class, function () {
                $('#' + id).modal(options.modal);
                return false;
            });
        });
    }
    // Bootstrap Modal Destroyer
    $.destroyModal = function (modal) {
        modal.modal('hide');
        modal.on('hidden.bs.modal', function () {
        });
    }
})(jQuery, this, 0);

$('[data-confirm]').each(function () {

    var me = $(this),
        me_data = me.data('confirm');

    me_data = me_data.split("|");
    me.fireModal({
        title: me_data[0],
        body: me_data[1],
        buttons: [
            {
                text: me.data('confirm-text-yes') || 'Yes',
                class: 'btn btn-sm btn-danger rounded-pill',
                handler: function () {
                    eval(me.data('confirm-yes'));
                }
            },
            {
                text: me.data('confirm-text-cancel') || 'Cancel',
                class: 'btn btn-sm btn-secondary rounded-pill',
                handler: function (modal) {
                    $.destroyModal(modal);
                    eval(me.data('confirm-no'));
                }
            }
        ]
    })
});

function common_bind() {


    if ($(".datepicker").length) {
        /*$('.datepicker').daterangepicker({
            singleDatePicker: true,
            minDate: '01-01-0000',
            format: 'YYYY-MM-DD',
            locale: {
                date_picker_locale,
                format: 'YYYY-MM-DD'
            },
            autoUpdateInput: false
            // locale: date_picker_locale,
        });*/
        // alert('zasdas');
        $('.datepicker').daterangepicker({
            singleDatePicker: true,
            minDate: '0000-01-01',
            format: 'YYYY-MM-DD',
            locale: {
                // date_picker_locale,
                format: 'YYYY-MM-DD'
            },
            singleDatePicker: true,
            autoUpdateInput: false  // This prevents auto-filling the input
        });
    
        // Handle the date selection to update input only when a date is chosen
        $('.datepicker').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD'));
            $(this).trigger('change');
        });
    
        // Handle cancel to clear the input
        $('.datepicker').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
        
        $('.datepicker').on('focus', function() {
            $(this).data('daterangepicker').show();
        });
        
        $('.datepicker').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD'));
        });
    }


    if (jQuery().select2) {
        $(".select2").select2({
            disableOnMobile: false,
            nativeOnMobile: false
        });
    }

    $('.timepicker').timepicker({
        showMeridian: false,
        minuteStep: 5,
    });


    if ($(".custom-datepicker").length) {
        $('.custom-datepicker').daterangepicker({
            singleDatePicker: true,
            format: 'Y-MM',
            minDate: new Date(),
            locale: {
                format: 'Y-MM'
            }
        });

    }


}

function toggleSalary() {
    var hiddenSalary = document.getElementById('hidden-salary');
    var actualSalary = document.getElementById('actual-salary');
    var eyeIcon = document.getElementById('eye-icon');

    if (hiddenSalary.style.display === "none") {
        hiddenSalary.style.display = "inline";
        actualSalary.style.display = "none";
        eyeIcon.classList.remove('fa-eye-slash');
        eyeIcon.classList.add('fa-eye');
    } else {
        hiddenSalary.style.display = "none";
        actualSalary.style.display = "inline";
        eyeIcon.classList.remove('fa-eye');
        eyeIcon.classList.add('fa-eye-slash');
    }
}
$(document).ready(function() {
    
    $('#department_id').on('change', function() {
         var d_id = $('#department_id').val();
        AjaxRequest();
        getDesignation(d_id);
    });

    $('#is_team_leader').on('change', function() {
        if ($(this).is(':checked')) {
            $('#team_leader').empty();
            $('#hidden_team_leader').val('');
            $('#is_team_leader').val('1');
        } else {
            AjaxRequest();
        }
    });

    function AjaxRequest() {
        var branchId = $('#branch_id').val(); 
        var departmentId = $('#department_id').val();
        var url = window.location.href;
        var newURl = url.split('/employee/')[0] + '/employee/get-team-leader';
        if (branchId && departmentId) {
            $.ajax({
                url: newURl, 
                type: 'POST',
                data: {
                    branchId: branchId,
                    departmentId: departmentId
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
                },
                success: function(response) {
                    $('#team_leader').empty();
                    if (response && response.length > 0) {
                        $.each(response, function(index, leader) {
                            $('#team_leader').append('<option value="' + leader.id + '">' + leader.name + '</option>');
                            $('#hidden_team_leader').val(leader.id);
                        });
                    } else {
                        $('#team_leader').append('<option value="">No Team Leaders Available</option>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    $('#team_leader_data').html('<p>Error loading data.</p>');
                }
            });
        } else {
            $('#team_leader').empty();
        }
    }
    
    function getDesignation(department_id) {
        var url = window.location.href;
        var newURl = url.split('/employee/')[0] + '/employee/json';
        $.ajax({
            url: newURl,
            type: 'POST',
            data: {
                "department_id": department_id,
            },
            headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
            },
            success: function(data) {
                $('#designation_id').empty();
                $('#designation_id').append(
                '<option value="">{{ __("Select any Designation") }}</option>');
                $.each(data, function(key, value) {
                    $('#designation_id').append('<option value="' + key + '">' + value +
                        '</option>');
                });
            }
        });
    }
});

$(document).ready(function () {
    $(document).on('click', '#rejectBtn', function (e) {
        e.preventDefault(); 
        $('#rejectReasonDiv').show();
        $('#reject_reason').prop('required', true); 
    
        $('#reason-error').remove();
    
        if ($('#reject_reason').val().trim() !== '') {
            $('<input>').attr({
                type: 'hidden',
                name: 'status',
                value: 'Reject'
            }).appendTo('#leaveForm');
            $('#leaveForm').submit();
        } else {
            $('<span id="reason-error" class="text-danger">Please provide a reason for rejection.</span>').insertAfter('#reject_reason');

            setTimeout(function () {
                $('#reason-error').fadeOut('slow', function () {
                    $(this).remove(); 
                });
            }, 3000);
        }
    });

    $(document).on('click', '#approvalBtn', function () {
        $('<input>').attr({
            type: 'hidden',
            name: 'status',
            value: 'Approval'
        }).appendTo('#leaveForm');
        $('#leaveForm').submit();
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const togglePassword = document.querySelector('.toggle-password');
    const passwordInput = document.getElementById('password');
    
    togglePassword.addEventListener('click', function() {
        // Toggle the type attribute between password and text
        const type = passwordInput.type === 'password' ? 'text' : 'password';
        passwordInput.type = type;
        
        // Toggle the eye icon (change it to "eye-slash" when password is visible)
        this.querySelector('i').classList.toggle('fa-eye-slash');
    });
});

document.querySelectorAll('.read-more-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
        // Get the full description from the data attribute
        var fullDescription = this.getAttribute('data-description');
        
        // Set the full description inside the modal
        document.getElementById('modal-description').innerText = fullDescription;

        // Show the modal (using Bootstrap 5 modal API)
        var modal = new bootstrap.Modal(document.getElementById('descriptionModal'));
        modal.show();
    });
});
