var userMetaManager = (function () {
    var metaInformation = [];
    var userId;
    $('#usersMeta').dataTable({
        "dom": '<"top">rt<"bottom" pl>',
        "order": [[0, "asc"]],
        'scrollX': true,
        "autoWidth": false
    });

    $('#submitMeta').click(function (e) {
        e.preventDefault();
        if ($('#txtMetaKey').val() !== '') {
            getMetaMatchLIst();
        } else {
            $('#txtMetaKey').addClass('error');
        }
    });

    $('body').on('click', '.user-mail', function (e) {
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
            'action': 'update_user_meta_data',
            'security': myAjax.ajax_nonce,
            'userId': userId,
            'userMetaData': updatedMetaData
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
        jQuery('#myModal').modal('hide');
        console.log(json.error);
        if (json['error']) {
            var container = jQuery('#modalnfo').find('.modal-body');
            jQuery(container).html(json['error']);
            jQuery('#modalnfo').modal('show');
        }
        else {
            var container = jQuery('#modalSuccess').find('.modal-body');
            jQuery(container).html(json['success']);
            jQuery('#modalSuccess').modal('show');
        }
    }
    // Function to get the user meta information.
    function  getUserMetaInformation() {
        var data = {
            action: "get_user_meta_details",
            'security': myAjax.ajax_nonce,
            "userId": userId
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
            'action': 'delete_user_meta',
            'security': myAjax.ajax_nonce,
            'userId': userId,
            'userMetaData': deleteMetaInformation
        };
        ajaxCall(data, showMessage);

    }

// Function to shoe the user meta details.
    function showMetaDetails(result) {
        metaInformation = [];
        jQuery('#userMetaInformation').DataTable().destroy();
        jQuery('#userMetaDetails').html(result);
        jQuery('#myModal').modal('show');
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
            action: "meta_search",
            security: myAjax.ajax_nonce,
            "metaKey": metakey,
            "metaValue": metaValue
        };
        ajaxCall(data, showmetaLIst);
    }

// Function to make a ajax call
    function ajaxCall(data, cbFunction, chidCbFunc) {
        jQuery.ajax({
            url: myAjax.ajaxurl,
            type: "post",
            data: data,
            success: function (result) {
                if (chidCbFunc) {
                    chidCbFunc();
                }
                cbFunction(result);
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
