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
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.0/css/bulma.min.css">
		<style>
			ol, ul {
				list-style-position: outside;
				margin: 1em;
				padding: 1em;
			}
			ol li, ul li {
				margin-bottom: 1em;
			}
			.column {
				padding: 1.5em;
			}
			ol ol {
				list-style-type: upper-alpha;
			}
		</style>
	</head>
	<body>
		<nav class="navbar is-dark" role="navigation" aria-label="main navigation">
			<div class="navbar-brand">
				<a class="navbar-item" href="https://analytics.hpm.io/hub/">
					Texas Newsroom Data Uploader Help
				</a>
			</div>
			<div class="navbar-end">
				<div class="navbar-item">
					<div class="buttons">
						<a class="button is-primary" href="/hub/upload/">
							Data Uploader
						</a>
						<a class="button is-link" href="/hub/">
							Graphing App
						</a>
					</div>
				</div>
			</div>
		</nav>
		<div class="container is-fluid">
			<div class="hero">
				<div class="columns is-centered hero-body">
					<div class="column is-two-thirds">
						<h1 class="is-size-1">Texas News Hub Newcast Analytics</h1>
						<h2 class="is-size-2">Overview</h2>
						<p>This application was built to help visualize and aggregate newscast analytics for the stations in the Texas Newsroom project. It is broken into 2 parts: a PHP-based parser that can receive reports from the PPM Analysis Tool and NPR One, and a Vue.js/Chart.js-powered visualization application. Below are walkthroughs on how to download data from the respective tools.</p>
					</div>
				</div>
			</div>
			<section class="section">
				<div class="columns is-centered is-multiline">
					<div class="column is-6">
						<h2 class="is-size-2">PPM Reports</h2>
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
					<div class="column is-6">
						<h2 class="is-size-2">NPR One Reports</h2>
						<ol>
							<li>Each station logs in to <a href="https://analytics.nprstations.org/">NPR Station Analytics</a> to get its own metrics.<br /><br />If you don’t have an account for <a href="https://analytics.nprstations.org/">NPR Station Analytics</a> you can register to do so. Each account has access to only one station.</li>
							<li>Go to the <code>NPR One</code> tab and change the dates to include the month you want to view. Click <code>Submit</code>.<br /><img src="./img/image001.png" alt="Go to the NPR One tab and change the dates to include the month you want to view. Click Submit."></li>
							<li>Scroll down and click <code>Station Newscast</code>.<br /><img src="./img/image002.png" alt="Scroll down and click Station Newscast"></li>
							<li>On the Station Newscasts page, click <code>Download CSV</code>.<br /><img src="./img/image003.png" alt="On the Station Newscasts page, click Download CSV"></li>
							<li>Open the zip file that downloads. Find the file that begins with <code>npr_one_station_newscast_drilldown-10_regional_newscasts&hellip;</code></li>
							<li>Go to the Texas News Hub dashboard upload page at <a href="https://analytics.hpm.io/hub/upload/">Texas Newsroom Data Uploader page</a> and upload this file.</li>
						</ol>
					</div>
					<div class="column is-4">
						<h2 class="is-size-2">Questions</h2>
						<p>Contact me at <a href="mailto:jcounts@houstonpublicmedia.org">jcounts@houstonpublicmedia.org</a>.</p>
					</div>
				</div>
			</section>
		</div>
	</body>
</html>
