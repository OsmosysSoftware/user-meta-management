<?php
require_once( __DIR__ . '/../config.php');
?>
<div class="meta-results">
    <table id="usersMeta" class="table table-striped">
        <thead>
            <tr>
                <th>ID<i class="fa fa-fw fa-sort"></i></th>
                <th>First name<i class="fa fa-fw fa-sort"></i></th>
                <th>Last name<i class="fa fa-fw fa-sort"></i></th>
                <th>Email<i class="fa fa-fw fa-sort"></i></th>
                <th>Role<i class="fa fa-fw fa-sort"></i></th>
            </tr>    
        </thead>
        <tbody>
            <?php
            if (count($inputData)) {
                for ($i = 0; $i < count($inputData); $i++) {
                    ?>
                    <tr>
                        <td id="userId"><?php echo $inputData[$i]['id']; ?></td>
                        <td><?php echo $inputData[$i]['firstName']; ?></td>
                        <td><?php echo $inputData[$i]['lastName']; ?></td>
                        <td><a class="user-mail"><?php echo $inputData[$i]['email']; ?></a></td>
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

