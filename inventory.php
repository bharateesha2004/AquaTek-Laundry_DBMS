<?php include 'db_connect.php' ?>
<div class="container-fluid">
	<div class="col-lg-12">
		<div class="row">
			<div class="col-md-5">
				<div class="card">
					<div class="card-header">
						<h4><b>Inventory</b></h4>
					</div>
					<div class="card-body">
						<table class="table table-bordered">
							<thead>
								<th class="text-center">#</th>
								<th class="text-center">Supply Name</th>
								<th class="text-center">Stock Available</th>
							</thead>
							<tbody>



							<?php
$i = 1;
$sup_arr = [];  // Initialize $sup_arr

$sql = "
    SELECT 
        s.id, s.name,
        COALESCE(SUM(CASE WHEN i.stock_type = 1 THEN i.qty ELSE 0 END), 0) AS inn,
        COALESCE(SUM(CASE WHEN i.stock_type = 2 THEN i.qty ELSE 0 END), 0) AS `out`
    FROM supply_list s
    LEFT JOIN inventory i ON s.id = i.supply_id
    GROUP BY s.id, s.name
    ORDER BY s.name ASC
";
$supply = $conn->query($sql);
while ($row = $supply->fetch_assoc()):
    $sup_arr[$row['id']] = $row['name'];  // Store the values into $sup_arr

    $available = $row['inn'] - $row['out'];
?>
    <tr>
        <td class="text-center"><?php echo $i++ ?></td>
        <td class=""><?php echo $row['name'] ?></td>
        <td class="text-right"><?php echo $available ?></td>
    </tr>
<?php endwhile; ?>








							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="col-md-7">
				<div class="card">
					<div class="card-header">
						Supply In/Out List
						<button class="btn btn-primary btn-sm float-right" id="manage-supply">Manage Supply</button>
					</div>
					<div class="card-body">
						<table class="table table-bordered">
							<thead>
								<th class="text-center">Date</th>
								<th class="text-center">Supply Name</th>
								<th class="text-center">Qty</th>
								<th class="text-center">Type</th>
								<th class="text-center"></th>
							</thead>
							<tbody>
							<?php 
								$i = 1;
								$inventory = $conn->query("SELECT * FROM inventory order by id desc");
								while($row=$inventory->fetch_assoc()):
							?>
								<tr>
									<td class="text-center"><?php echo date("Y-m-d",strtotime($row['date_created'])) ?></td>
									<td class=""><?php echo $sup_arr[$row['supply_id']] ?></td>
									<td class="text-right"><?php echo $row['qty'] ?></td>
									<?php if($row['stock_type'] == 1): ?>
										<td class="text-center"><span class="badge badge-primary"> IN </span></td>
									<?php else: ?>
										<td class="text-center"><span class="badge badge-secondary"> Used </span></td>
									<?php endif; ?>
									<td>
										<button type="button" class="btn btn-sm btn-outline-primary edit_stock" data-id="<?php echo $row['id'] ?>"><i class="fa fa-edit"></i></button>
										<button type="button" class="btn btn-sm btn-outline-danger delete_stock" data-id="<?php echo $row['id'] ?>"><i class="fa fa-trash"></i></button>
									</td>
								</tr>
							<?php endwhile; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<script>
	$('table').dataTable()
	$('#manage-supply').click(function(){
		uni_modal("Manage Supply","manage_inv.php")
	})
	$('.edit_stock').click(function(){
		uni_modal("Manage Supply","manage_inv.php?id="+$(this).attr('data-id'))
	})
	$('.delete_stock').click(function(){
		_conf("Are you sre to remove this data from list?","delete_stock",[$(this).attr('data-id')])
	})
	$('#laundry-list').dataTable()
	function delete_stock($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_inv',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Data successfully deleted",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
			}
		})
	}
</script>