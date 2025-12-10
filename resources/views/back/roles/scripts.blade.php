<script>
    // This file is kept for backward compatibility but scripts are now in pushscripts.blade.php
    // jQuery will be loaded after this, so we use window.onload
    window.addEventListener('load', function() {
        if (typeof jQuery !== 'undefined') {
            jQuery(document).ready(function($){
        //============================================= LOADER
        var $loading = $('#loading').hide();
        $(document)
        .ajaxStart(function () {
            $loading.show();
        })
        .ajaxStop(function () {
            $loading.hide();
        });
        
        //============================================= AJAX REQUEST FOR SHOWING ADD NEW RECORD MODAL
        $(document).on('click', "#add_btn", function (e) {
            e.preventDefault();
            let url = $(this).attr("href");
            let title = $(this).attr("data-title");
            $("#modal-title").html(title);
            $.ajax({
                url: url,
                method: "get",
                success: function (data) {
                    $("#modal-body").html(data);
                },
                error: function() {
                    alert("Please try again ... ");
                }
            });
        });

        //============================================= AJAX REQUEST FOR ADDING RECORD
        $(document).on('click', "#submit_add_form", function (e) {
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
                success: function(data){
                    $('#add_form').find('small').remove();
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
        
                        setTimeout(function () {
                            let rolesIndexUrl = "{{ route('back.roles.index') }}";
                            window.location.href = rolesIndexUrl;
                        }, 1000);
                    }
                },
                error: function(xhr, status, error){
                    console.error("AJAX Error:", status, error);
                    console.error("Response:", xhr.responseText);
                    
                    $('#add_form').find('small').remove();
                    $("#add_form_messages").empty().removeClass();
                    
                    try {
                        if (xhr && xhr.responseJSON && xhr.responseJSON.errors) {
                            $.each(xhr.responseJSON.errors, function( key, value ) {
                                let el = $('[name="'+key+'"]').first();
                                if (el.length) {
                                    el.after($('<small class="text-danger d-block">'+value[0]+'</small>'));
                                }
                            });
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
            }); 
        });

        //============================================= AJAX REQUEST FOR SHOWING DELETE MODAL
        $(document).on('click', ".deleteClass", function (e) {
            let url = $(this).attr("href");
            let title = $(this).attr("data-title");
            $("#delete-modal-title").html(title);
            $("#submit_delete").attr("href" , url );
        });

        //============================================= AJAX REQUEST FOR DELETING RECORD
        $(document).on('click', "#submit_delete", function (e) {  
            e.preventDefault();
            let url = $(this).attr("href");
            $.ajax({
                url: url ,
                method: 'DELETE' ,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: "json",
                success: function(data){
                    $("#delete_alert_div").removeClass().addClass('alert alert-success').append(data['success']) ; 
                    
                    setTimeout(function(){
                        let rolesIndexUrl = "{{ route('back.roles.index') }}";
                        $("#mainCont").load(rolesIndexUrl + " #mainCont > *");
                        $("#deleteModal").load(" #deleteModal > *");
                        $('#deleteModal').modal('toggle');
                    }, 1000);
                },
                error: function(){
                    alert("Please try again ...");
                }
            }) ;
        });

        //============================================= SCRIPT FOR SHOWING EDITING MODAL
        $(document).on('click', ".editClass", function (e) {
            e.preventDefault();
            let url = $(this).attr("href");
            let title = $(this).attr("data-title");
            $("#modal-title").html(title);
            $.ajax({
                url: url,
                method: "get",
                success: function(data){
                    $("#modal-body").html(data);
                },
                error: function(){
                    alert("Please try again ... ");
                }
            });
        });

        //============================================= AJAX REQUEST FOR UPDATING
        $(document).on('click', "#submit_edit_form", function (e) {
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
                success: function(data){
                    $('#edit_form').find('small').remove();
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
                        
                        setTimeout(function(){
                            let rolesIndexUrl = "{{ route('back.roles.index') }}";
                            $("#mainCont").load(rolesIndexUrl + " #mainCont > *");
                            $("#edit_form_messages").empty().removeClass();
                            $("#mainModal").modal('hide');
                        }, 1000);
                    }
                },
                error: function(xhr, status, error){
                    console.error("AJAX Error:", status, error);
                    console.error("Response:", xhr.responseText);
                    
                    $('#edit_form').find('small').remove();
                    $("#edit_form_messages").empty().removeClass();
                    
                    try {
                        if (xhr && xhr.responseJSON && xhr.responseJSON.errors) {
                            $.each(xhr.responseJSON.errors, function( key, value ) {
                                let el = $('[name="'+key+'"]').first();
                                if (el.length) {
                                    el.after($('<small class="text-danger d-block">'+value[0]+'</small>'));
                                }
                            });
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
                }
            }); 
        });
    
        //============================================= SCRIPT FOR DISPLAYING RECORD DETAILS ON MODAL
        $(document).on('click', ".displayClass", function (e) {
            e.preventDefault();
            let formAction = $(this).attr("href");
            let title = $(this).attr("data-title");
            $("#modal-title").html(title);
            $.ajax({
                url: formAction,
                method: "get",
                success: function(data){
                    $("#modal-body").html(data);
                },
                error: function(){
                    alert("failed .. Please try again !");
                }
            }) ;
        });

        //============================================= SCRIPT FOR SELECT ALL CHECKBOXES
        $(document).on('click', "#selectAll", function () {
            $('input[name^=permissionArray]:checkbox').not(this).prop('checked', this.checked);
        });
            });
        }
    });
</script>