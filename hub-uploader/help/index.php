<!DOCTYPE html>
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
		<title>Texas Newsroom Data Uploader Instructions</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
		<style>
			html { height: 100%; }
			body { position: relative; padding-bottom: 50px; min-height: 100%; }
			header, footer { padding: 0 15px; background: rgb(25,25,25); }
			footer { position: absolute; bottom: 0; left: 0; width: 100%; }
			header h3, footer p { color: white; margin: 0; padding: 10px 0; }
			header h3 span { font-size: 12px; font-style: italic; }
			ul { padding-bottom: 20px; }
			ul li, ol li {
				margin-bottom: 15px;
				font-size: 16px;
				font-weight: 400;
			}
			ul li img, ol li img {
				max-width: 100%;
			}
			th, td { text-align: center; }
			.jumbotron { background-color: white !important; }
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
	</head>
	<body>
		<header>
			<h3>Texas Newsroom Data Uploader Instructions</h3>
		</header>
		<div class="container-fluid">
			<div class="row jumbotron">
				<div class="col-sm-8 col-sm-offset-2">
					<h1>Texas News Hub Newcast Analytics</h1>
					<h2>Overview</h2>
					<p>This application was built to help visualize and aggregate newscast analytics for the stations in the Texas Newsroom project. It is broken into 2 parts: a PHP-based parser that can receive reports from the PPM Analysis Tool and NPR One, and a Vue.js/Chart.js-powered visualization application. Below are walkthroughs on how to download data from the respective tools.</p>
				</div>
				<div class="row">
					<div class="col-sm-5">
						<h2>PPM Reports</h2>
						<ol>
							<li>Open PPM Analysis Tool. As it is only available for Windows machines, macOS users will either have to find a separate machine or set up a virtual machine. If you go the virtual machine route, <a href="https://developer.microsoft.com/en-us/microsoft-edge/tools/vms/" rel="nofollow">Microsoft offers some basic VMs that you can download and use for free</a> for the various virtualization platforms. <a href="https://www.virtualbox.org/wiki/Downloads" rel="nofollow">VirtualBox is available for free also.</a></li>
							<li>Open the Ranker section and click "Radio." Here is a screenshot of our report setup. If you can match this without any other help, <a href="#step-3">you can skip to step 3</a>.<br><img src="https://github.com/jwcounts/hub-newscast/raw/master/screenshots/screen-01.png" alt="Selecting Radio Ranker report in PPM Analysis Tool" width="500" style="max-width:100%;"><br><img alt="Screenshot of PPM Analysis Tool Ranker report setup" src="https://github.com/jwcounts/hub-newscast/raw/master/screenshots/screen-02.png" width="500" style="max-width:100%;">
								<ol>
									<li>First, define your market. Depending on the data sets you have access to, you should only have access to one market. If it doesn't appear in the dropdown, click the <code>Market</code> button and select it.</li>
									<li>Select your survey. You can run several surveys at once, but you can only view or export results one survey at a time. If you already have surveys in the dropdown, select the survey you want to run your report on. Otherwise, click the <code>Survey</code> button. In the popup, select the "Monthly" tab, highlight the month you wish to report on, and either double-click or click the right arrow to select.<br><img src="https://github.com/jwcounts/hub-newscast/raw/master/screenshots/screen-03.png" alt="How to select monthly surveys for reporting" width="500" style="max-width:100%;"></li>
									<li>Select your Geography. I usually pull the METRO Geography (as opposed to DMA), but we should decide as a group to be consistent.</li>
									<li>Select your TimePeriod.  We will need to create a custom period, so click on the <code>TimePeriod</code> button. In the popup, click the "Custom" tab, select "Monday" for your Start Day, "Friday" for your End Day, Start Time of 7AM, End Time of 6PM, and select "Avg. All" under Day Selection. Click the <code>Select</code> button to enter it into the "Selected" pane below. You can also add it to your favorites, if you want.<br><br><strong><em>Make sure that the secondary dropdown on TimePeriod is set to "Quarter Hour." Very important.</em></strong><br><img src="https://github.com/jwcounts/hub-newscast/raw/master/screenshots/screen-04.png" alt="Creating a custom time period" width="500" style="max-width:100%;"></li>
									<li>Select your Outlet. Please only select one outlet at a time, since it will skew the Analysis Totals otherwise.</li>
									<li>Select your Estimates by clicking the <code>Estimates</code> button. The reporting is currently set up to accept "AQH Persons," "AQH RTG%", "Share%," and "AVG WK Cume." Once you have moved them into the "Selected" pane, you can order them as pictured or not. The parsing script will figure it out.<br><img src="https://github.com/jwcounts/hub-newscast/raw/master/screenshots/screen-06.png" alt="Setting up our estimates" width="500" style="max-width:100%;"></li>
									<li>Select the rest of the reporting options: Target to "P 6+", Location to "Both In/Out of Home", Listening to "Threshold Not Set".<br><img src="https://github.com/jwcounts/hub-newscast/raw/master/screenshots/screen-07.png" alt="The remaining settings" width="500" style="max-width:100%;"></li>
								</ol>
							</li>
							<li id="step-3"><strong><em>If you already have your reporting setup in place, skip to here</em></strong><br>Click the <code>Run Analysis</code> button.</li>
							<li>Click the <code>Excel</code> icon in the toolbar up top. In the popover, select "Export to a New Excel File" and click <code>Finish</code>. You can name the file whatever you want, it doesn't matter.<br><img src="https://github.com/jwcounts/hub-newscast/raw/master/screenshots/screen-08.png" alt="Click the Excel button" width="500" style="max-width:100%;"><br><img src="https://github.com/jwcounts/hub-newscast/raw/master/screenshots/screen-09.png" alt="Select export to a new excel file" width="500" style="max-width:100%;"></li>
							<li>You will also want to run a more broad "run-of-station" report, which will flow into the "Broadcast Overview" section of the graphing app. It is the same report as above, but it should be run on a <code>TimePeriod</code> of Monday-Sunday, 6AM - 12AM. After you create the TimePeriod, select "Block" from the dropdown, click <code>Run Analysis</code>, and export to an Excel file.<br><img src="https://github.com/jwcounts/hub-newscast/raw/master/screenshots/screen-12.png" alt="Run-of-Station report settings" width="500" style="max-width:100%;"></li>
							<li>In your browser of choice, go to <a href="https://analytics.hpm.io/hub/upload/" rel="nofollow">the News Hub upload page</a>, and enter the password you've been given.</li>
							<li>Drag-and-drop your file into the dropzone, or click in the dropzone to bring up a file picker. You can upload multiple files at a time, as long as the files contain one month's data for one station.<br><img src="https://github.com/jwcounts/hub-newscast/raw/master/screenshots/screen-11.png" alt="The upload screen" width="500" style="max-width:100%;"></li>
						</ol>
					</div>
					<div class="col-md-5 col-md-offset-2">
						<h2>NPR One Reports</h2>
						<ol>
							<li>Each station logs in to <a href="https://analytics.nprstations.org/">NPR Station Analytics</a> to get its own metrics.<br /><br />If you don’t have an account for <a href="https://analytics.nprstations.org/">NPR Station Analytics</a> you can register to do so. Each account has access to only one station.</li>
							<li>Go to the <code>NPR One</code> tab and change the dates to include the month you want to view. Click <code>Submit</code>.<br /><img src="./img/image001.png" alt="Go to the NPR One tab and change the dates to include the month you want to view. Click Submit."></li>
							<li>Scroll down and click <code>Station Newscast</code>.<br /><img src="./img/image002.png" alt="Scroll down and click Station Newscast"></li>
							<li>On the Station Newscasts page, click <code>Download CSV</code>.<br /><img src="./img/image003.png" alt="On the Station Newscasts page, click Download CSV"></li>
							<li>Open the zip file that downloads. Find the file that begins with <code>npr_one_station_newscast_drilldown-10_regional_newscasts&hellip;</code></li>
							<li>Go to the Texas News Hub dashboard upload page at <a href="https://analytics.hpm.io/hub/upload/">Texas Newsroom Data Uploader page</a> and upload this file.</li>
						</ol>
					</div>
				</div>
				<div class="col-sm-6 col-sm-offset-3">
					<h2 class="text-center">Questions</h2>
					<p class="text-center">Contact me at <a href="mailto:jcounts@houstonpublicmedia.org">jcounts@houstonpublicmedia.org</a>.</p>
				</div>
			</div>
		</div>
		<footer class="clearfix">
			<p class="pull-left">&copy; <?PHP echo date('Y'); ?>, Houston Public Media</p>
			<p class="pull-right"><a href="/hub/upload/" class="btn btn-primary">Data Uploader</a> <a href="/hub/" class="btn btn-success">Graphing App</a></p>
		</footer>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
	</body>
</html>
