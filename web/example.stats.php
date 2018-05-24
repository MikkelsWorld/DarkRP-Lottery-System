<?php
/*
This page contains example code for stats you can use on your website.
*/

/*
Configuration:
Change this to the path, of your settings.php file.
Its not a good idea to keep this cronjob file public, as it can be ran from any browser.
So put it outside public_html.
*/
$settings = "settings.php";
require_once($settings);

/*
EXAMPLE OF TOP 10 WINNERS.
*/

$top10 = "SELECT `name`, `payout` FROM `lottery_winners` ORDER BY payout desc LIMIT 10";

$top10 = $database->query($top10);
$top10 = $top10->fetch_all();

/*
EXAMPLE OF LATEST DRAWINGS.
*/

$last10 = "SELECT `number`, `drawn` FROM `lottery_numbers` ORDER BY drawn desc LIMIT 10";

$last10 = $database->query($last10);
$last10 = $last10->fetch_all();

/*
EXAMPLE OF LATEST DRAWINGS.
*/

$pots = "SELECT `name`, `payout`, `sysname` FROM `lottery_pot`";
$pots = $database->query($pots);
$pots = $pots->fetch_all();

/*
Below are the HTML. You can integrate it into your website if you want.
*/

?>
<!DOCTYPE html>
<html>
<head>

	<title>Example stats for DarkRP lottery</title>

	<!-- Import bootstrap css -->
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

</head>
<body>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			<div class="row">

				<!-- Last 10 winners -->
				<div class="col-md-4">
					<div class="page-header">
						<h1>
							Top Winners
						</h1>
					</div>
					<table class="table">
						<thead>
							<tr>
								<th>
									#
								</th>
								<th>
									Name
								</th>
								<th>
									Payment Taken
								</th>
							</tr>
						</thead>
						<tbody>

							<?php

							if (count($top10) > 0 ) {
								$rank = 1;



								foreach ($top10 as $winner) {
									switch ($rank) {
										case '1':
											$style = "success";
											break;

										default:
											$style = "default";
											break;
									}

									echo "<tr class=" . $style . ">";
									echo "<td>" . $rank . "</td>";
									echo "<td>" . $winner['0'] . "</td>";
									echo "<td>$" . $winner['1'] . "</td>";
									echo "</tr>";

									//Increase count
									$rank++;
								}

							} else {
								echo "<p>No winners yet.</p>";
							}
						?>
						</tbody>
					</table>
				</div>

				<!-- Last 10 drawings -->
				<div class="col-md-4">
					<div class="page-header">
						<h1>
							Last drawings
						</h1>
					</div>
					<table class="table">
						<thead>
							<tr>
								<th>
									Numbers
								</th>
								<th>
									Date
								</th>
							</tr>
						</thead>
						<tbody>
						<?php
							if (count($last10) > 0 ) {
								foreach ($last10 as $drawing) {
									echo "<tr>";
									echo "<td>" . $drawing['0'] . "</td>";
									echo "<td>" . $drawing['1'] . "</td>";
									echo "</tr>";
								}

							} else {
								echo "<p>No Drawings yet.</p>";
							}
						?>
						</tbody>
					</table>
				</div>

				<!-- Current pots -->
				<div class="col-md-4">
					<div class="page-header">
						<h1>
							Current Pots
						</h1>
					</div>
					<table class="table">
						<thead class="thead-inverse">
							<tr>
								<th>
									Name
								</th>
								<th>
									Size
								</th>
							</tr>
						</thead>
						<tbody>
						<?php
						foreach ($pots as $pot) {
								switch ($pot['2']) {
									case 'jackpot':
										$style = "success";
										break;

									case '2ndplace':
										$style = "warning";
										break;

									case '3rdplace':
										$style = "danger";
										break;
									
									default:
										$style = "default";
										break;
								}
								echo "<tr class=" . $style . ">";
								echo "<td>" . $pot['0'] . "</td>";
								echo "<td>" . $pot['1'] . "</td>";
								echo "</tr>";
							}
						?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
</body>
</html>