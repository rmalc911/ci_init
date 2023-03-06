<div class="card">
	<div class="card-header">
		<div class="card-head-row">
			<h4 class="card-title">Delete Data</h4>
		</div>
	</div>
	<div class="card-body">
		<table class="table table-bordered table-striped">
			<thead>
				<tr>
					<th>Master</th>
					<th>Auto Delete Mapped</th>
					<th>Delete</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>Distributors</td>
					<td></td>
					<td><a href="<?= ad_base_url('delete/distributors') ?>" class="btn btn-danger btn-sm" onclick="return confirm('Confirm delete all Distributor data');">Delete</a></td>
				</tr>
				<tr>
					<td>Brands</td>
					<td>Products</td>
					<td><a href="<?= ad_base_url('delete/brands') ?>" class="btn btn-danger btn-sm" onclick="return confirm('Confirm delete all Brand data');">Delete</a></td>
				</tr>
				<tr>
					<td>Categories</td>
					<td>Products</td>
					<td><a href="<?= ad_base_url('delete/category') ?>" class="btn btn-danger btn-sm" onclick="return confirm('Confirm delete all Category data');">Delete</a></td>
				</tr>
				<tr>
					<td>UOMs</td>
					<td>Sizes</td>
					<td><a href="<?= ad_base_url('delete/uom') ?>" class="btn btn-danger btn-sm" onclick="return confirm('Confirm delete all UOM data');">Delete</a></td>
				</tr>
				<tr>
					<td>Sizes</td>
					<td>Products</td>
					<td><a href="<?= ad_base_url('delete/size') ?>" class="btn btn-danger btn-sm" onclick="return confirm('Confirm delete all Size data');">Delete</a></td>
				</tr>
				<tr>
					<td>Packaging Types</td>
					<td>Products</td>
					<td><a href="<?= ad_base_url('delete/packaging') ?>" class="btn btn-danger btn-sm" onclick="return confirm('Confirm delete all Packaging Types data');">Delete</a></td>
				</tr>
				<tr>
					<td>Products</td>
					<td>Rate Card</td>
					<td><a href="<?= ad_base_url('delete/products') ?>" class="btn btn-danger btn-sm" onclick="return confirm('Confirm delete all Products data');">Delete</a></td>
				</tr>
				<tr>
					<td>Rate Card</td>
					<td></td>
					<td><a href="<?= ad_base_url('delete/rate_card') ?>" class="btn btn-danger btn-sm" onclick="return confirm('Confirm delete all Rate Card data');">Delete</a></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>