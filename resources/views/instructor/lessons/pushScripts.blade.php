@push('scripts')
<script>
    $(document).ready(function() {
        var $loading = $('#loading').hide();
        $(document)
            .ajaxStart(function() {
                $loading.show();
            })
            .ajaxStop(function() {
                $loading.hide();
            });

        $(document).on('click', "#add_btn", function(e) {
            e.preventDefault();
            let url = $(this).attr("href");
            let title = $(this).attr("data-title");
            $("#modal-title").html(title);
            $.ajax({
                url: url,
                method: "get",
                success: function(data) {
                    $("#modal-body").html(data);
                },
                error: function() {
                    alert("Please try again ... ");
                }
            });
        });

        $(document).on('click', "#submit_add_form", function(e) {
            e.preventDefault();
            let theForm = $("#add_form");
            if (!theForm.length) {
                console.error("Form not found");
                return;
            }
            
            let formAction = theForm.attr('action');
            let formMethod = theForm.attr('method') || 'POST';
            let formData = new FormData($('#add_form')[0]);

            $.ajax({
                url: formAction,
                method: formMethod,
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: "json",
                success: function(data) {
                    $('#add_form').find('small').remove();
                    $('#add_form').find('.error').removeClass('error');
                    $("#add_form_messages").empty().removeClass();
                    
                    if (data && data['failed']) {
                        $("#add_form_messages")
                            .addClass('alert alert-danger')
                            .append(data['failed'])
                            .get(0).scrollIntoView({ behavior: 'instant', block: 'center' });
                    } else if (data && data['success']) {
                        $("#add_form_messages")
                            .addClass('alert alert-success')
                            .append(data['success'])
                            .get(0).scrollIntoView({ behavior: 'instant', block: 'center' });

                        setTimeout(function() {
                            $(':input', '#add_form')
                                .not(':button, :submit, :reset, :hidden')
                                .val('')
                                .prop('checked', false)
                                .prop('selected', false);
                            $("#add_form_messages").empty().removeClass();
                            const modalElement = document.getElementById('mainModal');
                            if (modalElement) {
                                const modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
                                modal.hide();
                            }
                            let lessonsIndexUrl = "{{ route('instructor.lessons.index') }}";
                            $("#mainCont").load(lessonsIndexUrl + " #mainCont > *");
                        }, 1000);
                    }
                },
                error: function(xhr, status, error){
                    console.error("AJAX Error:", status, error);
                    console.error("Response:", xhr.responseText);
                    
                    $('#add_form').find('small').remove();
                    $('#add_form').find('.error').removeClass('error');
                    $("#add_form_messages").empty().removeClass();
                    
                    try {
                        if (xhr && xhr.responseJSON && xhr.responseJSON.errors) {
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                let el = $('[name="' + key + '"]').first();
                                if (el.length) {
                                    el.addClass('error');
                                    el.after($('<small class="text-danger d-block">' + value[0] + '</small>'));
                                }
                            });
                            $('html, body').animate({
                                scrollTop: $('small:first').offset().top
                            }, 500);
                        } else if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                            $("#add_form_messages")
                                .addClass('alert alert-danger')
                                .append(xhr.responseJSON.message);
                        } else {
                            $("#add_form_messages")
                                .addClass('alert alert-danger')
                                .append('حدث خطأ. يرجى المحاولة مرة أخرى.');
                        }
                    } catch(e) {
                        console.error("Error handling:", e);
                        $("#add_form_messages")
                            .addClass('alert alert-danger')
                            .append('حدث خطأ. يرجى المحاولة مرة أخرى.');
                    }
                },
                beforeSend: function() {
                    $('#submit_add_form').attr('disabled', true);
                    $('#loading').removeClass('d-none');
                },
                complete: function() {
                    $('#submit_add_form').attr('disabled', false);
                    $('#loading').addClass('d-none');
                }
            });
        });

        $(document).on('click', ".deleteClass", function(e) {
            e.preventDefault();
            let url = $(this).attr("href");
            let title = $(this).attr("data-title");
            $("#delete-modal-title").html(title);
            $("#submit_delete").attr("href", url);
            $("#delete_alert_div").empty().removeClass();
            const deleteModalElement = document.getElementById('deleteModal');
            if (deleteModalElement) {
                const deleteModal = bootstrap.Modal.getInstance(deleteModalElement) || new bootstrap.Modal(deleteModalElement);
                deleteModal.show();
            }
        });

        $(document).on('click', "#submit_delete", function(e) {
            e.preventDefault();
            let url = $(this).attr("href");
            if (!url) {
                console.error("Delete URL not found");
                return;
            }
            $.ajax({
                url: url,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: "json",
                success: function(data) {
                    $("#delete_alert_div").empty().removeClass().addClass('alert alert-success').html(data['success']);

                    setTimeout(function() {
                        let lessonsIndexUrl = "{{ route('instructor.lessons.index') }}";
                        window.location.href = lessonsIndexUrl;
                    }, 1000);
                },
                error: function(xhr, status, error){
                    console.error("Delete error:", status, error);
                    $("#delete_alert_div").empty().removeClass().addClass('alert alert-danger').html('حدث خطأ. يرجى المحاولة مرة أخرى.');
                },
                beforeSend: function() {
                    $('#submit_delete').attr('disabled', true);
                    $('#loading').removeClass('d-none');
                },
                complete: function() {
                    $('#submit_delete').attr('disabled', false);
                    $('#loading').addClass('d-none');
                }
            });
        });

        $(document).on('click', ".editClass", function(e) {
            e.preventDefault();
            let url = $(this).attr("href");
            let title = $(this).attr("data-title");
            $("#modal-title").html(title);
            $.ajax({
                url: url,
                method: "get",
                success: function(data) {
                    $("#modal-body").html(data);
                },
                error: function() {
                    alert("Please try again ... ");
                }
            });
        });

        $(document).on('click', "#submit_edit_form", function(e) {
            e.preventDefault();
            let theForm = $("#edit_form");
            if (!theForm.length) {
                console.error("Edit form not found");
                return;
            }
            
            let formAction = theForm.attr('action');
            let formMethod = theForm.attr('method') || 'POST';
            let formData = new FormData($('#edit_form')[0]);

            $.ajax({
                url: formAction,
                method: formMethod,
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: "json",
                success: function(data) {
                    $('#edit_form').find('small').remove();
                    $('#edit_form').find('.error').removeClass('error');
                    $("#edit_form_messages").empty().removeClass();
                    
                    if (data && data['failed']) {
                        $("#edit_form_messages")
                            .addClass('alert alert-danger')
                            .append(data['failed'])
                            .get(0).scrollIntoView({ behavior: 'instant', block: 'center' });
                    } else if (data && data['success']) {
                        $("#edit_form_messages")
                            .addClass('alert alert-success')
                            .append(data['success'])
                            .get(0).scrollIntoView({ behavior: 'instant', block: 'center' });

                        setTimeout(function() {
                            let lessonsIndexUrl = "{{ route('instructor.lessons.index') }}";
                            $("#mainCont").load(lessonsIndexUrl + " #mainCont > *");
                            $("#edit_form_messages").empty().removeClass();
                            const modalElement = document.getElementById('mainModal');
                            if (modalElement) {
                                const modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
                                modal.hide();
                            }
                        }, 1000);
                    }
                },
                error: function(xhr, status, error){
                    console.error("AJAX Error:", status, error);
                    console.error("Response:", xhr.responseText);
                    
                    $('#edit_form').find('small').remove();
                    $('#edit_form').find('.error').removeClass('error');
                    $("#edit_form_messages").empty().removeClass();
                    
                    try {
                        if (xhr && xhr.responseJSON && xhr.responseJSON.errors) {
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                let el = $('[name="' + key + '"]').first();
                                if (el.length) {
                                    el.addClass('error');
                                    el.after($('<small class="text-danger d-block">' + value[0] + '</small>'));
                                }
                            });
                            $('html, body').animate({
                                scrollTop: $('small:first').offset().top
                            }, 500);
                        } else if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                            $("#edit_form_messages")
                                .addClass('alert alert-danger')
                                .append(xhr.responseJSON.message);
                        } else {
                            $("#edit_form_messages")
                                .addClass('alert alert-danger')
                                .append('حدث خطأ. يرجى المحاولة مرة أخرى.');
                        }
                    } catch(e) {
                        console.error("Error handling:", e);
                        $("#edit_form_messages")
                            .addClass('alert alert-danger')
                            .append('حدث خطأ. يرجى المحاولة مرة أخرى.');
                    }
                },
                beforeSend: function() {
                    $('#submit_edit_form').attr('disabled', true);
                    $('#loading').removeClass('d-none');
                },
                complete: function() {
                    $('#submit_edit_form').attr('disabled', false);
                    $('#loading').addClass('d-none');
                }
            });
        });

        $(document).on('click', ".displayClass", function(e) {
            e.preventDefault();
            let formAction = $(this).attr("href");
            let title = $(this).attr("data-title");
            $("#modal-title").html(title);
            $.ajax({
                url: formAction,
                method: "get",
                success: function(data) {
                    $("#modal-body").html(data);
                },
                error: function() {
                    alert("failed .. Please try again !");
                }
            });
        });

        const videoInputs = document.querySelectorAll('input[type="file"][name="video_file"]');
        videoInputs.forEach(input => {
            input.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        let preview = input.closest('.form-group').querySelector('.video-preview');
                        if (!preview) {
                            preview = document.createElement('video');
                            preview.className = 'video-preview img-thumbnail mt-2';
                            preview.controls = true;
                            preview.style.maxWidth = '300px';
                            preview.style.maxHeight = '200px';
                            input.closest('.form-group').appendChild(preview);
                        }
                        preview.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
        
        if (typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
            document.querySelectorAll('.dropdown-toggle').forEach(function(element) {
                try {
                    new bootstrap.Dropdown(element);
                } catch(e) {
                    console.log('Dropdown already initialized or error:', e);
                }
            });
        } else if (typeof $ !== 'undefined' && $.fn.dropdown) {
            $('.dropdown-toggle').dropdown();
        }
    });
</script>
@endpush

