<?php include 'server/server.php' ?>
<?php 

	$query = "SELECT * FROM tblresident WHERE resident_type=1 AND is_deleted = 0";
    $result = $conn->query($query);
	$total = $result->num_rows;

	$query1 = "SELECT * FROM tblresident WHERE gender='Male' AND resident_type=1 AND is_deleted = 0";
    $result1 = $conn->query($query1);
	$male = $result1->num_rows;

	$query2 = "SELECT * FROM tblresident WHERE gender='Female' AND resident_type=1 AND is_deleted = 0";
    $result2 = $conn->query($query2);
	$female = $result2->num_rows;

	$query3 = "SELECT * FROM tblresident WHERE voterstatus='Yes' AND resident_type=1 AND is_deleted = 0";
    $result3 = $conn->query($query3);
	$totalvoters = $result3->num_rows;

	$query4 = "SELECT * FROM tblresident WHERE voterstatus='No' AND resident_type=1 AND is_deleted = 0";
	$non = $conn->query($query4)->num_rows;

	$query5 = "SELECT * FROM tblpurok";
	$purok = $conn->query($query5)->num_rows;

	$query6 = "SELECT * FROM tblprecinct";
	$precinct = $conn->query($query6)->num_rows;

	$date = date('Y-m-d'); 
	$query8 = "SELECT SUM(amounts) as am FROM tblpayments WHERE `date`='$date'";
	$revenue = $conn->query($query8)->fetch_assoc();

	$query2 = "SELECT * FROM tblresident WHERE demographic_group='Senior Citizen' AND resident_type=1";
    $result2 = $conn->query($query2);
	$SeniorCitizen = $result2->num_rows;

	$query2 = "SELECT * FROM tblresident WHERE demographic_group='PWDs' AND resident_type=1";
    $result2 = $conn->query($query2);
	$PWDs = $result2->num_rows;

	$query2 = "SELECT * FROM tblresident WHERE demographic_group='ERPAT(BELA)' AND resident_type=1";
    $result2 = $conn->query($query2);
	$ERPAT = $result2->num_rows;

	$query2 = "SELECT * FROM tblresident WHERE demographic_group='WOMENS FEDERATION' AND resident_type=1";
    $result2 = $conn->query($query2);
	$WOMENS = $result2->num_rows;


	// Query to count the total number of generated tickets
    $countTicketsQuery = "SELECT COUNT(*) as total_tickets FROM tblticket_logs";
    $countResult = $conn->query($countTicketsQuery);
    // Initialize the totalTickets variable
    $totalTickets = 0; 
    
    // Fetch the result if available
if ($countResult && $countResult->num_rows > 0) {
    $row = $countResult->fetch_assoc();
    $totalTickets = $row['total_tickets'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<?php include 'templates/header.php' ?>
	<title>Dashboard - BBQS BURU-UN</title>
</head>
<body>
	<?php include 'templates/loading_screen.php' ?>

	<div class="wrapper">
		<!-- Main Header -->
		<?php include 'templates/main-header.php' ?>
		<!-- End Main Header -->

		<!-- Sidebar -->
		<?php include 'templates/sidebar.php' ?>
		<!-- End Sidebar -->

		<div class="main-panel">
			<div class="content">
				<div class="panel-header bg-primary-gradient">
					<div class="page-inner">
						<div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
							<div>
								<h2 class="text-white fw-bold">Dashboard</h2>
							</div>
						</div>
					</div>
				</div>
				<div class="page-inner mt--2">
					<?php if (isset($_SESSION['message'])): ?>
						<div class="alert alert-<?= $_SESSION['success']; ?> <?= $_SESSION['success'] == 'danger' ? 'bg-danger text-light' : null ?>" role="alert">
							<?php echo $_SESSION['message']; ?>
						</div>
						<?php unset($_SESSION['message']); ?>
					<?php endif ?>
					<div class="row">
						<div class="col-md-4">
							<div class="card card-stats card-primary card-round">
								<div class="card-body">
									<div class="row">
										<div class="col-3">
											<div class="icon-big text-center">
												<i class="flaticon-users"></i>
											</div>
										</div>
										<div class="col-6 col-stats">
											<div class="numbers mt-4">
												<h2 class="fw-bold text-uppercase">Population</h2>
												<h3 class="fw-bold text-uppercase"><?= number_format($total) ?></h3>
											</div>
										</div>
									</div>
								</div>
								<div class="card-body">
									<a href="resident_info.php?state=all" class="card-link text-light">Total Population</a>
								</div>
							</div>
						</div>
						
						<div class="col-md-4">
							<div class="card card-stats card-secondary card-round">
								<div class="card-body">
									<div class="row">
										<div class="col-3">
											<div class="icon-big text-center">
												<i class="flaticon-user"></i>
											</div>
										</div>
										<div class="col-6 col-stats">
											<div class="numbers mt-4">
												<h2 class="fw-bold text-uppercase">Male</h2>
												<h3 class="fw-bold"><?= number_format($male) ?></h3>
											</div>
										</div>
									</div>
								</div>
								<div class="card-body">
									<a href="resident_info.php?state=male" class="card-link text-light">Total Male</a>
								</div>
							</div>
						</div>
						
						<div class="col-md-4">
							<div class="card card-stats card-warning card-round">
								<div class="card-body">
									<div class="row">
										<div class="col-3">
											<div class="icon-big text-center">
												<i class="icon-user-female"></i>
											</div>
										</div>
										<div class="col-6 col-stats">
											<div class="numbers mt-4">
												<h2 class="fw-bold text-uppercase">Female</h2>
												<h3 class="fw-bold text-uppercase"><?= number_format($female) ?></h3>
											</div>
										</div>
									</div>
								</div>
								<div class="card-body">
									<a href="resident_info.php?state=female" class="card-link text-light">Total Female</a>
								</div>
							</div>
						</div>
						
						<!-- Senior Citizens -->
						<div class="col-md-4">
							<div class="card card-stats card-round" style="background-color:#D2B48C; color:#fff">
								<div class="card-body">
									<div class="row">
										<div class="col-3">
											<div class="icon-big text-center">
												<i class="fas fa-blind"></i>
											</div>
										</div>
										<div class="col-6 col-stats">
											<div class="numbers mt-4">
												<h2 class="fw-bold text-uppercase">Senior Citizens</h2>
												<h3 class="fw-bold text-uppercase"><?= number_format($SeniorCitizen) ?></h3>
											</div>
										</div>
									</div>
								</div>
								<div class="card-body">
									<a href="resident_info.php?state=Senior Citizen" class="card-link text-light">Total Senior Citizens</a>
								</div>
							</div>
						</div>

						<!-- PWD's -->
						<div class="col-md-4">
							<div class="card card-stats card-round" style="background-color:#191970; color:#fff">
								<div class="card-body">
									<div class="row">
										<div class="col-3">
											<div class="icon-big text-center">
											<i class="fas fa-wheelchair"></i>
											</div>
										</div>
										<div class="col-6 col-stats">
											<div class="numbers mt-4">
												<h2 class="fw-bold text-uppercase">Persons with Disabilities</h2>
												<h3 class="fw-bold text-uppercase"><?= number_format($PWDs) ?></h3>
											</div>
										</div>
									</div>
								</div>
								<div class="card-body">
									<a href="resident_info.php?state=PWDs" class="card-link text-light">Total PWD's</a>
								</div>
							</div>
						</div>
					    
                      	<!-- Puroks -->
						<div class="col-md-4">
							<div class="card card-stats card-round" style="background-color:#2F4F4F; color:#fff">
								<div class="card-body">
									<div class="row">
										<div class="col-3">
											<div class="icon-big text-center">
											<i class="fas fa-handshake"></i>
											</div>
										</div>
										<div class="col-6 col-stats">
											<div class="numbers mt-4">
												<h2 class="fw-bold text-uppercase">ERPAT</h2>
												<h2 class="fw-bold text-uppercase">(BELA)</h2>
												<h3 class="fw-bold text-uppercase"><?= number_format($ERPAT) ?></h3>
											</div>
										</div>
									</div>
								</div>
								<div class="card-body">
									<a href="resident_info.php?state=ERPAT(BELA)" class="card-link text-light">Total Erpats</a>
								</div>
							</div>
						</div>
					</div>
					<?php if(isset($_SESSION['username']) && $_SESSION['role']=='administrator'):?>
					<div class="row">
						<div class="col-md-4">
							<div class="card card-stats card-success card-round">
								<div class="card-body">
									<div class="row">
										<div class="col-3">
											<div class="icon-big text-center">
												<i class="fas fa-fingerprint"></i>
											</div>
										</div>
										<div class="col-3 col-stats">
										</div>
										<div class="col-6 col-stats">
											<div class="numbers mt-4">
												<h2 class="fw-bold text-uppercase">Voters</h2>
												<h3 class="fw-bold text-uppercase"><?= number_format($totalvoters) ?></h3>
											</div>
										</div>
									</div>
								</div>
								<div class="card-body">
									<a href="resident_info.php?state=voters" class="card-link text-light">Total Voters </a>
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="card card-stats card-info card-round">
								<div class="card-body">
									<div class="row">
										<div class="col-3">
											<div class="icon-big text-center">
												<i class="flaticon-users"></i>
											</div>
										</div>
										<div class="col-3 col-stats">
										</div>
										<div class="col-6 col-stats">
											<div class="numbers mt-4">
												<h2 class="fw-bold text-uppercase">Non Voters</h2>
												<h3 class="fw-bold text-uppercase"><?= number_format($non) ?></h3>
											</div>
										</div>
									</div>
								</div>
								<div class="card-body">
									<a href="resident_info.php?state=non_voters" class="card-link text-light">Total Non Voters </a>
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="card card-stats card-round" style="background-color:#a349a3; color:#fff">
								<div class="card-body">
									<div class="row">
										<div class="col-3">
											<div class="icon-big text-center">
												<i class="fas fa-list"></i>
											</div>
										</div>
										<div class="col-3 col-stats">
										</div>
										<div class="col-6 col-stats">
											<div class="numbers mt-4">
												<h2 class="fw-bold text-uppercase">Precinct #</h2>
												<h3 class="fw-bold"><?= number_format($precinct) ?></h3>
											</div>
										</div>
									</div>
								</div>
								<div class="card-body">
									<a href="purok_info.php?state=precinct" class="card-link text-light">Precint Information</a>
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="card card-stats card-round" style="background-color:#880a14; color:#fff">
								<div class="card-body">
									<div class="row">
										<div class="col-3">
											<div class="icon-big text-center">
												<i class="icon-direction"></i>
											</div>
										</div>
										<div class="col-3 col-stats">
										</div>
										<div class="col-6 col-stats">
											<div class="numbers mt-4">
												<h2 class="fw-bold text-uppercase">Purok Number</h2>
												<h3 class="fw-bold text-uppercase"><?= number_format($purok) ?></h3>
											</div>
										</div>
									</div>
								</div>
								<div class="card-body">
									<a href="purok_info.php?state=purok" class="card-link text-light">Purok Information</a>
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="card card-stats card-round card-danger">
								<div class="card-body">
									<div class="row">
										<div class="col-3">
											<div class="icon-big text-center">
											<i class="fas fa-ticket-alt"></i>
											</div>
										</div>
										<div class="col-3 col-stats">
										</div>
										<div class="col-6 col-stats">
											<div class="numbers mt-4">
												<h2 class="fw-bold text-uppercase">Total Tickets reports</h2>
												<h3 class="fw-bold text-uppercase"><?= number_format($totalTickets) ?></h3>
                                            </div>
                                        </div>
									</div>
								</div>
								<div class="card-body">
								<a href="new_report.php" class="card-link text-light">Queue Report Information</a>
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="card card-stats card-round" style="background-color:#3E9C35; color:#fff">
								<div class="card-body">
									<div class="row">
										<div class="col-3">
											<div class="icon-big text-center">
												<i class="fas fa-dollar-sign"></i>
											</div>
										</div>
										<div class="col-3 col-stats">
										</div>
										<div class="col-6 col-stats">
											<div class="numbers mt-4">
												<h2 class="fw-bold text-uppercase">Revenues - BY DAY</h2>
                                                <h3 class="fw-bold text-uppercase">P <?= number_format($revenue['am'] ?? 0, 2) ?></h3>
											</div>
										</div>
									</div>
								</div>
								<div class="card-body">
									<a href="revenue.php" class="card-link text-light">All Revenues</a>
								</div>
							</div>
						</div>
					</div>
					<?php endif ?>
					<div class="row">
						<div class="col-md-12">
							<div class="card">
								<div class="card-header">
									<div class="card-head-row">
										<div class="card-title fw-bold">LGU Mission Statement</div>
									</div>
								</div>
								<div class="card-body">
									<p><?= !empty($db_txt) ? $db_txt : 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque in ipsum id orci porta dapibus. Donec rutrum congue leo eget malesuada. Curabitur arcu erat, accumsan id imperdiet et, porttitor at sem. Quisque velit nisi, pretium ut lacinia in, elementum id enim.' ?></p>
									<div class="text-center">
										<img class="img-fluid" src="<?= !empty($db_img) ? 'assets/uploads/'.$db_img : 'assets/img/bg-abstract.png' ?>" />
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- Main Footer -->
			<?php include 'templates/main-footer.php' ?>
			<!-- End Main Footer -->
			
		</div>
		
	</div>
	<?php include 'templates/footer.php' ?>
</body>
</html>