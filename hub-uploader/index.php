<?PHP
	session_start();
	require_once 'vendor/autoload.php';
	$ds = DIRECTORY_SEPARATOR;

	/**
	 * Expose global env() function from oscarotero/env
	 */
	Env::init();

	/**
	 * Use Dotenv to set required environment variables and load .env file in root
	 */
	$dotenv = new Dotenv\Dotenv( __DIR__ );
	if ( file_exists( __DIR__ . $ds . '.env' ) ) :
		$dotenv->load();
		$dotenv->required([ 'PASSWORD' ]);
	endif;
	$password = env( 'PASSWORD' );

	$store = 'uploads';
	if ( !empty( $_POST['pin'] ) && $_POST['pin'] === $password ) :
		$_SESSION['pin'] = $_POST['pin'];
	endif; ?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="icon" sizes="48x48" href="https://cdn.hpm.io/assets/images/favicon/icon-48.png">
		<link rel="icon" sizes="96x96" href="https://cdn.hpm.io/assets/images/favicon/icon-96.png">
		<link rel="icon" sizes="144x144" href="https://cdn.hpm.io/assets/images/favicon/icon-144.png">
		<link rel="icon" sizes="192x192" href="https://cdn.hpm.io/assets/images/favicon/icon-192.png">
		<link rel="icon" sizes="256x256" href="https://cdn.hpm.io/assets/images/favicon/icon-256.png">
		<link rel="icon" sizes="384x384" href="https://cdn.hpm.io/assets/images/favicon/icon-384.png">
		<link rel="icon" sizes="512x512" href="https://cdn.hpm.io/assets/images/favicon/icon-512.png">
		<link rel="apple-touch-icon" sizes="57x57" href="https://cdn.hpm.io/assets/images/favicon/apple-touch-icon-57.png">
		<link rel="apple-touch-icon" sizes="60x60" href="https://cdn.hpm.io/assets/images/favicon/apple-touch-icon-60.png">
		<link rel="apple-touch-icon" sizes="72x72" href="https://cdn.hpm.io/assets/images/favicon/apple-touch-icon-72.png">
		<link rel="apple-touch-icon" sizes="76x76" href="https://cdn.hpm.io/assets/images/favicon/apple-touch-icon-76.png">
		<link rel="apple-touch-icon" sizes="114x114" href="https://cdn.hpm.io/assets/images/favicon/apple-touch-icon-114.png">
		<link rel="apple-touch-icon" sizes="120x120" href="https://cdn.hpm.io/assets/images/favicon/apple-touch-icon-120.png">
		<link rel="apple-touch-icon" sizes="152x152" href="https://cdn.hpm.io/assets/images/favicon/apple-touch-icon-152.png">
		<link rel="apple-touch-icon" sizes="167x167" href="https://cdn.hpm.io/assets/images/favicon/apple-touch-icon-167.png">
		<link rel="apple-touch-icon" sizes="180x180" href="https://cdn.hpm.io/assets/images/favicon/apple-touch-icon-180.png">
		<link rel="mask-icon" href="https://cdn.hpm.io/assets/images/favicon/safari-pinned-tab.svg" color="#ff0000">
		<meta name="msapplication-config" content="https://cdn.hpm.io/assets/images/favicon/config.xml" />
		<link rel="manifest" href="https://cdn.hpm.io/assets/images/favicon/manifest.json">
		<title>Texas Newsroom Newscast Stats</title>
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.0/css/bulma.min.css">
		<link href="https://app.hpm.io/js/dropzone/css/dropzone.css" rel="stylesheet" />
	</head>
	<body>
		<nav class="navbar is-dark" role="navigation" aria-label="main navigation">
			<div class="navbar-brand">
				<a class="navbar-item" href="https://analytics.hpm.io/hub/">
					Texas Newsroom Newscast Stats
				</a>
			</div>
			<div class="navbar-end">
				<div class="navbar-item">
					<div class="buttons">
						<a class="button is-danger" href="/hub/upload/help/">
							<strong>Need Help?</strong>
						</a>
						<a class="button is-link" href="/hub/">
							Graphing App
						</a>
					</div>
				</div>
			</div>
		</nav>
		<div class="container is-fluid">
			<section class="section">
<?PHP
	if ( !empty( $_SESSION['pin'] ) && $_SESSION['pin'] === $password ) : ?>
				<div id="inputforms">
					<div class="columns is-centered">
						<div class="column is-half">
							<h4>Upload Your Files</h4>
							<form action="upload.php" class="dropzone" id="acd"></form>
							<p><i>Accepted File Types: .xls, .xlsx, .csv</i></p>
						</div>
					</div>
				</div>
				<div class="columns">
					<div class="column">
						<div id="output"></div>
					</div>
				</div>
<?PHP
	else : ?>
				<div class="columns is-centered">
					<div class="column is-half">
						<form id="pinit" role="form" class="form-horizontal" method="post" action="">
							<div class="field">
								<label for="name" class="label">Password?</label>
								<div class="control">
									<input class="input" type="password" id="pin" placeholder="What's the secret word?" name="pin" />
								</div>
							</div>
							<div class="control">
								<button type="submit" class="button is-primary">Submit</button>
							</div>
						</form>
					</div>
				</div>
<?PHP
	endif; ?>
			</section>
		</div>
		<script src="https://app.hpm.io/js/dropzone/dropzone.min.js"></script>
		<script>
			Dropzone.options.acd = {
				paramName: "acd",
				acceptedFiles: ".xls,.xlsx,.csv",
				maxFilesize: 20,
				init: function() {
					this.on("sending", function(file, xhr, formData) {
						var csv = false;
						if (file.type === 'text/csv' || file.name.match('\.csv$') ) {
							csv = true;
						}
						if (csv) {
							var stationCall = prompt("Enter your NPR Org ID\nKERA: 77\nKUHF: 220\nKTSX: 188\nKUT: 252", "###");
							formData.append( "stationCall", stationCall );
						}
					});
					this.on("error", function(file, errorMessage) {
						alert( errorMessage );
					});
					this.on("success", function(file, responseText) {
						console.log(responseText);
						document.querySelector("#output").append(responseText.message);
					});
				}
			};
		</script>
	</body>
</html>
