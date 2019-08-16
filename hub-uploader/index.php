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
		<title>Texas News Hub Newscast Stats</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
		<link href="https://app.hpm.io/js/dropzone/css/dropzone.css" rel="stylesheet" />
		<style>
			html { height: 100%; }
			body { position: relative; padding-bottom: 50px; min-height: 100%; }
			header, footer { padding: 0 15px; background: rgb(25,25,25); }
			footer { position: absolute; bottom: 0; left: 0; width: 100%; }
			header h3, footer p { color: white; margin: 0; padding: 10px 0; }
			header h3 span { font-size: 12px; font-style: italic; }
			ul { padding-bottom: 20px; }
			th, td { text-align: center; }
			.jumbotron h1 span { font-size: 33px; }
			td span { font-size: 10px; }
			.bg-info, .bg-success, .bg-warning { margin-bottom: 20px; border-bottom: 4px solid #000; padding-bottom: 20px; }
			@media print {
				header, footer, #inputforms, #print, #stats { display: none; }
				h2 { font-size: 24px; }
				h3 { font-size: 18px; }
				h4 { font-size: 12px; }
				h1, .h1, h2, .h2, h3, .h3 {
					margin-top: 10px;
					margin-bottom: 5px;
				}
				.bg-info, .bg-success, .bg-warning { margin-bottom: 0; }
			}
		</style>
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
	<body>
		<header>
			<h3>Texas News Hub Newscast Stats</h3>
		</header>
		<div class="container-fluid">
<?PHP
	if ( !empty( $_SESSION['pin'] ) && $_SESSION['pin'] === $password ) : ?>
			<div id="inputforms">
				<div class="row jumbotron">
					<div class="col-sm-12">
						<h4>Upload Your Files</h4>
						<form action="upload.php" class="dropzone" id="acd"></form>
						<p><i>Accepted File Types: .xls, .xlsx, .csv</i></p>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<div id="output"></div>
				</div>
			</div>
<?PHP
	else : ?>
			<div class="row jumbotron">
					<div class="col-sm-6 col-sm-offset-3">
						<form id="pinit" role="form" class="form-horizontal" method="post" action="">
							<div class="form-group">
								<label for="name" class="col-sm-4 control-label">Password?</label>
								<div class="col-sm-8">
									<input type="password" class="form-control" id="pin" placeholder="What's the secret word?" name="pin" />
								</div>
							</div>
							<div class="form-group">
								<button type="submit" class="btn btn-primary col-sm-2 col-sm-offset-5">Submit</button>
							</div>
						</form>
					</div>
				</div>
<?PHP
	endif; ?>
		</div>
		<footer class="clearfix">
			<p class="pull-left">&copy; <?PHP echo date('Y'); ?>, Houston Public Media</p>
			<p class="pull-right"><a href="/hub/" class="btn btn-success">Graphing App</a></p>
		</footer>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
		<script src="https://app.hpm.io/js/dropzone/dropzone.min.js"></script>
		<script>
			Dropzone.options.acd = {
				paramName: "acd",
				acceptedFiles: ".xls,.xlsx,.csv",
				maxFilesize: 20,
				init: function() {
					this.on("error", function(file, errorMessage) {
						alert( errorMessage );
					});
					this.on("success", function(file, responseText) {
						$("#output").append(responseText.message);
					});
				}
			};
		</script>
	</body>
</html>
