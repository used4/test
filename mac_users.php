<?php 
include ('includes/header.php');

//table name
$table_name = "ibo";
$pagem = "mac_users.php";
$newuser = "users_create.php";
$updateuser = "users_edite.php";
$addtrilas = "setup_trial.php?create";

$results_per_page = 30;

if (isset($_GET['view'])) {
    $page = $_GET['view'];
} else {
    $page = 1;
}

$start_from = ($page - 1) * $results_per_page;

// Search functionality
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$searchQuery = '';
$placeholders = array();

if (!empty($searchTerm)) {
    $searchQuery = "mac_address LIKE :searchTerm OR title LIKE :searchTerm";
    $placeholders[':searchTerm'] = "%$searchTerm%";
}

// Retrieve total count of records based on the search criteria
$countResult = $db->selectWithCount($table_name, "id", $searchQuery, $placeholders);
$totaleview = $countResult[0]['total'];
$total_pages = ceil($totaleview / $results_per_page);

// Fetch records based on the search filter
$res = $db->select($table_name, '*', $searchQuery, 'id ASC LIMIT :start_from, :results_per_page', array_merge($placeholders, [':start_from' => $start_from, ':results_per_page' => $results_per_page]));

//delete row
if(isset($_GET['delete'])){
    $db->delete($table_name, 'id = :id',[':id' => $_GET['delete']]);
    echo "<script>window.location.href='".$pagem."?status=2'</script>";
}

?>
<style>
.pagination-scroll {
    overflow-x: auto;
    white-space: nowrap;
    padding: 10px 0;
}
.pagination-scroll a {
    display: inline-block;
    margin-left: 3px;
    margin-right: 3px;
    background: red;
    color: white !important;
    padding: 6px 12px;
    border-radius: 4px;
    text-decoration: none;
}
.pagination-scroll a.active {
    background: #b30000 !important;
}
</style>

<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background-color: black;">
            <div class="modal-header">
                <h2 style="color: white;">Confirm</h2>
            </div>
            <div class="modal-body" style="color: white;">
                Do you really want to delete?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
                <a style="color: white;" class="btn btn-danger btn-ok">Delete</a>
            </div>
        </div>
    </div>
</div>

<div class="col-md-12 mx-auto ctmain-table">
    <div class="card-body">
        <div class="card ctcard">
            <div class="card-header card-header-warning">
                <center>
                    <h2><i class="icon icon-commenting"></i> Current Users</h2>
                </center>
            </div>
            <div class="card-body">
                <div class="col-12">
                    <center>
                        <a id="button" href="./<?=$newuser ?>" class="btn btn-info">New DNS/User</a>
                    </center>
                </div>
                <br><br>

                <form method="get" class="mb-3">
                    <div class="form-group ctinput">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search by Mac Address or Title">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">Search</button>
                            </div>
                        </div>
                    </div>
                </form>

                <br>

                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead style="color:white!important">
                            <tr>
                                <th>Title</th>
                                <th>Mac Address</th>
                                <th>Username</th>
                                <th>Protect This</th>
                                <th>DNS</th>
                                <th>Edit&nbsp;&nbsp;&nbsp;Add Trial&nbsp;&nbsp;&nbsp;Delete</th>
                            </tr>
                        </thead>

                        <?php foreach ($res as $row) { ?>
                        <tbody>
                            <tr>
                                <td><?=$row['title'] ?></td>
                                <td><?=$row['mac_address'] ?></td>
                                <td><?=$row['username'] ?></td>
                                <td><?=$row['protection'] == '1' ? 'YES' : 'NO' ?></td>
                                <td><?=$row['url'] ?></td>
                                <td>
                                    <a class="btn btn-info btn-ok" href="<?=$updateuser ?>?update=<?=$row['id'] ?>">
                                        <i class="fa fa-pencil-square-o"></i>
                                    </a>
                                    &nbsp;&nbsp;&nbsp;
                                    <a class="btn btn-info btn-ok" href="<?=$addtrilas ?>&index=<?=$row['mac_address'] ?>">
                                        <i class="fa fa-calendar"></i>
                                    </a>
                                    &nbsp;&nbsp;&nbsp;
                                    <a class="btn btn-danger btn-ok" href="#" data-href="<?=$pagem ?>?delete=<?=$row['id'] ?>" data-toggle="modal" data-target="#confirm-delete">
                                        <i class="fa fa-trash-o"></i>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                        <?php } ?>

                    </table>
                </div>

                <?php if ($results_per_page < $totaleview) { ?>
                <div class="pagination-scroll">

                    <?php if ($page > 1) { ?>
                        <a href="<?=$pagem ?>?view=<?=($page - 1)?>&search=<?=$searchTerm ?>">&lt; Prev</a>
                    <?php } ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                        <a class="<?=($i == $page ? 'active' : '')?>" 
                           href="<?=$pagem ?>?view=<?=$i?>&search=<?=$searchTerm ?>">
                           <?=$i?>
                        </a>
                    <?php } ?>

                    <?php if ($page < $total_pages) { ?>
                        <a href="<?=$pagem ?>?view=<?=($page + 1)?>&search=<?=$searchTerm ?>">Next &gt;</a>
                    <?php } ?>

                </div>
                <?php } ?>

            </div>
        </div>
    </div>
</div>

<?php include ('includes/footer.php');?>

</body>
</html>
