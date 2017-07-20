<?php
require_once( __DIR__ . '/../config.php');
?>
<div class="umm-meta-results">
    <table id="UMMUsersMeta" class="umm-table table-striped">
        <thead>
            <tr>
                <th>ID<i class="fa fa-fw fa-sort"></i></th>
		<th>Email<i class="fa fa-fw fa-sort"></i></th>
                <th>First name<i class="fa fa-fw fa-sort"></i></th>
                <th>Last name<i class="fa fa-fw fa-sort"></i></th>                
                <th>Role<i class="fa fa-fw fa-sort"></i></th>
            </tr>    
        </thead>
        <tbody>
            <?php
            if (count($inputData)) {
                for ($i = 0; $i < count($inputData); $i++) {
                    ?>
                    <tr>			
                        <td id="userId"><a title="Click to view the user meta data dialog box" class="umm-user-id"><?php echo $inputData[$i]['id']; ?></a></td>
			<td><a class="user-mail" href="mailto:<?= $inputData[$i]['email']?>"><?php echo $inputData[$i]['email']; ?></a></td>
                        <td><?php echo $inputData[$i]['firstName']; ?></td>
                        <td><?php echo $inputData[$i]['lastName']; ?></td>                        
                        <td><?php echo ucfirst($inputData[$i]['role']); ?></td>
                    </tr>
                    <?php
                }
            } else {
                ?>
            <?php } ?>
        </tbody>
    </table>
    <div class="user-meta-information"></div>
</div>

