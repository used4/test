<?php 
include ('includes/header.php');

//table name
$table_name = "ibocode";
$pagem = "activation_code.php";
$newuser = "code_create.php";

$results_per_page = 6;

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
    $searchQuery = "ac_code LIKE :searchTerm OR username LIKE :searchTerm";
    $placeholders[':searchTerm'] = "%$searchTerm%";
}

// Retrieve total count of records based on the search criteria
$countResult = $db->selectWithCount($table_name, "id", $searchQuery, $placeholders);
$totaleview = $countResult[0]['total'];
$total_pages = ceil($totaleview / $results_per_page);

// Fetch records based on the search filter
$res = $db->select(
    $table_name,
    '*',
    $searchQuery,
    'id ASC LIMIT :start_from, :results_per_page',
    array_merge($placeholders, [
        ':start_from' => $start_from,
        ':results_per_page' => $results_per_page
    ])
);

//delete row
if(isset($_GET['delete'])){
    $db->delete($table_name, 'id = :id',[':id' => $_GET['delete']]);
    echo "<script>window.location.href='".$pagem."?status=2'</script>";
}

?>
<style>
  .pagination-gap {
    margin-left: 2px; 
    margin-right: 2px; 
    background-color: red; 
    color: white; 
    padding: 5px 10px;
    border-radius: 6px;
    flex: 0 0 auto;
  }

  .pagination-red {
    margin-left: 2px; 
    margin-right: 2px; 
    background-color: darkred; 
    color: white; 
    text-align: center; 
    padding: 5px 10px;
    border-radius: 6px;
    flex: 0 0 auto;
    font-weight: bold;
  }

  .text-color{
    color: white;
  }

  /* جعل الصفحات سلايدر */
  .pagination {
    display: flex;
    overflow-x: auto;
    white-space: nowrap;
    padding: 10px 0;
    scrollbar-color: #555 #222;
  }

  .pagination::-webkit-scrollbar {
    height: 6px;
  }

  .pagination::-webkit-scrollbar-thumb {
    background: #555;
    border-radius: 10px;
  }

  .pagination::-webkit-scrollbar-track {
    background: #222;
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
                    <h2><i class="icon icon-commenting"></i> Activation code</h2>
                </center>
            </div>
            <div class="card-body">
                <div class="col-12">
                    <center>
                        <a id="button" href="./<?=$newuser ?>" class="btn btn-info">New Activation code</a>
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
                                <th>Activation code</th>
                                <th>User status</th>
                                <th>DNS</th>
                                <th>&nbsp;&nbsp;&nbsp;Delete</th>
                            </tr>
                        </thead>

                        <?php foreach ($res as $row) { ?>
                        <tbody>
                            <tr>
                                <td><?=$row['ac_code'] ?></td>
                                <td style="color: <?= $row['status'] == 'NotUsed' ? 'green' : 'red' ?>"><?= $row['status'] ?></td>
                                <td><?=$row['url'] ?></td>
                                <td>
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
                    <div class="pagination">
                        <?php if ($page > 1) { ?>
                            <a class="pagination-gap" href="<?=$pagem ?>?view=<?=$page - 1 ?>&search=<?=$searchTerm ?>">&lt; Previous</a>
                        <?php } ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                            <?php if ($i == $page) { ?>
                                <a class="pagination-red active" href="<?=$pagem ?>?view=<?=$i ?>&search=<?=$searchTerm ?>">[<?=$i ?>]</a>
                            <?php } else { ?>
                                <a class="pagination-gap" href="<?=$pagem ?>?view=<?=$i ?>&search=<?=$searchTerm ?>"><?=$i ?></a>
                            <?php } ?>
                        <?php } ?>

                        <?php if ($page < $total_pages) { ?>
                            <a class="pagination-gap" href="<?=$pagem ?>?view=<?=$page + 1 ?>&search=<?=$searchTerm ?>">Next &gt;</a>
                        <?php } ?>
                    </div>
                <?php } ?>

            </div>
        </div>
    </div>
</div>

<?php include ('includes/footer.php'); ?>
</body>
</html>
