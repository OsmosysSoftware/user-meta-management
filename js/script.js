(function ($) {
    var UMMUserMetaManager = (function () {
	var metaInformation = [];
	var userId;	

	$(".dialog-info, .dialog-warning, .dialog-danger, .dialog-success").dialog({
	    autoOpen: false,
	    modal: true,
	    open: function () {
		designAlertDialogBoxes($(this));
	    }
	});

	$(".close-dialog").click(function () {
	    $(this).parents().find('.umm-dialog-alert').dialog('close');
	});

	var metaFormDialog = $("#UMMDialogForm").dialog({
	    autoOpen: false,
	    resizable: false,
	    height: 570,
	    width: 520,
	    modal: true,
	    buttons: {
		Cancel: function () {		    
		}
	    },
	    open: function () {
		if ($(this).parent().find('.ui-dialog-titlebar .dialog-form-heading').length === 0) {
		    $(this).parent().find('.ui-dialog-titlebar').addClass('dialog-form-header').append('<span class="dialog-form-heading">User meta information</span><button type="button" id="UMMAddUserMetaInformation"   class="umm-btn umm-btn-primary text-right">Add Meta Key</button>');
		}
	    },
	    close: function () {
		getMetaMatchLIst();
	    }		    
	});

	$('#UMMUsersMeta').dataTable({
	    dom: '<"top">rt<"bottom" pl>',
	    order: [[0, "asc"]],
	    scrollX: true,
	    autoWidth: false,
	    columnDefs: [
		{"width": "7%", "targets": 0},
		{"width": "30%", "targets": 1},
		{"width": "25%", "targets": 2},
		{"width": "25%", "targets": 3},
		{"width": "13%", "targets": 4}
	    ]
	});

	$('#submitMeta').click(function (e) {
	    e.preventDefault();
	    if ($('#txtMetaKey').val() !== '') {
		getMetaMatchLIst();
	    } else {
		$('#txtMetaKey').addClass('error');
	    }
	});

	$('#resetMeta').click(function (e) {
	    e.preventDefault();
	    $("#userMetaForm")[0].reset();
	    $('#txtMetaKey').removeClass('error');
	    getMetaMatchLIst();	    
	});
	
	$('body').on('click', '.umm-user-id', function (e) {
	    e.preventDefault();
	    $('.user-meta-results').html('');
	    userId = +($($(this).parents('tr').find('.umm-user-id')).html());
	    getUserMetaInformation();
	    metaFormDialog.dialog('open');
	});

	$('body').on('click', '#imgClose', function () {
	    $('.user-meta-information').hide();
	});

	$('body').on('click', '#updateUserMetaInformation', function (e) {
	    e.preventDefault();
	    var updateMetaInformation = [];
	    var updatedMetaData = {};	
	    var userMetaInfoTableRow = $('#UMMUserMetaInformation tr');
	    
	    // Storing all keys and values of dialog form into the updateMetaInformation array
	    for (var i = 1; i < userMetaInfoTableRow.length; i++) {
		var key = $($(userMetaInfoTableRow[i]).find('input[type="text"]')[0]).val();
		var value = $($(userMetaInfoTableRow[i]).find('input[type="text"]')[1]).val();
		updateMetaInformation[key] = value;
	    }
	    // Filtering newly added meta keys and values and storing in updatedMetaData array.
	    for (var key in updateMetaInformation) {
		if (metaInformation[key] !== updateMetaInformation[key].trim() && updateMetaInformation[key].trim() !== '') {
		    updatedMetaData[key] = updateMetaInformation[key];
		}
	    }
	    var data = {
		action: 'UMM_update_user_meta_data',
		security: UMMData.ajax_nonce,
		userId: userId,
		UMMData: updatedMetaData
	    };
	    ajaxCall(data, showMessage);
	});

	$('body').on('click', '#UMMAddUserMetaInformation', function () {
	    addMetaKeyValue();
	});

	$('body').on('click', '#deleteUserMetaInformation', function () {
	    deleteUserMetaInformation();
	});

	/**
	 * Method to add styles to the alert message dialog boxes.
	 * @param {type} currentDialog will have current dialog object
	 * @returns {undefined}
	 */
	function designAlertDialogBoxes(currentDialog) {
	    
	    if(currentDialog.hasClass('dialog-success')) {
		$('#updateUserMetaInformation').val('Update');
		metaFormDialog.dialog("close");
	    }
	    var alertDialogs = ['dialog-info', 'dialog-warning', 'dialog-danger', 'dialog-success'];
	    var alertDialogTitleBars = ['ui-info-dialog-titlebar', 'ui-warning-dialog-titlebar', 'ui-danger-dialog-titlebar', 'ui-success-dialog-titlebar'];
	    // Checking the type of alert dialog box and adding respective title bar, icon and class required
	    for (var i = 0; i < alertDialogs.length; i++) {
		if (currentDialog.hasClass(alertDialogs[i])) {
		    var $currentTitleBar = currentDialog.parent().find('.ui-dialog-titlebar');
		    if (currentDialog.parent().find(alertDialogTitleBars[i]).length === 0) {
			$currentTitleBar.addClass(alertDialogTitleBars[i]);
		    }
		    if (currentDialog.parent().find('.fa-envelope').length === 0) {
			$currentTitleBar.append('<i class="fa fa-envelope fa-2x"></i>');
		    }
		}
	    }
	}

	// Function to show the messages on to alert dialog boxes
	function showMessage(data) {
	    var json = JSON.parse(data);
	    if (json['error']) {
		var container = $('#UMMModalInfo').find('.dialog-body');
		$(container).html(json['error']);
		$('#UMMModalInfo').dialog().dialog('open');
	    } else {
		var container = $('#modalSuccess').find('.dialog-body');
		$(container).html(json['success']);
		$('#modalSuccess').dialog().dialog('open');
	    }
	}

	// Function to get the user meta information.
	function  getUserMetaInformation() {
	    var data = {
		action: "UMM_get_user_meta_details",
		security: UMMData.ajax_nonce,
		userId: userId
	    };
	    ajaxCall(data, showMetaDetails);
	}

	// Function to delete the User meta information from the meta list available..
	function deleteUserMetaInformation() {
	    var deleteMetaInformation = {};
	    var userMetaInfoTableRow = $('#UMMUserMetaInformation tr');
	    for (var i = 1; i < userMetaInfoTableRow.length; i++) {
		var check = $($(userMetaInfoTableRow[i]).find('input:checked'));
		if (check.length) {
		    var key = $($(userMetaInfoTableRow[i]).find('input')[1]).val();
		    var value = $($(userMetaInfoTableRow[i]).find('input')[2]).val();
		    deleteMetaInformation[key] = value;
		}
	    }
	    var data = {
		action: 'UMM_delete_user_meta',
		security: UMMData.ajax_nonce,
		userId: userId,
		UMMData: deleteMetaInformation
	    };
	    ajaxCall(data, showMessage);

	}

	// Function to show the user meta details.
	function showMetaDetails(result) {
	    metaInformation = [];
	    $('#UMMUserMetaInformation').DataTable().destroy();
	    $('#userMetaDetails').html('');
	    $('#userMetaDetails').html(result);
	    $('.user-meta-information').show();
	    $('#UMMUserMetaInformation').dataTable({
		dom: '<"top">rt<"bottom">',
		columnDefs: [
		    {"width": "180px", "targets": 1}
		],
		autoWidth: false,
		paging: false,
		scrollY: "400px",
		scrollCollapse: false,
		sortable: false,
		bSort: false
	    });

	    var userMetaInfoTableRow = $('#UMMUserMetaInformation tr');	    
	    for (var i = 1; i < userMetaInfoTableRow.length; i++) {
		var key = $($(userMetaInfoTableRow[i]).find('input[type="text"]')[0]).val();
		var value = $($(userMetaInfoTableRow[i]).find('input[type="text"]')[1]).val();
		metaInformation[key] = value;
	    }
	}

	// Function to show the meta information of the user.
	function showmetaLIst(result) {
	    $('#UMMUsersMeta').DataTable().destroy();
	    $('#UMMUserMeta').DataTable().clear().draw();
	    $('.user-meta-information').remove();
	    $('.umm-meta-results').html('');
	    $('.meta-table-results').html(result);
	    $('.user-meta-information').show();
	    $('#UMMUsersMeta').dataTable({
		dom: '<"top">rt<"bottom" pl>',
		language: {
		    emptyTable: "There are no users with the specificied meta combination."
		},
		scrollX: true,
		autoWidth: false,
		order: [[0, "asc"]],
	        columnDefs: [
		    {"width": "7%", "targets": 0},
		    {"width": "30%", "targets": 1},
		    {"width": "25%", "targets": 2},
		    {"width": "25%", "targets": 3},
		    {"width": "13%", "targets": 4}
	        ]
	    });
	}

	// Function to get the list of users who has meta key and value matched.
	function getMetaMatchLIst() {
	    $('#txtMetaKey').removeClass('error');
	    var metakey = $('#txtMetaKey').val();
	    var metaValue = $('#txtMetaValue').val();
	    var data = {
		action: 'UMM_meta_search',
		security: UMMData.ajax_nonce,
		metaKey: metakey,
		metaValue: metaValue
	    };
	    ajaxCall(data, showmetaLIst);
	}

	// Function to make a ajax call
	function ajaxCall(data, cbFunction, chidCbFunc) {
	    $.ajax({
		url: UMMData.ajaxurl,
		type: "post",
		data: data,
		timeout: 10000, 
		success: function (result) {
		    if (result !== '-1') {
			if (chidCbFunc) {
			    chidCbFunc();
			}
			cbFunction(result);
		    } else {
			location.reload();			
		    }
		},
		error: function (xhr) {
		    $.notify('Unable to process your request', "error");
		}
	    });
	}

	// Function to append the new meta key and value to the existing meta information.
	function addMetaKeyValue() {
	    var appendContent = '<tr id="div-16"><td><input  type="checkbox" name="meta[]"></td>';
	    appendContent += '<td><input type="text" placeholder="Meta key..."></td>';
	    appendContent += '<td><input type="text" placeholder="Meta value..." </td></tr>';
	    $('#UMMUserMetaInformation').append(appendContent);
	    $('html, .dataTables_scrollBody').animate({scrollTop: $('#UMMUserMetaInformation tr:last').offset().top}, 500);
	    $('#updateUserMetaInformation').val('Save');
	}
    });
    $(UMMUserMetaManager);
})(jQuery);
