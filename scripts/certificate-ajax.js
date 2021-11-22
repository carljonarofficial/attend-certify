$(document).ready(function() {
	// Initialize Event ID and Title
    var currentEventID = "", currentEventTitle = "";

    // Get Audio Beep Effects
    var errorBeep = new Audio("./audio/error.wav");
    var successBeep = new Audio("./audio/success.wav");

	// Barcode Result Variable
    var lastResult;

    // Initialize Certificate DataTable
    var certificateData;

    // Initialize Messages Snackbar Flag
    var successMsg = true, errorMsg = true;

	// Fetch selected event for certificate data
    $("#selectEvent").change(function() {
    	document.getElementById("main-cerficate-validate").style.display = "initial";
    	var loadEventFlag = true;
    	lastResult = "";
    	currentEventID = $("#selectEvent").val();
    	currentEventTitle = $( "#selectEvent option:selected" ).text();
    	successMsg = true;
    	errorMsg = true;
    	certificateData = $('#certificateList').removeAttr('width').DataTable({
    		"destroy": true,
    		"lengthChange": false,
	        "processing":true,
	        "serverSide":true,
	        "order":[],
	        "ajax":{
	        	url:"certificate-action",
	        	type:"POST",
	            data:{
	            	certificateAction:'listCertificate',
	            	eventID: currentEventID
	            },
	            beforeSend: function(){
	            	if (loadEventFlag == true) {
	            		$("#loadingModal").modal('show');
	            		loadEventFlag = false;
	            	}
                },
	            dataType:"json",
                complete: function(data) {
                	$("#loadingModal").modal('hide');
                	statusSnackBar.style.backgroundColor = "#5cb85c";
                	if (successMsg == true) {
                		statusSnackBar.innerHTML = '<i class="fas fa-check"></i> Load successfully';
                	}
		            $('#certificateList_paginate').addClass('mb-2');
		            $("#certificateList").css("width", "1046px");
		            $("#certificateView").css("width", "10%");
		            $("#certificateNum").css("width", "9%");
		            $("#certificateInviteeName").css("width", "17%");
		            $("#certificateInviteeCode").css("width", "18%");
		            $("#certificateCode").css("width", "15%");
		            $("#certificateDateTime").css("width", "14%");
		            $("#certificateSend").css("width", "14%");
		            $("#titleValidateCert").html(currentEventTitle);
		            document.getElementById("totalCertificates").innerText = data.responseJSON.recordsTotal;
		        },
		        error: function (jqXHR, textStatus, errorThrown) {
		        	$("#loadingModal").modal('hide');
	                statusSnackBar.style.backgroundColor = "#d9534f";
	                if (errorMsg == true) {
	                	statusSnackBar.innerHTML = '<i class="fas fa-times-circle"></i> Load Error';
	                }
	            }
	        },
	        "columnDefs":[
	            {
	                "targets":[0, 6],
	                "orderable":false
	            },
	            {
	                "targets":[6],
	                "className": "text-center"
	            }
	        ],
	        'columns': [
	            { data: "view" },
	            { data: "row_num" },
	            { data: "invitee_name" },
	            { data: "invitee_code" },
	            { data: "certificate_code" },
	            { data: "datetime_generated" },
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
	                            columns: [ 1, 2, 3, 4, 5 ]
	                        },
	                        title: $("#selectEvent option:selected").text() + " Certificates List"
	                    },
	                    {
	                        extend: 'csvHtml5',
	                        exportOptions: {
	                            columns: [ 1, 2, 3, 4, 5 ]
	                        },
	                        title: $("#selectEvent option:selected").text() + " Certificates List"
	                    },
	                    {
	                        extend: 'pdfHtml5',
	                        exportOptions: {
	                            columns: [ 1, 2, 3, 4, 5 ]
	                        },
	                        title: $("#selectEvent option:selected").text() + " Certificates List"
	                    },
	                    {
	                        extend: 'print',
	                        exportOptions: {
	                            columns: [ 1, 2, 3, 4, 5 ]
	                        },
	                        title: $("#selectEvent option:selected").text() + " Certificates List"
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
	                			eventID: currentEventID
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
    	$('#sendSelectedCertificates').hide();
    	$("#selectAllInvitees").prop('checked',false);
		displayStatusSnackBar();
    });

	// Select All Invitees using checkbox
    $("#selectAllInvitees").click(function() {
        $(this).closest('table').find('td input:checkbox').prop('checked', this.checked);
        if ($(this).is(":checked")){
            certificateData.rows().select();
        } else {
            certificateData.rows().deselect();
        }
        selectedCheckboxes();
    });

    // Selected Checkbox Row
    $("#certificateList").on('click', '.selectAttendance', function(){
        $("#selectAllInvitees").prop('checked',false);
        var selectedIndex = $(this).closest("tr").index();
        if ($(this).is(":checked")){
            certificateData.row(selectedIndex).select();
        } else {
            certificateData.row(selectedIndex).deselect();
        }
        selectedCheckboxes();
    });

    // Show or Hide Send Button provided that selected at least one row
    function selectedCheckboxes() {
        var rowCount = 0;
        $('#certificateList .selectAttendance:checked').each(function() {
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
        $('#certificateList .selectAttendance:checked').each(function() {
            selectedRows.push($(this).attr('value'));
        });
        if (selectedRows.length > 0) {
            $.ajax({
            	url:"certificate-action",
	            method:"POST",
	            data:{
	            	certificateAction: 'sendSelectedCertificate',
            		currentEventID: currentEventID,
	            	selectedInviteeCodes: JSON.stringify(selectedRows)
	            },
                dataType:"json",
                beforeSend: function(){
                    $("#loadingModal").modal('show');
                }
            })
            .done(function(data) {
                $("#loadingModal").modal('hide');
                $("#selectAllInvitees").closest('table').find('td input:checkbox').prop('checked', false);
                certificateData.rows().deselect();
                $('#sendSelectedCertificates').hide();
                if (data.Status == "success") {
                	successMsg = false;
    				errorMsg = false;
    				certificateData.ajax.reload();
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

    // View Certificate Modal
    $("#certificateList").on('click', '.viewCertificate', function(){
    	var inviteeCode = $(this).attr("id");
    	$.ajax({
			url:"certificate-action",
            method:"POST",
            data:{
            	certificateAction: 'getCertificate',
            	currentEventID: currentEventID,
	            inviteeCode: inviteeCode
            },
            dataType:"json",
			beforeSend: function() {
				$("#loadingModal").modal('show');
				$('#certificateFile').attr("src",'');
			},
			success: function(data) {
				$("#loadingModal").modal('hide');
				$('#viewCertficateName').html(data.invitee_name);
				$('#viewEventTitle').html(currentEventTitle);
				$('#certificateFile').attr("src",'data:application/pdf;base64,' + data.base64CERT);
				$('#viewCertificateModal').modal('show');
			}
    	})
    	.fail(function() {
    		$("#loadingModal").modal('hide');
    		statusSnackBar.style.backgroundColor = "#d9534f";
            statusSnackBar.innerHTML = '<i class="fas fa-times-circle"></i> Load Error';
            displayStatusSnackBar();
    	});
    });

    // Send Certificate
    $("#certificateList").on('click', '.sendCertificate', function() {
    	var inviteeCode = $(this).attr("id");
    	$.ajax({
            url:"certificate-action",
            method:"POST",
            data:{
            	certificateAction: 'sendCertificate',
            	currentEventID: currentEventID,
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

    // Get Preview Certificate
    $("#certOptionsModal").on('click', "#previewCertificateBtn", function () {
    	var inviteeCode = $(this).attr("id");
    	$.ajax({
			url:"certificate-action",
            method:"POST",
            data:{
            	certificateAction: 'getPreviewCertificate',
            	eventID: currentEventID,
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
            	eventID: currentEventID,
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

    // Check Code Result
    function codeResult(decodedText) {
	    jQuery(function($) {
            $.ajax({
                url: "certificate-action",
                method: "POST",
                dataType: "json",
                data: {
                    certificateAction:'validateCertificate',
                    eventID: $("#selectEvent").val(),
                    certificateCode:decodedText
                }
            })
            .done(function(data) {
                if (data.scanStatus == "success") {
                	successBeep.play();
                	displyScannedResult(data.scannedCertificateName, decodedText, "#5cb85c", "Valid");
			        statusSnackBar.style.backgroundColor = "#5cb85c";
			        statusSnackBar.innerHTML = '<i class="fas fa-check"></i> Valid Certificate';
			        barcodeReader.style.borderColor = "#28a745";
                } else if (data.scanStatus == "invalid") {
                	errorBeep.play();
                	displyScannedResult(data.scannedCertificateName, "Invalid Code", "#d9534f", "Invalid");
                    statusSnackBar.style.backgroundColor = "#d9534f";
                    statusSnackBar.innerHTML = '<i class="fas fa-times-circle"></i> Invalid Certificate';
                    barcodeReader.style.borderColor = "#dc3545";
                } else {
                	errorBeep.play();
                	displyScannedResult(data.scannedCertificateName, "Error", "#d9534f", "Error");
                    statusSnackBar.style.backgroundColor = "#d9534f";
                    statusSnackBar.innerHTML = '<i class="fas fa-times-circle"></i> Validation Error';
                    barcodeReader.style.borderColor = "#dc3545";
                }
            })
            .fail(function() {
            	errorBeep.play();
            	displyScannedResult("Error", "Error", "#d9534f", "Error");
                statusSnackBar.style.backgroundColor = "#d9534f";
                statusSnackBar.innerHTML = '<i class="fas fa-times-circle"></i> Validation Error';
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

        // statusSnackBar.style.backgroundColor = "";
        // statusSnackBar.innerHTML = '';
    }

	// Display Scanned Invitte Name Result
    function displyScannedResult(name, code, bg_color, status) {
        $("#scannedCertificateName").html(name);
        $("#scannedCertificateCode").html(code);
        $("#scannedCertificateResult").html(status);
        $("#scannedResultMsg").css('background-color', bg_color);
        $("#scannedResultMsg").show();
    }

    // Initialize QR Code Reader
    const html5QrCode = new Html5Qrcode("qr-reader", { 
        formatsToSupport: [ Html5QrcodeSupportedFormats.PDF_417 ] 
    });

    // Configure QR Code Reader
    const config = {
        fps: 10,
        qrbox: {
            width: 280,
            height: 200
        } 
    };

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

    // Validate Certificate Button Click
    $("#validateCertBtn").click(function() {
        $("#scanSelectionMode").val("cameraMode");
        $("#codeInputModeContent").hide();
        $("#scannedResultMsg").hide();
        $("#cameraModeContent").show();
    });

    // Hide Modal Event
    $('#validateCertificateBarcodeModal').on('hide.bs.modal', function (e) {
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
        if ($("#scanSelectionMode").val() == "cameraMode") {
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