$(document).ready(function(){

    // Get Event ID
    var eventID = document.getElementById('current-event-ID').value;

    $('#crud-successful').hide();

    // Load Invitee List
    var inviteeData = $('#inviteeList').removeAttr('width').DataTable({
        "lengthChange": false,
        "processing":true,
        "serverSide":true,
        "order":[],
        "ajax":{
            url:"invitee-action?eventID="+eventID,
            type:"POST",
            data:{inviteeAction:'listInvitee'},
            dataType:"json",
            complete: function(data) {
                $('#sendSelectedInvitations, #deleteSelectedInvitations').hide();
                $("#selectAllInvitees").prop('checked',false);
                $('#inviteeList_paginate').addClass('mb-2');
                $("#inviteeList").css("width", "1078px");
                $("#inviteeView").css("width", "8%");
                $("#inviteeID").css("width", "9%");
                $("#inviteeFName").css("width", "15%");
                $("#inviteeMName").css("width", "15%");
                $("#inviteeLName").css("width", "13%");
                $("#inviteeType").css("width", "5%");
                $("#inviteeMore").css("width", "5%");
                $("#inviteeCheckBox").css("width", "1%");
                document.getElementById("totalInvitees").innerText = data.responseJSON.recordsTotal;
            }
        },
        "columnDefs":[
            {
                "targets":[0, 6, 7],
                "orderable":false
            },
            {
                "className": "text-center",
                "targets": [7]
            }
        ],
        'columns': [
            { data: "view" },
            { data: "ID" },
            { data: "firstname" },
            { data: "middlename" },
            { data: "lastname" },
            { data: "type" },
            { data: "delete" },
            { data: "checkbox" }
         ],
        // "searching": false,
        "pageLength": 10,
        "dom": 'Bfrtip',
        "buttons": [
            {
                extend: 'collection',
                text: '<i class="fas fa-file-export"></i> Export',
                className: "float-left",
                buttons: [
                    {
                        extend: 'excelHtml5',
                        exportOptions: {
                            columns: [ 1, 2, 3, 4, 5 ]
                        },
                        title: document.getElementById("current-event-title").innerHTML + " Invitees\' Details List"
                    },
                    {
                        extend: 'csvHtml5',
                        exportOptions: {
                            columns: [ 1, 2, 3, 4, 5 ]
                        },
                        title: document.getElementById("current-event-title").innerHTML + " Invitees\' Details List"
                    },
                    {
                        extend: 'pdfHtml5',
                        exportOptions: {
                            columns: [ 1, 2, 3, 4, 5 ]
                        },
                        title: document.getElementById("current-event-title").innerHTML + " Invitees\' Details List"
                    },
                    {
                        extend: 'print',
                        exportOptions: {
                            columns: [ 1, 2, 3, 4, 5 ]
                        },
                        title: document.getElementById("current-event-title").innerHTML + " Invitees\' Details List"
                    }
                ]
            }
        ]
    });

    // Select All Invitees using checkbox
    $("#selectAllInvitees").click(function() {
        $(this).closest('table').find('td input:checkbox').prop('checked', this.checked);
        if ($(this).is(":checked")){
            inviteeData.rows().select();
        } else {
            inviteeData.rows().deselect();
        }
        selectedCheckboxes();
    });

    // Selected Checkbox Row
    $("#inviteeList").on('click', '.selectInvitee', function(){
        $("#selectAllInvitees").prop('checked',false);
        var selectedIndex = $(this).closest("tr").index();
        if ($(this).is(":checked")){
            inviteeData.row(selectedIndex).select();
        } else {
            inviteeData.row(selectedIndex).deselect();
        }
        selectedCheckboxes();
    });

    // Show or Hide Send and Delete Buttons provided that selected at least one row
    function selectedCheckboxes() {
        var rowCount = 0;
        $('#inviteeList .selectInvitee:checked').each(function() {
            rowCount++;
        });
        if (rowCount > 0) {
            $('#sendSelectedInvitations, #deleteSelectedInvitations').show();
        } else {
            $('#sendSelectedInvitations, #deleteSelectedInvitations').hide();
        }
        
    }

    // Add Invitee Function
    $('#addInvitee').click(function(){
        $('#inviteeForm')[0].reset();
        $('.add-edit-invitee-title').html("<i class='fa fa-plus-circle'></i> Add Invitee");
        // $("#inviteeSave").removeClass("btn-warning");
        // $("#inviteeSave").addClass("btn-succes");
        // $('#inviteeSave').html("<i class='fa fa-plus'></i> Add");
        $('#inviteeAction').val('addInvitee');
        $('#inviteeSave').val('Add');
    });

    // Send Selected Invitation
    $("#sendSelectedInvitations").click(function() {
        var selectedRows = new Array();
        $('#inviteeList .selectInvitee:checked').each(function() {
            selectedRows.push($(this).attr('value'));
        });
        if (selectedRows.length > 0) {
            $.ajax({
                url:"invitee-action?eventID="+eventID,
                method:"POST",
                data:{selectedInviteeIDs: JSON.stringify(selectedRows), inviteeAction:'sendSelectedInvitation'},
                dataType:"json",
                beforeSend: function(){
                    $("#loadingModal").modal('show');
                }
            })
            .done(function(data) {
                $("#loadingModal").modal('hide');
                $("#selectAllInvitees").closest('table').find('td input:checkbox').prop('checked', false);
                inviteeData.rows().deselect();
                $('#sendSelectedInvitations, #deleteSelectedInvitations').hide();
                if (data.Status == "success") {
                    $("#crud-successful").removeClass("bg-succes");
                    $("#crud-successful").removeClass("bg-warning");
                    $("#crud-successful").removeClass("bg-danger");
                    $("#crud-successful").addClass("bg-succes");
                    $('#crud-successful').html("<h5>SEND EMAIL INVITATIONS SUCCESSFULLY</h5>");
                    $('#crud-successful').show().delay(1000).fadeOut();
                }else{
                    $("#crud-successful").removeClass("bg-succes");
                    $("#crud-successful").removeClass("bg-warning");
                    $("#crud-successful").removeClass("bg-danger");
                    $("#crud-successful").addClass("bg-danger");
                    $('#crud-successful').html("<h5>SEND EMAIL INVITATIONS FAILED</h5>");
                    $('#crud-successful').show().delay(1000).fadeOut();
                }
                console.log("Success: " + data.Status);
            })
            .fail(function() {
                $("#loadingModal").modal('hide');
                $("#crud-successful").removeClass("bg-succes");
                $("#crud-successful").removeClass("bg-warning");
                $("#crud-successful").removeClass("bg-danger");
                $("#crud-successful").addClass("bg-danger");
                $('#crud-successful').html("<h5>SEND EMAILS INVITATION ERROR</h5>");
                $('#crud-successful').show().delay(1000).fadeOut();
                console.log("Error");
            });
        }
    });

    // Delete Selected Invitees
    $("#deleteSelectedInvitations").click(function() {
        var selectedRows = new Array();
        $('#inviteeList .selectInvitee:checked').each(function() {
            selectedRows.push($(this).attr('value'));
        });
        if (selectedRows.length > 0) {
            $.ajax({
                url:"invitee-action?eventID="+eventID,
                method:"POST",
                data:{selectedInviteeIDs: JSON.stringify(selectedRows), inviteeAction:'getSelectedInvitees'},
                dataType:"json",
                beforeSend: function(){
                    $("#loadingModal").modal('show');
                }
            })
            .done(function(data) {
                $("#loadingModal").modal('hide');
                if (data.Status == "success") {
                    var strSelected = "";
                    for (var i = 0; i < data.selectedData.length; i++) {
                        strSelected += "<li>" + data.selectedData[i] + "</li>";
                    }
                    $("#selected-invitees-deletion").html(strSelected);
                    $('#deleteSelectedInviteeModal').modal('show');
                } else {
                    $("#crud-successful").removeClass("bg-succes");
                    $("#crud-successful").removeClass("bg-warning");
                    $("#crud-successful").removeClass("bg-danger");
                    $("#crud-successful").addClass("bg-danger");
                    $('#crud-successful').html("<h5>DELETE INVITEE ERROR</h5>");
                    $('#crud-successful').show().delay(1000).fadeOut();    
                }
            })
            .fail(function() {
                $("#loadingModal").modal('hide');
                $("#crud-successful").removeClass("bg-succes");
                $("#crud-successful").removeClass("bg-warning");
                $("#crud-successful").removeClass("bg-danger");
                $("#crud-successful").addClass("bg-danger");
                $('#crud-successful').html("<h5>DELETE INVITEE ERROR</h5>");
                $('#crud-successful').show().delay(1000).fadeOut();
            });
        }
        
    });

    // Submit Delete Selected Invitee Form
    $("#deleteSelectedInviteeModal").on('submit', '#inviteeSelectedDeleteForm', function(event) {
        event.preventDefault();
        var selectedRows = new Array();
        $('#inviteeList .selectInvitee:checked').each(function() {
            selectedRows.push($(this).attr('value'));
        });
        if (selectedRows.length > 0) {
            $.ajax({
                url:"invitee-action?eventID="+eventID,
                method:"POST",
                data:{selectedInviteeIDs: JSON.stringify(selectedRows), inviteeAction:'deleteSelectedInvitees'},
                dataType:"json",
                beforeSend: function(){
                    $("#loadingModal").modal('show');
                }
            })
            .done(function(data) {
                $("#loadingModal").modal('hide');
                $("#selectAllInvitees").closest('table').find('td input:checkbox').prop('checked', false);
                $("#crud-successful").removeClass("bg-succes");
                $("#crud-successful").removeClass("bg-warning");
                $("#crud-successful").removeClass("bg-danger");
                $("#crud-successful").addClass("bg-danger");
                if (data.Status == "success") {
                    $('#crud-successful').html("<h5>DELETE SELECTED INVITEE/S SUCCESSFULLY</h5>");
                }else{
                    $('#crud-successful').html("<h5>DELETE SELECTED INVITEE/S FAILED</h5>");
                }
                $('#deleteSelectedInviteeModal').modal('hide');
                $('#crud-successful').show().delay(1000).fadeOut();
                inviteeData.ajax.reload();
            })
            .fail(function() {
                $("#loadingModal").modal('hide');
                $("#crud-successful").removeClass("bg-succes");
                $("#crud-successful").removeClass("bg-warning");
                $("#crud-successful").removeClass("bg-danger");
                $("#crud-successful").addClass("bg-danger");
                $('#crud-successful').html("<h5>DELETE SELECTED INVITEE/S ERROR</h5>");
                $('#crud-successful').show().delay(1000).fadeOut();
            });
        }
    });

    // Submit Add or Edit Invitee Form
    $("#addEditInviteeModal").on('submit','#inviteeForm', function(event){
        event.preventDefault();
        // $('#inviteeSave').attr('disabled','disabled');
        var formData = $(this).serialize();
        // console.log(formData);
        if (addEditInviteeValidation() == true) {
            $.ajax({
                url:"invitee-action?eventID="+eventID,
                method:"POST",
                data:formData,
                beforeSend: function(){
                    $("#loadingModal").modal('show');
                },
                success:function(data){
                    $("#loadingModal").modal('hide');
                    $("#crud-successful").removeClass("bg-succes");
                    $("#crud-successful").removeClass("bg-warning");
                    $("#crud-successful").removeClass("bg-danger");
                    if ($("#inviteeAction").val() == "addInvitee") {
                        if (JSON.parse(data).Status == "nameAlreadyExists") {
                            $("#crud-successful").addClass("bg-warning");
                            $('#crud-successful').html("<h5>NAME OF INVITEE ALREADY EXISTS</h5>");
                        } else if (JSON.parse(data).Status == "emailAlreadyExists") {
                            $("#crud-successful").addClass("bg-warning");
                            $('#crud-successful').html("<h5>EMAIL OF INVITEE ALREADY EXISTS</h5>");
                        } else if (JSON.parse(data).Status == "phoneNumAlreadyExists") {
                            $("#crud-successful").addClass("bg-warning");
                            $('#crud-successful').html("<h5>PHONE NUMBER OF INVITEE ALREADY EXISTS</h5>");
                        } else if (JSON.parse(data).Status == "error") {
                            $("#crud-successful").addClass("bg-danger");
                            $('#crud-successful').html("<h5>ADD INVITEE FAILED</h5>");
                        } else {
                            $("#crud-successful").addClass("bg-succes");
                            $('#crud-successful').html("<h5>ADD INVITEE SUCCESSFULLY</h5>");
                        }
                    }else{
                        if (JSON.parse(data).Status == "nameAlreadyExists") {
                            $("#crud-successful").addClass("bg-warning");
                            $('#crud-successful').html("<h5>NAME OF INVITEE ALREADY EXISTS</h5>");
                        } else if (JSON.parse(data).Status == "emailAlreadyExists") {
                            $("#crud-successful").addClass("bg-warning");
                            $('#crud-successful').html("<h5>EMAIL OF INVITEE ALREADY EXISTS</h5>");
                        } else if (JSON.parse(data).Status == "phoneNumAlreadyExists") {
                            $("#crud-successful").addClass("bg-warning");
                            $('#crud-successful').html("<h5>PHONE NUMBER OF INVITEE ALREADY EXISTS</h5>");
                        } else if (JSON.parse(data).Status == "success") {
                            $("#crud-successful").addClass("bg-warning");
                            $('#crud-successful').html("<h5>EDIT INVITEE SUCCESSFULLY</h5>");
                        } else {
                            $("#crud-successful").addClass("bg-danger");
                            $('#crud-successful').html("<h5>EDIT INVITEE FAILED</h5>");
                        }
                    }
                    $('#inviteeForm')[0].reset();
                    $('#addEditInviteeModal').modal('hide');
                    $('#crud-successful').show().delay(1000).fadeOut();
                    // $('#inviteeSave').attr('disabled', false);
                    inviteeData.ajax.reload();
                }
            })
            .fail(function() {
                $("#loadingModal").modal('hide');
                $("#crud-successful").removeClass("bg-succes");
                $("#crud-successful").removeClass("bg-warning");
                $("#crud-successful").removeClass("bg-danger");
                $("#crud-successful").addClass("bg-danger");
                if ($("#inviteeAction").val() == "addInvitee") {
                    $('#crud-successful').html("<h5>ADD INVITEE ERROR</h5>");   
                }else{
                    $('#crud-successful').html("<h5>EDIT INVITEE ERROR</h5>");   
                }
                $('#inviteeForm')[0].reset();
                $('#addEditInviteeModal').modal('hide');
                $('#crud-successful').show().delay(1000).fadeOut();
                console.log("Error");
            });
        }    
    });

    // View Invitee Function
    $("#inviteeList").on('click', '.viewInvitee', function(){
        var inviteeID = $(this).attr("id");
        var inviteeAction = 'getInvitee';
        $.ajax({
            url:"invitee-action?eventID="+eventID,
            method:"POST",
            data:{inviteeID:inviteeID, inviteeAction:inviteeAction},
            dataType:"json",
            beforeSend: function(){
                $("#loadingModal").modal('show');
            },
            success:function(data){
                $("#loadingModal").modal('hide');
                $('#viewInviteeModalTitleName').html(data.firstname + " " + data.middlename + " " + data.lastname);
                $('#viewInviteeEmail').html('<a href="mailto:' + data.email + '">' + data.email + '</a>');
                $('#viewInviteePhoneNum').html('<a href="tel:' + data.phonenum + '">' + data.phonenum + '</a>');
                $('#viewInviteeType').html(data.type);
                $('#viewInviteeCode').html(data.invitee_code);
                $('#viewDateTimeAdded').html(formatDateTime(data.datetime_added));
                $("#viewInviteeBarcode").attr("src",'data:image/png;base64,' + data.base64IVT);
                $('#viewInviteeModal').modal('show');
            }
        })
        .fail(function() {
            $("#loadingModal").modal('hide');
            $("#crud-successful").removeClass("bg-succes");
            $("#crud-successful").removeClass("bg-warning");
            $("#crud-successful").removeClass("bg-danger");
            $("#crud-successful").addClass("bg-danger");
            $('#crud-successful').html("<h5>VIEW INVITEE ERROR</h5>");
            $('#crud-successful').show().delay(1000).fadeOut();
            console.log("Error");
        });
    });

    // Send Email Invitation
    $("#inviteeList").on('click', '.sendEmailInvitee', function() {
        var inviteeID = $(this).attr("id");
        var inviteeAction = 'sendEmailInvitee';
        $.ajax({
            url:"invitee-action?eventID="+eventID,
            method:"POST",
            data:{inviteeID:inviteeID, inviteeAction:inviteeAction},
            dataType:"json",
            beforeSend: function(){
                $("#loadingModal").modal('show');
            }
        })
        .done(function(data) {
            $("#loadingModal").modal('hide');
            if (data.Status == "success") {
                $("#crud-successful").removeClass("bg-succes");
                $("#crud-successful").removeClass("bg-warning");
                $("#crud-successful").removeClass("bg-danger");
                $("#crud-successful").addClass("bg-succes");
                $('#crud-successful').html("<h5>SEND EMAIL INVITATION SUCCESSFULLY</h5>");
                $('#crud-successful').show().delay(1000).fadeOut();
            }else{
                $("#crud-successful").removeClass("bg-succes");
                $("#crud-successful").removeClass("bg-warning");
                $("#crud-successful").removeClass("bg-danger");
                $("#crud-successful").addClass("bg-danger");
                $('#crud-successful').html("<h5>SEND EMAIL INVITATION FAILED</h5>");
                $('#crud-successful').show().delay(1000).fadeOut();
            }
            console.log("Success: " + data.Status);
        })
        .fail(function() {
            $("#loadingModal").modal('hide');
            $("#crud-successful").removeClass("bg-succes");
            $("#crud-successful").removeClass("bg-warning");
            $("#crud-successful").removeClass("bg-danger");
            $("#crud-successful").addClass("bg-danger");
            $('#crud-successful').html("<h5>SEND EMAIL INVITATION ERROR</h5>");
            $('#crud-successful').show().delay(1000).fadeOut();
            console.log("Error");
        });
    });

    // Edit Invitee Function
    $("#inviteeList").on('click', '.editInvitee', function(){
        var inviteeID = $(this).attr("id");
        var inviteeAction = 'getInvitee';
        $.ajax({
            url:"invitee-action?eventID="+eventID,
            method:"POST",
            data:{inviteeID:inviteeID, inviteeAction:inviteeAction},
            dataType:"json",
            beforeSend: function(){
                $("#loadingModal").modal('show');
            },
            success:function(data){
                $("#loadingModal").modal('hide');
                $('.add-edit-invitee-title').html("<i class='fa fa-edit'></i> Edit Invitee");
                // $("#inviteeSave").removeClass("btn-succes");
                // $("#inviteeSave").addClass("btn-warning");
                // $('#inviteeSave').html("<i class='fas fa-edit'></i> Edit");
                $('#selectedInviteeID').val(data.ID);
                $('#inviteeFirstName').val(data.firstname);
                $('#inviteeMiddleName').val(data.middlename);
                $('#inviteeLastName').val(data.lastname);
                $('#inviteeEmail').val(data.email);
                $('#inviteePhoneNum').val(data.phonenum);
                $('#inviteeTypeForm').val(data.type);
                $('#inviteeAction').val('editInvitee');
                $('#inviteeSave').val('Edit');
                $('#addEditInviteeModal').modal('show');
            }
        })
        .fail(function() {
            $("#loadingModal").modal('hide');
            $("#crud-successful").removeClass("bg-succes");
            $("#crud-successful").removeClass("bg-warning");
            $("#crud-successful").removeClass("bg-danger");
            $("#crud-successful").addClass("bg-danger");
            $('#crud-successful').html("<h5>EDIT INVITEE ERROR</h5>");
            $('#crud-successful').show().delay(1000).fadeOut();
            console.log("Error");
        });
    });

    // Delete Invitee Modal Function
    $("#inviteeList").on('click', '.deleteInvitee', function(){
        var inviteeID = $(this).attr("id");
        var inviteeAction = 'getInvitee';
        $.ajax({
            url:"invitee-action?eventID="+eventID,
            method:"POST",
            data:{inviteeID:inviteeID, inviteeAction:inviteeAction},
            dataType:"json",
            beforeSend: function(){
                $("#loadingModal").modal('show');
            },
            success:function(data){
                $("#loadingModal").modal('hide');
                $('#deleteInviteeModal').modal('show');
                $('#inviteeDeleteID').val(data.ID);
                $('#invitee-delete-name').html("<i class='fas fa-user'></i> Name of Invitee: " + data.firstname + " " + data.middlename + " " + data.lastname);
            }
        })
        .fail(function() {
            $("#loadingModal").modal('hide');
            $("#crud-successful").removeClass("bg-succes");
            $("#crud-successful").removeClass("bg-warning");
            $("#crud-successful").removeClass("bg-danger");
            $("#crud-successful").addClass("bg-danger");
            $('#crud-successful').html("<h5>DELETE INVITEE ERROR</h5>");
            $('#crud-successful').show().delay(1000).fadeOut();
            console.log("Error");
        });
    });

    // Submit Delete Invitee Form
    $("#deleteInviteeModal").on('submit','#inviteeDeleteForm', function(event){
        event.preventDefault();
        // $('#inviteeSave').attr('disabled','disabled');
        var formData = $(this).serialize();
        $.ajax({
            url:"invitee-action?eventID="+eventID,
            method:"POST",
            data:formData,
            beforeSend: function(){
                $("#loadingModal").modal('show');
            },
            success:function(data){
                $("#loadingModal").modal('hide');
                $("#crud-successful").removeClass("bg-succes");
                $("#crud-successful").removeClass("bg-warning");
                $("#crud-successful").removeClass("bg-danger");
                $("#crud-successful").addClass("bg-danger");
                if (JSON.parse(data).Status == "success") {
                    $('#crud-successful').html("<h5>DELETE INVITEE SUCCESSFULLY</h5>");
                }else{
                    $('#crud-successful').html("<h5>DELETE INVITEE FAILED</h5>");
                }
                $('#inviteeDeleteForm')[0].reset();
                $('#deleteInviteeModal').modal('hide');
                $('#crud-successful').show().delay(1000).fadeOut();
                // $('#inviteeSave').attr('disabled', false);
                inviteeData.ajax.reload();
            }
        })
        .fail(function() {
            $("#loadingModal").modal('hide');
            $("#crud-successful").removeClass("bg-succes");
            $("#crud-successful").removeClass("bg-warning");
            $("#crud-successful").removeClass("bg-danger");
            $("#crud-successful").addClass("bg-danger");
            $('#crud-successful').html("<h5>DELETE INVITEE ERROR</h5>");
            $('#crud-successful').show().delay(1000).fadeOut();
            console.log("Error");
        });
    });

    // Format Date and Time
    function formatDateTime(dateTime){
        var date = new Date(dateTime);
        var hours = date.getHours();
        var minutes = date.getMinutes();
        var ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12; // the hour '0' should be '12'
        hours = hours < 10 ? '0'+hours : hours;
        minutes = minutes < 10 ? '0'+minutes : minutes;
        const monthNames = ["Jan.", "Feb.", "Mar.", "Apr.", "May", "Jun.",
      "Jul.", "Aug.", "Sept.", "Oct.", "Nov.", "Dec."
        ];
        var strDateTime = monthNames[date.getMonth()] + " " + date.getDate() + ", " + date.getFullYear() + " - " + hours + ':' + minutes + ampm;
        return strDateTime;
    }

    // Add or Edit Invitee validation
    function addEditInviteeValidation(){
        var valid = true;

        $("#inviteeFirstName").removeClass("error-field");
        $("#inviteeMiddleName").removeClass("error-field");
        $("#inviteeLastName").removeClass("error-field");
        $("#inviteeEmail").removeClass("error-field");
        $("#inviteePhoneNum").removeClass("error-field");

        var inviteeFirstName = $("#inviteeFirstName").val();
        var inviteeMiddleName = $("#inviteeMiddleName").val();
        var inviteeLastName = $("#inviteeLastName").val();
        var inviteeEmail = $("#inviteeEmail").val();
        var inviteePhoneNum = $("#inviteePhoneNum").val();
        var emailRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;
        var phoneNumRegex = /^\(?([0-9]{4})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/;

        $("#firstName-info").html("").hide();
        $("#middleName-info").html("").hide();
        $("#lastName-info").html("").hide();
        $("#email-info").html("").hide();
        $("#phoneNum-info").html("").hide();

        if(inviteeFirstName.trim() == ""){
            $("#firstName-info").html("* Required.").css("color", "#ee0000").show();
            $("#inviteeFirstName").addClass("error-field");
            valid = false;
        }
        if(inviteeMiddleName.trim() == ""){
            $("#middleName-info").html("* Required.").css("color", "#ee0000").show();
            $("#inviteeMiddleName").addClass("error-field");
            valid = false;
        }
        if(inviteeLastName.trim() == ""){
            $("#lastName-info").html("* Required.").css("color", "#ee0000").show();
            $("#inviteeLastName").addClass("error-field");
            valid = false;
        }
        if (inviteeEmail == "") {
            $("#email-info").html("* Required.").css("color", "#ee0000").show();
            $("#inviteeEmail").addClass("error-field");
            valid = false;
        } else if (inviteeEmail.trim() == "") {
            $("#email-info").html("* Invalid.").css("color", "#ee0000").show();
            $("#inviteeEmail").addClass("error-field");
            valid = false;
        } else if (!emailRegex.test(inviteeEmail)) {
            $("#email-info").html("* Invalid.").css("color", "#ee0000").show();
            $("#inviteeEmail").addClass("error-field");
            valid = false;
        }
        if (inviteePhoneNum == "") {
            $("#phoneNum-info").html("* Required.").css("color", "#ee0000").show();
            $("#inviteePhoneNum").addClass("error-field");
            valid = false;
        } else if (inviteePhoneNum.trim() == "") {
            $("#phoneNum-info").html("* Invalid.").css("color", "#ee0000").show();
            $("#inviteePhoneNum").addClass("error-field");
            valid = false;
        } else if (!phoneNumRegex.test(inviteePhoneNum)) {
            $("#phoneNum-info").html("* Invalid.").css("color", "#ee0000").show();
            $("#inviteePhoneNum").addClass("error-field");
            valid = false;
        }
        return valid;
    }
});