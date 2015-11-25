<table id="userMetaInformation" class=" table table-striped table-bordered">
    <thead>
    <th>Meta key</th>
    <th>Value</th>
</thead>
<tbody >
    <?php foreach ($inputData as $key => $value) { ?>
        <tr>
            <td><input type="text"  disabled="disabled" value="<?php echo $key; ?>"></td>
            <td><input type="text"  value="<?php echo $inputData[$key]; ?>"></td>
        </tr>
    <?php } ?>
</tbody>
</table>