<table id="UMMUserMetaInformation" class="umm-table table-striped table-bordered">
    <thead>
        <tr>
            <th></th>
            <th>Meta key</th>
            <th>Value</th>
        </tr>
    </thead>
    <tbody >
        <?php 
      require_once( __DIR__ . '/../config.php');
      $deleteExemptedList = unserialize(UMM_DELETE_EXEMPTED_LIST);
        foreach ($inputData as $key => $value) { ?>
            <tr>
                <?php 
            if(in_array($key, $deleteExemptedList) )
            { 
            ?>
                <td></td>
           <?php  }else{?>
            
           <td><input  type="checkbox" name="meta[]"></td><?php }?>
                <td><input type="text"  disabled="disabled" value="<?php echo $key; ?>"></td>
                <td><input type="text"  value="<?php echo $inputData[$key]; ?>"></td>
            </tr>
        <?php } ?>
    </tbody>
</table>