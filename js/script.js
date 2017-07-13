(function($) {
var userMetaManager = (function () {
    var metaInformation = [];
    var userId;
       
    $(".dialog-info, .dialog-warning, .dialog-danger, .dialog-success").dialog({autoOpen: false, modal:true});   
    $(".close-dialog").click(function (){
	$(".dialog-info, .dialog-warning, .dialog-danger, .dialog-success").dialog('close');	
    });   
    
//    ('<i class="fa fa-envelope fa-x"></i>')
    
    var dialog = $("#dialogForm").dialog({
      autoOpen: false,
      resizable: false,
      width: 'auto',
//      position: ['center',20],
//      width: 700,
      modal: true,
      buttons: {
        Cancel: function() {
          dialog.dialog("close");
        }
      },
      open: function () {
//	  $(this).dialog('option', 'position', 'top');
	if ($(this).parent().find('.ui-dialog-titlebar .dialog-form-heading').length === 0) {
	    $(this).parent().find('.ui-dialog-titlebar').addClass('dialog-form-header').append('<span class="dialog-form-heading">User meta information</span><button type="button" id="addUserMetaInformation"   class="btn btn-primary text-right">Add Meta Key</button>');
	}
      },      
      close: function() {
	
	var alertDialogs = ['.dialog-info', '.dialog-warning', '.dialog-danger', '.dialog-success'];
	var alertDialogTitleBars = ['ui-info-dialog-titlebar', 'ui-warning-dialog-titlebar', 'ui-danger-dialog-titlebar', 'ui-success-dialog-titlebar'];
	for(var i=0; i<alertDialogs.length; i++) {
	    $alertDialog = $(alertDialogs[i]).parent().find('.ui-dialog-titlebar'); 
	    if($alertDialog.length > 0) {
		$alertDialog.removeClass('ui-info-dialog-titlebar ui-warning-dialog-titlebar ui-danger-dialog-titlebar ui-success-dialog-titlebar').addClass(alertDialogTitleBars[i]);
		if($alertDialog.find('.fa-envelope').length === 0) {
		    $alertDialog.append('<i class="fa fa-envelope fa-2x"></i>');
		}	
	    }
	}
      }
    });
        
    jQuery("a.user-mail").click(function (){
	dialog.dialog('open');
    });
    
    $('#usersMeta').dataTable({
        "dom": '<"top">rt<"bottom" pl>',
        "order": [[0, "asc"]],
        'scrollX': true,
        "autoWidth": false,
	"columnDefs": [{ 
		"width": "20%", "targets": 0,
		"width": "20%", "targets": 1,
		"width": "20%", "targets": 2,
		"width": "20%", "targets": 3,
		"width": "20%", "targets": 4
	    }]
    });

    $('#submitMeta').click(function (e) {
        e.preventDefault();
        if ($('#txtMetaKey').val() !== '') {
            getMetaMatchLIst();
        } else {
            $('#txtMetaKey').addClass('error');
        }
    });

    jQuery('body').on('click', '.user-mail', function (e) {
        e.preventDefault();
        jQuery('.user-meta-results').html('');
        userId = +($($(this).parents('tr').find('#userId')).html());
        getUserMetaInformation();
    });

    $('body').on('click', '#imgClose', function () {
        jQuery('.user-meta-information').hide();
    });

    $('body').on('click', '#updateUserMetaInformation', function () {
        var updateMetaInformation = [];
        var updatedMetaData = {};
        for (var i = 1; i < jQuery('#userMetaInformation tr').length; i++) {
            var key = jQuery(jQuery(jQuery('#userMetaInformation tr')[i]).find('input[type="text"]')[0]).val();
            var value = jQuery(jQuery(jQuery('#userMetaInformation tr')[i]).find('input[type="text"]')[1]).val();
            updateMetaInformation[key] = value;
        }
        for (var key in updateMetaInformation) {
            if (metaInformation[key] !== updateMetaInformation[key]) {
                updatedMetaData[key] = updateMetaInformation[key];
            }
        }
        var data = {
            action: 'update_user_meta_data',
            security: UMMData.ajax_nonce,
            userId: userId,
            UMMData: updatedMetaData
        };
        ajaxCall(data, showMessage);


    });

    $('body').on('click', '#addUserMetaInformation', function () {
        addMetaKeyValue();
    });

    $('body').on('click', '#deleteUserMetaInformation', function () {
        deleteUserMetaInformation();
    });

    // Function to show the
    function showMessage(data) {
        var json = JSON.parse(data);
        dialog.dialog('close');
        console.log(json.error);
        if (json['error']) {
            var container = jQuery('#modalInfo').find('.modal-body');
            jQuery(container).html(json['error']);
	    jQuery('#modalInfo').dialog();
            jQuery('#modalInfo').dialog('open');
	}
        else {
            var container = jQuery('#modalSuccess').find('.modal-body');
            jQuery(container).html(json['success']);
	    jQuery('#modalSuccess').dialog();
            jQuery('#modalSuccess').dialog('open');
        }
    }
    // Function to get the user meta information.
    function  getUserMetaInformation() {
        var data = {
            action: "get_user_meta_details",
            security: UMMData.ajax_nonce,
            userId: userId
        };
        ajaxCall(data, showMetaDetails);
    }

    // Function to delete the User meta information from the meta list available..
    function deleteUserMetaInformation() {
        var deleteMetaInformation = {};
        for (var i = 1; i < jQuery('#userMetaInformation tr').length; i++) {
            var check = jQuery(jQuery(jQuery('#userMetaInformation tr')[i]).find('input:checked'));
            if (check.length) {
                var key = jQuery(jQuery(jQuery('#userMetaInformation tr')[i]).find('input')[1]).val();
                var value = jQuery(jQuery(jQuery('#userMetaInformation tr')[i]).find('input')[2]).val();
                deleteMetaInformation[key] = value;
            }
        }
        var data = {
            action: 'delete_user_meta',
            security: UMMData.ajax_nonce,
            userId: userId,
            UMMData: deleteMetaInformation
        };
        ajaxCall(data, showMessage);

    }

// Function to shoe the user meta details.
    function showMetaDetails(result) {
        metaInformation = [];
        jQuery('#userMetaInformation').DataTable().destroy();
        jQuery('#userMetaDetails').html(result);
//        dialog.dialog('open');
        jQuery('.user-meta-information').show();
        jQuery('#userMetaInformation').dataTable({
            "dom": '<"top">rt<"bottom">',
            "columnDefs": [
                {"width": "180px", "targets": 1}
            ],
            "autoWidth": false,
            "paging": false,
            "scrollY": "400px",
            "scrollCollapse": false,
             sortable: false,
            "bSort": false
        });

        for (var i = 1; i < jQuery('#userMetaInformation tr').length; i++) {
            var key = jQuery(jQuery(jQuery('#userMetaInformation tr')[i]).find('input[type="text"]')[0]).val();
            var value = jQuery(jQuery(jQuery('#userMetaInformation tr')[i]).find('input[type="text"]')[1]).val();
            metaInformation[key] = value;
        }
    }

    // Function to show the meta information of the user.
    function showmetaLIst(result) {
        jQuery('#usersMeta').DataTable().destroy();
        jQuery('#userMeta').DataTable().clear().draw();
        jQuery('.user-meta-information').remove();
        jQuery('.meta-results').html('');
        jQuery('.meta-table-results').html(result);
        jQuery('.user-meta-information').show();
        $('#usersMeta').dataTable({
            "dom": '<"top">rt<"bottom" pl>',
            "language": {
                "emptyTable": "There are no users with the specificied meta combination."
            },
            'scrollX': true,
            "autoWidth": false
        });
    }

    // Function to get the list of users who has meta key and value matched.
    function getMetaMatchLIst() {
        jQuery('#txtMetaKey').removeClass('error');
        var metakey = jQuery('#txtMetaKey').val();
        var metaValue = jQuery('#txtMetaValue').val();
        var data = {
            action: 'meta_search',
            security: UMMData.ajax_nonce,
            metaKey: metakey,
            metaValue: metaValue
        };
        ajaxCall(data, showmetaLIst);
    }

// Function to make a ajax call
    function ajaxCall(data, cbFunction, chidCbFunc) {
        jQuery.ajax({
            url: UMMData.ajaxurl,
            type: "post",
            data: data,
            success: function (result) {
                if (result !== '-1') {
                    if (chidCbFunc) {
                        chidCbFunc();
                    }
                    cbFunction(result);
                }
                else {
                    location.reload();
                }
            },
            error: function (xhr) {
                console.log(xhr);
                jQuery.notify('Unable to process your request', "error");
            }
        });
    }


// Function to append the new meta key and value to the existing meta information.
    function addMetaKeyValue() {
        var appendContent = '<tr id="div-16"><td><input  type="checkbox" name="meta[]"></td>';
        appendContent += '<td><input type="text" placeholder="  Meta key....."></td>';
        appendContent += '<td><input type="text" placeholder=" Meta value...." </td></tr>';
        jQuery('#userMetaInformation').append(appendContent);
        jQuery('html, .dataTables_scrollBody').animate({scrollTop: jQuery('#userMetaInformation tr:last').offset().top}, 500);
        jQuery('#updateUserMetaInformation').html('Save');

    }
});
jQuery(userMetaManager);
})( jQuery );
