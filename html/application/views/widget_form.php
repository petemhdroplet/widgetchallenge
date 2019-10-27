<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<title>Wally's Widgets</title>
</head>
<body>

<div class="container">
	<div class="row">
		<div class="col-sm">
			<h1>Widget Package Shipment Calculator</h1>
		</div>
	</div>

	<div class="row">
		<div class="col-sm">
			<p>Enter the number of widgets ordered to receive the number of each pack type required in the shipment.</p>
		</div>
	</div>
	
	<form method="POST">
		<div class="form-row">
			<div class="col">
				<input type="text" class="form-control" name="widgetCount">
			</div>
			<div class="col">
				<button type="submit" class="btn btn-primary">Calculate</button>
			</div>
		</div>
	</form>
	
	<?php if (isset($widgetCount) && isset($shipment)): ?>
	<div class="row">
		<div class="col-sm">
			<div class="alert alert-success" role="alert">
				<?php echo "Shipment calculated for $widgetCount widgets:"; ?>
			</div>
		</div>
	</div>
		<?php foreach ($shipment as $pack): ?>
			<div class="row">
			<div class="col-sm">
				<div class="alert alert-primary" role="alert">
					<?php echo $pack["shipped"] . " boxes of " . $pack["quantity"] . " widgets"; ?>
				</div>
			</div>
		</div>
		<?php endforeach; ?>
	<?php endif; ?>
	
</div>

</body>
</html>