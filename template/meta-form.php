<div class="umm-meta-form">
    <form class="form-inline" role="form" id="userMetaForm">
        <div class="form-group">
            <label for="txtMetaKey">Meta key : </label>
            <input type="text" id="txtMetaKey" name="meta-key">
        </div>
        <div class="form-group">
            <label for="txtMetaValue">Meta value :</label>
            <input type="text" id="txtMetaValue" name="meta-value">
        </div>
	<div class="umm-btn-group">
	    <input type="submit" name="meta-search" value="Search" id="submitMeta" class="umm-btn umm-btn-primary">
	    <input type="button" value="Reset" id="resetMeta" class="umm-btn umm-btn-warning">
	</div>        
    </form>
</div>
<div class="meta-table-results"></div>
<div id="UMMDialogForm">
    <form>
	<div id="userMetaDetails">
	</div>
	<div class="umm-text-right">
	    <input type="submit" id="updateUserMetaInformation"  class="umm-btn umm-btn-primary btn-space"  value="Update" tabindex="-1">
	    <input type="button" id="deleteUserMetaInformation" class="umm-btn umm-btn-danger btn-space" value="Delete" tabindex="-1">
	</div>
    </form>    
</div>
<div class="alert-dialog-boxes">
<div id="modalSuccess" class="umm-dialog-alert dialog-success" style="display: none;">
    <div class="dialog-content">
        <div class=" message-container">
            <div class="dialog-title">Success</div>
	    <div class="dialog-body">You have updated meta information successfully</div>	    
            <div class="dialog-footer">
                <button type="button" class="umm-btn umm-btn-success close-dialog">OK</button>
            </div>
        </div>
    </div>
</div>
<div id="UMMModalInfo" class="umm-dialog-alert dialog-info" style="display: none;">
    <div class="dialog-content">
        <div class=" message-container">
            <div class="dialog-title">Alert</div>
	    <div class="dialog-body">There is nothing to update</div>	    
            <div class="dialog-footer">
                <button type="button" class="umm-btn umm-btn-info close-dialog">OK</button>
            </div>
        </div>
    </div>
</div>
<div id="modalDanger" class="umm-dialog-alert dialog-danger" style="display: none;">
    <div class="dialog-content">
        <div class=" message-container">
            <div class="dialog-title">Danger</div>
	    <div class="dialog-body">You've done bad!</div>	    
            <div class="dialog-footer">
                <button type="button" class="umm-btn umm-btn-danger close-dialog">OK</button>
            </div>
        </div>
    </div>
</div>
<div id="modalWarning" class="umm-dialog-alert dialog-warning" style="display: none;">
    <div class="dialog-content">
        <div class=" message-container">
            <div class="dialog-title">Warning</div>
	    <div class="dialog-body">Is something wrong?</div>	    
            <div class="dialog-footer">
                <button type="button" class="umm-btn umm-btn-warning close-dialog">OK</button>
            </div>
        </div>
    </div>
</div>
</div>
