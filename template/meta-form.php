<div class="meta-form">
    <form class="form-inline" role="form" id="userMetaForm">
        <div class="form-group">
            <label for="txtMetaKey">Meta key : </label>
            <input type="text" id="txtMetaKey" name="meta-key">
        </div>
        <div class="form-group">
            <label for="txtMetaValue">Meta value :</label>
            <input type="text" id="txtMetaValue" name="meta-value">
        </div>
        <input type="submit" name="meta-search" value="Search" id="submitMeta" class="btn btn-primary">
        <button name="meta-add" id="btnAddMeta" class="btn btn-primary add-meta-btn">Add Meta</button>
        <div class="dropdown add-meta-btn" style="display: inline;">
            <button id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" >              
                <span class="glyphicon glyphicon-th-list"></span>
            </button>
            <ul class="dropdown-menu" aria-labelledby="dLabel">
                <li><a class="filtered-list">Add user Meta to filtered list</a></li>
            </ul>
        </div>
    </form>
</div>

<div class="meta-table-results"></div>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <button type="button" id="addUserMetaInformation" class="btn btn-primary">Add Meta Key</button>

                <h4 class="modal-title" id="myModalLabel">User meta information</h4>
            </div>
            <div class="modal-body" id="userMetaDetails">
            </div>
            <div class="modal-footer">
                <button type="button" id="updateUserMetaInformation" class="btn btn-primary btn-space">Update</button>
                <button type="button" id="deleteUserMetaInformation" class="btn btn-primary btn-space">Delete</button>
            </div>
        </div>
    </div>
</div>
<div id="modalSuccess" class="modal modal-message modal-success fade" style="display: none;" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content message-container">
            <div class="modal-header">
                <i class="glyphicon glyphicon-check"></i>
            </div>
            <div class="modal-title">Success</div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal">OK</button>
            </div>
        </div> <!-- / .modal-content -->
    </div> <!-- / .modal-dialog -->
</div>
<!--End Success Modal Templates-->
<!--Info Modal Templates-->
<div id="modalnfo" class="modal modal-message modal-info fade" style="display: none;" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content message-container">
            <div class="modal-header">
                <i class="glyphicon glyphicon-envelope"></i>
            </div>
            <div class="modal-title">Alert</div>

            <div class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-info" data-dismiss="modal">OK</button>
            </div>
        </div> <!-- / .modal-content -->
    </div> <!-- / .modal-dialog -->
</div>
<!--End Info Modal Templates-->
<!--Danger Modal Templates-->
<div id="modalDanger" class="modal modal-message modal-danger fade" style="display: none;" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content message-container">
            <div class="modal-header">
                <i class="glyphicon glyphicon-fire"></i>
            </div>
            <div class="modal-title">Danger</div>

            <div class="modal-body">You've done bad!</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">OK</button>
            </div>
        </div> <!-- / .modal-content -->
    </div> <!-- / .modal-dialog -->
</div>
<!--End Danger Modal Templates-->
<!--Danger Modal Templates-->
<div class="modal modal-message modal-warning fade" style="display: none;" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content message-container">
            <div class="modal-header">
                <i class="glyphicon glyphicon-warning"></i>
            </div>
            <div class="modal-title">Add Meta</div>

            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning"  data-dismiss="modal">OK</button>
            </div>
        </div> <!-- / .modal-content -->
    </div> <!-- / .modal-dialog -->
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="modalUserMeta" >
    <div class="modal-dialog modal-dialog-custom">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Add Meta Information</h4>
            </div>
            <div class="modal-body">


                <form role="form">
                    <div class="form-group">
                        <label for="txtMetaUserKey">Meta Key:</label>
                        <input type="text"  id="txtMetaUserKey" placeholder="Enter the Meta Key..." class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="txtMetaUserValue">Meta Value:</label>
                        <input type="text"  id="txtMetaUserValue" placeholder="Enter the Meta Value ..." class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="ddlUsers">Users</label>
                            <select id="ddlUsers" class="chosen-select" multiple ></select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"  id="btnAddUserMeta" data-dismiss="modal">Add</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="mdlUserFilterMeta" >
    <div class="modal-dialog modal-dialog-custom">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Add Meta Information</h4>
            </div>
            <div class="modal-body">


                <form role="form">
                    <div class="form-group">
                        <label for="txtFilterMetaUserKey">Meta Key:</label>
                        <input type="text"  id="txtFilterMetaUserKey" placeholder="Enter the Meta Key..." class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="txtFilterMetaUserValue">Meta Value:</label>
                        <input type="text"  id="txtFilterMetaUserValue" placeholder="Enter the Meta Value ..." class="form-control">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"  id="btnAddFilterUserMeta" data-dismiss="modal">Add</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->