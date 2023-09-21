$(document).ready(function(){
    // Get Event ID
    var eventID = document.getElementById('current-event-ID').value;

    // Get Audio Beep Effects
    var errorBeep = new Audio("./audio/error.wav");
    var successBeep = new Audio("./audio/success.wav");
    var warningBeep = new Audio("./audio/warning.wav");

    // Selected Date Start and End
    var selectDateStart = $("#dateSelect").val()+" "+(($("#amfmSelect").val() == 'am') ? "00:00:00" : "12:00:00");
    var selectDateEnd = $("#dateSelect").val()+" "+(($("#amfmSelect").val() == 'am') ? "11:59:59" : "23:59:59");
    var selectIVTDateStart = $("#dateIvtSelect").val()+" "+(($("#amfmIvtSelect").val() == 'am') ? "00:00:00" : "12:00:00");
    var selectIVTDateEnd = $("#dateIvtSelect").val()+" "+(($("#amfmIvtSelect").val() == 'am') ? "11:59:59" : "23:59:59");

    // Load Attendance List
    var attendanceData = null;
    reinitAttendanceData();

    // Load Invitee List
    var inviteeData = null;
    reinitInviteeData();

    // Reinitialize Attendance Data Function
    function reinitAttendanceData() {
        attendanceData = $('#attendanceList').removeAttr('width').DataTable({
            "lengthChange": false,
            "processing":true,
            "serverSide":true,
            "order":[],
            "destroy": true,
            "ajax":{
                url:"attendance-action?eventID="+eventID,
                type:"POST",
                data: {
                    attendanceAction:'listAttendance',
                    selectDateStart: selectDateStart,
                    selectDateEnd: selectDateEnd
                },
                dataType:"json",
                complete: function(data) {
                    $('#attendanceList_paginate').addClass('mb-2');
                    $("#attendanceList").css("width", "1046px");
                    $("#attendanceNum").css("width", "5%");
                    $("#attendanceName").css("width", "25%");
                    $("#attendanceInviteeCode").css("width", "24%");
                    $("#attendanceType").css("width", "5%");
                    $("#attendanceDateTime").css("width", "17%");
                    // $("#attendanceSend").css("width", "18%");
                    $("#attendanceSend").css({
                        "width": '12%',
                        "padding": '3px'
                    });
                    var totalPresent = document.getElementsByClassName("totalPresent")
                    for (var i = 0; i < totalPresent.length; i++) {
                        totalPresent[i].innerText = data.responseJSON.recordsTotal;
                    }
                }
            },
            "columnDefs":[
                {
                    "targets": [5],
                    "className": "text-center",
                    "orderable":false,
                }
            ],
            'columns': [
                { data: "row_num" },
                { data: "invitee_name" },
                { data: "invitee_code" },
                { data: "type" },
                { data: "datetime_attendance" },
                { data: "send" }
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
                                columns: [ 0, 1, 2, 3, 4 ]
                            },
                            title: document.getElementById("current-event-title").innerHTML + " Attendance List"
                        },
                        {
                            extend: 'csvHtml5',
                            exportOptions: {
                                columns: [ 0, 1, 2, 3, 4 ]
                            },
                            title: document.getElementById("current-event-title").innerHTML + " Attendance List"
                        },
                        {
                            extend: 'pdfHtml5',
                            exportOptions: {
                                columns: [ 0, 1, 2, 3, 4 ]
                            },
                            title: document.getElementById("current-event-title").innerHTML + " Attendance List"
                        },
                        {
                            extend: 'print',
                            exportOptions: {
                                columns: [ 0, 1, 2, 3, 4 ]
                            },
                            title: document.getElementById("current-event-title").innerHTML + " Attendance List"
                        }
                    ]
                },
                {
                    text: '<i class="fas fa-cog"></i> Options',
                    className: "float-left",
                    action: function () {
                        $.ajax({
                            url: "certificate-action",
                            method: "POST",
                            data:{
                                certificateAction: 'getCertConfig',
                                eventID: eventID
                            },
                            dataType:"json",
                            beforeSend: function() {
                                $("#loadingModal").modal('show');
                            },
                            success: function(data) {
                                // Set Certificate Orienation
                                if (data.certOrientation == 'L'){
                                    $('#certOrientation').val("L");
                                } else {
                                    $('#certOrientation').val("P");
                                }
                                // Set Certificate Size
                                if (data.certSize == 'Letter'){
                                    $('#certSize').val("Letter");
                                } else if (data.certSize == 'A4'){
                                    $('#certSize').val("A4");
                                } else if (data.certSize == 'A3'){
                                    $('#certSize').val("A3");
                                } else if (data.certSize == 'A5'){
                                    $('#certSize').val("A5");
                                } else {
                                    $('#certSize').val("Legal");
                                }
                                // Set Certificate Font
                                if (data.certTextFont == 'Helvetica') {
                                    $('#certFont').val("Helvetica");
                                } else if (data.certTextFont == 'Courier') {
                                    $('#certFont').val("Courier");
                                } else {
                                    $('#certFont').val("Times");
                                }
                                // Set Certificate Font Style
                                if (data.certTextFontStyle == 'B') {
                                    $('#certFontStyle').val("B");
                                } else if (data.certTextFontStyle == 'I') {
                                    $('#certFontStyle').val("I");
                                } else if (data.certTextFontStyle == 'U') {
                                    $('#certFontStyle').val("U");
                                } else {
                                    $('#certFontStyle').val("");
                                }
                                // Set Certificate Font Size
                                $('#certFontSize').val(data.certTextFontSize);
                                // Set Certificate Font Color
                                $('#certFontColor').val(data.certTextFontColor);
                                // Set Certificate Text Positions
                                $('#certTextPositionX').val(data.certTextPositionX);
                                $('#certTextPositionY').val(data.certTextPositionY);
                                // Set Certificate Barcode Positions
                                $('#certBarcodePositionX').val(data.certBarcodePositionX);
                                $('#certBarcodePositionY').val(data.certBarcodePositionY);
                                // Hide Load Modal
                                $("#loadingModal").modal('hide');
                                // Show Certificate Options Modal
                                $("#certOptionsModal").modal('show');
                            }
                        })
                        .fail(function() {
                            $("#loadingModal").modal('hide');
                            statusSnackBar.style.backgroundColor = "#d9534f";
                            statusSnackBar.innerHTML = '<i class="fas fa-times-circle"></i> Load Error';
                            displayStatusSnackBar();
                        });
                        
                    }
                }
            ]
        });
    }

    // Reinitialize Invitee Data Function
    function reinitInviteeData() {
        inviteeData = $('#inviteeList').removeAttr('width').DataTable({
            "lengthChange": false,
            "processing":true,
            "serverSide":true,
            "order":[],
            "destroy": true,
            "ajax":{
                url:"attendance-action?eventID="+eventID,
                type:"POST",
                data: {
                    attendanceAction:'listInvitee',
                    selectDateStart: selectIVTDateStart,
                    selectDateEnd: selectIVTDateEnd
                },
                dataType:"json",
                complete: function(data) {
                    $('#inviteeList_paginate').addClass('mb-2');
                    $("#inviteeList").css("width", "1046px");
                    $("#inviteeNum").css("width", "5%");
                    $("#inviteeStatus").css("width", "5%");
                    $("#inviteeName").css("width", "30%");
                    $("#inviteeCode").css("width", "25%");
                    $("#inviteeEmail").css("width", "20%");
                    $("#inviteePhoneNum").css("width", "10%");
                    var totalInvitees = document.getElementsByClassName("totalInvitees");
                    for (var i = 0; i < totalInvitees.length; i++) {
                        totalInvitees[i].innerText = data.responseJSON.recordsTotal;
                    }
                    document.getElementById("totalAbsent").innerText = data.responseJSON.recordsAbsent;
                }
            },
            'columns': [
                { data: "row_num" },
                { data: "attendance_status" },
                { data: "invitee_name" },
                { data: "invitee_code" },
                { data: "email" },
                { data: "phonenum" },
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
                                columns: [ 0, 1, 2, 3, 4, 5 ]
                            },
                            title: document.getElementById("current-event-title").innerHTML + " Invitees' List"
                        },
                        {
                            extend: 'csvHtml5',
                            exportOptions: {
                                columns: [ 0, 1, 2, 3, 4, 5 ]
                            },
                            title: document.getElementById("current-event-title").innerHTML + " Invitees' List"
                        },
                        {
                            extend: 'pdfHtml5',
                            exportOptions: {
                                columns: [ 0, 1, 2, 3, 4, 5 ]
                            },
                            title: document.getElementById("current-event-title").innerHTML + " Invitees' List"
                        },
                        {
                            extend: 'print',
                            exportOptions: {
                                columns: [ 0, 1, 2, 3, 4, 5 ]
                            },
                            title: document.getElementById("current-event-title").innerHTML + " Invitees' List"
                        }
                    ]
                }
            ]
        }); 
    }

    // Date Select Change
    $("#dateSelect").change(function () {
        selectDateStart = $("#dateSelect").val()+" "+(($("#amfmSelect").val() === 'am') ? "00:00:00" : "12:00:00");
        selectDateEnd = $("#dateSelect").val()+" "+(($("#amfmSelect").val() === 'am') ? "11:59:59" : "23:59:59");
        reinitAttendanceData();
    });

    // AM or PM Change
    $("#amfmSelect").change(function () {
        selectDateStart = $("#dateSelect").val()+" "+(($("#amfmSelect").val() === 'am') ? "00:00:00" : "12:00:00");
        selectDateEnd = $("#dateSelect").val()+" "+(($("#amfmSelect").val() === 'am') ? "11:59:59" : "23:59:59");
        reinitAttendanceData();
    });

    // Date Invitee Select Change
    $("#dateIvtSelect").change(function () {
        selectIVTDateStart = $("#dateIvtSelect").val()+" "+(($("#amfmIvtSelect").val() == 'am') ? "00:00:00" : "12:00:00");
        selectIVTDateEnd = $("#dateIvtSelect").val()+" "+(($("#amfmIvtSelect").val() == 'am') ? "11:59:59" : "23:59:59");
        reinitInviteeData();
    });

    // AM or PM Invitee Change
    $("#amfmIvtSelect").change(function () {
        selectIVTDateStart = $("#dateIvtSelect").val()+" "+(($("#amfmIvtSelect").val() == 'am') ? "00:00:00" : "12:00:00");
        selectIVTDateEnd = $("#dateIvtSelect").val()+" "+(($("#amfmIvtSelect").val() == 'am') ? "11:59:59" : "23:59:59");
        reinitInviteeData();
    });

    // Select All Invitees using checkbox
    $("#selectAllInvitees").click(function() {
        $(this).closest('table').find('td input:checkbox').prop('checked', this.checked);
        if ($(this).is(":checked")){
            attendanceData.rows().select();
        } else {
            attendanceData.rows().deselect();
        }
        selectedCheckboxes();
    });

    // Selected Checkbox Row
    $("#attendanceList").on('click', '.selectAttendance', function(){
        $("#selectAllInvitees").prop('checked',false);
        var selectedIndex = $(this).closest("tr").index();
        if ($(this).is(":checked")){
            attendanceData.row(selectedIndex).select();
        } else {
            attendanceData.row(selectedIndex).deselect();
        }
        selectedCheckboxes();
    });

    // Show or Hide Send Button provided that selected at least one row
    function selectedCheckboxes() {
        var rowCount = 0;
        $('#attendanceList .selectAttendance:checked').each(function() {
            rowCount++;
        });
        if (rowCount > 0) {
            $('#sendSelectedCertificates').show();
        } else {
            $('#sendSelectedCertificates').hide();
        }   
    }

    // Send Selected Certificate/s
    $("#sendSelectedCertificates").click(function() {
        var selectedRows = new Array();
        $('#attendanceList .selectAttendance:checked').each(function() {
            selectedRows.push($(this).attr('value'));
        });
        if (selectedRows.length > 0) {
            $.ajax({
                url:"attendance-action?eventID="+eventID,
                method:"POST",
                data:{selectedInviteeCodes: JSON.stringify(selectedRows), attendanceAction:'sendSelectedCertificate'},
                dataType:"json",
                beforeSend: function(){
                    $("#loadingModal").modal('show');
                }
            })
            .done(function(data) {
                $("#loadingModal").modal('hide');
                $("#selectAllInvitees").prop('checked',false);
                $("#selectAllInvitees").closest('table').find('td input:checkbox').prop('checked', false);
                attendanceData.rows().deselect();
                $('#sendSelectedCertificates').hide();
                if (data.Status == "success") {
                    statusSnackBar.style.backgroundColor = "#5cb85c";
                    statusSnackBar.innerHTML = '<i class="fas fa-check"></i> Send Certificate/s Successfully';
                    displayStatusSnackBar();
                }else{
                    statusSnackBar.style.backgroundColor = "#5cb85c";
                    statusSnackBar.innerHTML = '<i class="fas fa-times-circle"></i> Send Certificate/s Failed';
                    displayStatusSnackBar();
                }
            })
            .fail(function() {
                $("#loadingModal").modal('hide');
                statusSnackBar.style.backgroundColor = "#d9534f";
                statusSnackBar.innerHTML = '<i class="fas fa-times-circle"></i> Send Error';
                displayStatusSnackBar();
            });
        }
    });

    // Get Preview Certificate
    $("#certOptionsModal").on('click', "#previewCertificateBtn", function () {
        var inviteeCode = $(this).attr("id");
        $.ajax({
            url:"certificate-action",
            method:"POST",
            data:{
                certificateAction: 'getPreviewCertificate',
                eventID: eventID,
                certLayout: $('#certOrientation').val() + "-" + $('#certSize').val(),
                certTextStyle: $('#certFont').val() + "-" + $('#certFontStyle').val() + "-" + $('#certFontSize').val(),
                certTextFontColor: $('#certFontColor').val(),
                certTextPosition: $('#certTextPositionX').val() + "," + $('#certTextPositionY').val(),
                certBarcodePosition:  $('#certBarcodePositionX').val() + "," + $('#certBarcodePositionY').val(),
            },
            dataType:"json",
            beforeSend: function() {
                $("#loadingModal").modal('show');
                $('#previewCertFile').attr("src",'');
            },
            success: function(data) {
                $("#loadingModal").modal('hide');
                $('#previewCertFile').attr("src",'data:application/pdf;base64,' + data.base64CERT);
                $("#previewCertConfigModal").modal('show');
            }
        })
        .fail(function() {
            $("#loadingModal").modal('hide');
            statusSnackBar.style.backgroundColor = "#d9534f";
            statusSnackBar.innerHTML = '<i class="fas fa-times-circle"></i> Load Error';
            displayStatusSnackBar();
        });

    });

    // Save Certificate Config
    $("#certOptionsModal").on('submit','#certificateOptionForm', function(event){
        event.preventDefault();
        $.ajax({
            url:"certificate-action",
            method:"POST",
            data:{
                certificateAction: 'saveCertConfig',
                eventID: eventID,
                certLayout: $('#certOrientation').val() + "-" + $('#certSize').val(),
                certTextStyle: $('#certFont').val() + "-" + $('#certFontStyle').val() + "-" + $('#certFontSize').val(),
                certTextFontColor: $('#certFontColor').val(),
                certTextPosition: $('#certTextPositionX').val() + "," + $('#certTextPositionY').val(),
                certBarcodePosition:  $('#certBarcodePositionX').val() + "," + $('#certBarcodePositionY').val(),
            },
            dataType:"json",
            beforeSend: function() {
                $("#loadingModal").modal('show');
            },
            success: function(data) {
                $("#loadingModal").modal('hide');
                $("#certOptionsModal").modal('hide');
                statusSnackBar.style.backgroundColor = "#5cb85c";
                statusSnackBar.innerHTML = '<i class="fas fa-check"></i> Config Successfully';
                displayStatusSnackBar();
            }
        })
        .fail(function() {
            $("#loadingModal").modal('hide');
            $("#certOptionsModal").modal('hide');
            statusSnackBar.style.backgroundColor = "#d9534f";
            statusSnackBar.innerHTML = '<i class="fas fa-times-circle"></i> Config Error';
            displayStatusSnackBar();
        });
    });

    // Send Email Certificate
    $("#attendanceList").on('click', '.sendCertificate', function() {
        var inviteeCode = $(this).attr("id");
        $.ajax({
            url:"attendance-action?eventID="+eventID,
            method:"POST",
            data:{
                attendanceAction:'sendCertificate',
                inviteeCode: inviteeCode
            },
            dataType:"json",
            beforeSend: function(){
                $("#loadingModal").modal('show');
            }
        })
        .done(function(data) {
            $("#loadingModal").modal('hide');
            if (data.Status == 'success') {
                statusSnackBar.style.backgroundColor = "#5cb85c";
                statusSnackBar.innerHTML = '<i class="fas fa-check"></i> Send Certificate Successfully';
                displayStatusSnackBar();
            } else {
                statusSnackBar.style.backgroundColor = "#5cb85c";
                statusSnackBar.innerHTML = '<i class="fas fa-times-circle"></i> Send Certificate Failed';
                displayStatusSnackBar();
            }
        })
        .fail(function() {
            $("#loadingModal").modal('hide');
            statusSnackBar.style.backgroundColor = "#d9534f";
            statusSnackBar.innerHTML = '<i class="fas fa-times-circle"></i> Send Error';
            displayStatusSnackBar();
        });
    });

    // Check Code Result
    function codeResult(decodedText) {
        var isGenerateCert = ($("input[id='generateCertAuto']").is(":checked")) ? "auto" : "manual";
        var isSendEmailCert = ($("input[name='sendCert'").is(':checked')) ? "yes" : "no";
        jQuery(function($) {
            $.ajax({
                url: "attendance-action?eventID="+eventID,
                method: "POST",
                dataType: "json",
                data: {
                    attendanceAction:'scanAttendance', 
                    inviteeCode:decodedText,
                    isGenerateCert: isGenerateCert
                }
            })
            .done(function(data) {
                if (data.scanStatus == "success") {
                    successBeep.play();
                    displyScannedResult(data.scannedInviteeName, decodedText, "#5cb85c", "Checked Successfully");
                    statusSnackBar.style.backgroundColor = "#5cb85c";
                    statusSnackBar.innerHTML = '<i class="fas fa-check"></i> Attendance checked successfully';
                    barcodeReader.style.borderColor = "#28a745";

                    // Refresh Attendance and Invitees' List Tables
                    attendanceData.ajax.reload();
                    inviteeData.ajax.reload();

                    // Check if Send Certificate is switched on
                    if (isSendEmailCert == "yes") {
                        $.ajax({
                            url:"attendance-action?eventID="+eventID,
                            method:"POST",
                            data:{
                                attendanceAction:'sendCertificate',
                                inviteeCode: decodedText
                            },
                            dataType:"json",
                            beforeSend: function(){
                                $("#loadingModal").modal('show');
                            }
                        })
                        .done(function(data) {
                            $("#loadingModal").modal('hide');
                            if (data.Status == 'success') {
                                statusSnackBar.style.backgroundColor = "#5cb85c";
                                statusSnackBar.innerHTML = '<i class="fas fa-check"></i> Send Certificate Successfully';
                                displayStatusSnackBar();
                            } else {
                                statusSnackBar.style.backgroundColor = "#5cb85c";
                                statusSnackBar.innerHTML = '<i class="fas fa-times-circle"></i> Send Certificate Failed';
                                displayStatusSnackBar();
                            }
                        })
                        .fail(function() {
                            $("#loadingModal").modal('hide');
                            statusSnackBar.style.backgroundColor = "#d9534f";
                            statusSnackBar.innerHTML = '<i class="fas fa-times-circle"></i> Send Error';
                            displayStatusSnackBar();
                        });
                    }
                } else if (data.scanStatus == "already") {
                    warningBeep.play();
                    displyScannedResult(data.scannedInviteeName, decodedText, "#f0ad4e", "Checked Already");
                    statusSnackBar.style.backgroundColor = "#f0ad4e";
                    statusSnackBar.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Attendance already checked';
                    barcodeReader.style.borderColor = "#ffc107";
                } else if (data.scanStatus == "invalid") {
                    errorBeep.play();
                    displyScannedResult(data.scannedInviteeName, "Invalid Code", "#d9534f", "Invalid");
                    statusSnackBar.style.backgroundColor = "#d9534f";
                    statusSnackBar.innerHTML = '<i class="fas fa-times-circle"></i> Invalid invitee barcode';
                    barcodeReader.style.borderColor = "#dc3545";
                } else {
                    errorBeep.play();
                    displyScannedResult(data.scannedInviteeName, "Error", "#d9534f", "Error");
                    statusSnackBar.style.backgroundColor = "#d9534f";
                    statusSnackBar.innerHTML = '<i class="fas fa-times-circle"></i> Attendance Error';
                    barcodeReader.style.borderColor = "#dc3545";
                }
            })
            .fail(function() {
                errorBeep.play();
                displyScannedResult("Error", "Error", "#d9534f", "Error");
                statusSnackBar.style.backgroundColor = "#d9534f";
                statusSnackBar.innerHTML = '<i class="fas fa-times-circle"></i> Attendance Error';
                barcodeReader.style.borderColor = "#dc3545";
            });
        });

        displayStatusSnackBar();
    }

    // Show status snackback
    function displayStatusSnackBar() {
        // Add the "show" class to DIV
        statusSnackBar.className = "show";

        // After 2 seconds, remove the show class from DIV
        setTimeout(function(){ 
            statusSnackBar.className = statusSnackBar.className.replace("show", "");
            barcodeReader.style.borderColor = "#929eaa";
            statusSnackBar.style.backgroundColor = "";
            statusSnackBar.innerHTML = '';
        }, 2000);
    }

    // Display Scanned Invitte Name Result
    function displyScannedResult(name, code, bg_color, status) {
        $("#scannedInviteeName").html(name);
        $("#scannedInviteeCode").html(code);
        $("#scannedInviteeResult").html(status);
        $("#scannedResultMsg").css('background-color', bg_color);
        $("#scannedResultMsg").show();
    }

    // Initialize QR Code Reader
    var html5QrCode = null;
    if (beforeStartScanFlag && endScanFlag) {
        html5QrCode = new Html5Qrcode("qr-reader", { 
            formatsToSupport: [ Html5QrcodeSupportedFormats.PDF_417 ] 
        });
    }

    // Configure QR Code Reader
    const config = {
        fps: 10,
        qrbox: {
            width: 280,
            height: 200
        } 
    };

    // Last QR Code Result
    var lastResult;

    // Scan Attendance Camera Flag
    var camModeFlag = false;

    // Handle QR Code Success Scan
    const qrCodeSuccessCallback = (decodedText, decodedResult) => {
        if (decodedText != lastResult) {
            lastResult = decodedText;
            codeResult(decodedText);
        }
    };

    // Initialize Camera IDs and Labels
    var cameraIDs = new Array();
    var cameraLabels = new Array();

    // This method will trigger user permissions
    if (beforeStartScanFlag && endScanFlag) {
        Html5Qrcode.getCameras().then(devices => {
            /**
             * devices would be an array of objects of type:
             * { id: "id", label: "label" }
             */
            if (devices && devices.length) {
                // Fetch Camera IDs into Array
                cameraIDs = devices.map(function(value) {
                    return value.id;
                });
                cameraLabels = devices.map(function(value) {
                    return value.label;
                });
                // Assign Camera to Drop Down List
                $('#cameraSelection').html(function () {
                    var tempStr = ""
                    for (var i = 0; i < cameraIDs.length; i++) {
                        tempStr += "<option value='" + cameraIDs[i] + "'>" + cameraLabels[i] + "</option>"
                    }
                    return tempStr;
                });
                // Show Camera Content If Permission Granted
                $("#permissionStatus").hide(0, function () {
                    $("#qr-reader-content").show();
                });
            }
        }).catch(err => {
            $("#permissionStatus").html("<i class='fas fa-ban'></i> Permission denied.");
        });
    }

    // Start Scanning
    $("#startQRCodeScanner").click(function () {
        $("#qr-reader").show(0, function() {
            $("#qrStartSelection").hide(0, function() {
                $("#stopQRCodeScanner").show(0, function () {
                    html5QrCode.start({
                        deviceId: {
                            exact: $("#cameraSelection").val(),
                        } 
                    }, config, qrCodeSuccessCallback);
                    lastResult = "";
                    camModeFlag = true;
                });
            });
        });
    });

    // Stop Scanning
    $("#stopQRCodeScanner").click(function () {
        html5QrCode.stop().then((ignore) => {
            $("#qr-reader").hide(0, function () {
                $("#stopQRCodeScanner").hide(0, function () {
                    $("#qrStartSelection").show();
                    camModeFlag = false;
                });
            });
        }).catch((err) => {
            console.log("Stop Error");
        });
    });

    // Enter Invitee Code Submit
    $("#codeInputModeForm").submit(function(event) {
        event.preventDefault();
        // Get Input Code
        var inputCode = $("input[name='inviteeCodeInput']").val().trim();
        // Clear Input and Error Message
        $("input[name='inviteeCodeInput']").val("");
        $("#requiredCodeInputError").html("");
        if (inputCode == "") {
            $("#requiredCodeInputError").html("* Required").css('color', '#ee0000');
        } else {
            lastResult = inputCode;
            codeResult(inputCode);
        }
    });

    // Scan Attendance Button Click
    $("#scanAttendanceBtn").click(function() {
        $("#scanSelectionMode").val("cameraMode");
        $("#codeInputModeContent").hide();
        $("#scannedResultMsg").hide();
        $("#cameraModeContent").show();
    });

    // Hide Modal Event
    $('#scanAttendanceModal').on('hide.bs.modal', function (e) {
        // Check if Camera is Not Running
        if (camModeFlag == true) {
            html5QrCode.stop().then((ignore) => {
                $("#qr-reader").hide(0, function () {
                    $("#stopQRCodeScanner").hide(0, function () {
                        $("#qrStartSelection").show();
                        camModeFlag = false;
                    });
                });
            }).catch((err) => {
                console.log("Stop Error");
            });
        }
    });

    // Change Scan Attendance Mode
    $("#scanSelectionMode").change(function() {
        // Check if Camera is Not Running
        if (camModeFlag == true) {
            html5QrCode.stop().then((ignore) => {
                $("#qr-reader").hide(0, function () {
                    $("#stopQRCodeScanner").hide(0, function () {
                        $("#qrStartSelection").show();
                        camModeFlag = false;
                    });
                });
            }).catch((err) => {
                console.log("Stop Error");
            });
        }
        if ($("#scanSelectionMode" ).val() == "cameraMode") {
            $("#codeInputModeContent").hide();
            $("#cameraModeContent").show();
        } else {
            $("input[name='inviteeCodeInput']").val("");
            $("#cameraModeContent").hide();
            $("#codeInputModeContent").show();
        }
    });

    // Close Scanned Result Message
    $("#closeScannedResult").click(function() {
        $("#scannedResultMsg").fadeOut();
    });

});