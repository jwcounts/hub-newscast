<?php
	session_start();
	date_default_timezone_set( 'America/Chicago' );
	require_once 'vendor/autoload.php';
	if ( !defined( 'EOL' ) ) :
		define( 'EOL', ( PHP_SAPI == 'cli' ) ? PHP_EOL : '<br />' );
	endif;

	$seed = '2019/01/01 ';
	$days = $dates = $reportWeeks = $times_unix = [];
	$ros = false;
	$debug = false;
	$filetype = '';
	$times = [
		'7:30A-7:45A',
		'8:30A-8:45A',
		'10:30A-10:45A',
		'1P-1:15P',
		'4:30P-4:45P',
		'5:30P-5:45P'
	];

	$orgs = [
		220 => 'kuhf-fm',
		77 => 'kera-fm',
		188 => 'kstx-fm',
		252 => 'kut-fm'
	];
	$hours = [
		8 => [
			'sector' => 40,
			'airtime' => '7:31 AM'
		],
		9 => [
			'sector' => 40,
			'airtime' => '8:31 AM'
		],
		11 => [
			'sector' => 40,
			'airtime' => '10:30 AM'
		],
		14 => [
			'sector' => 10,
			'airtime' => '1:04 PM'
		],
		17 => [
			'sector' => 40,
			'airtime' => '4:32 PM'
		],
		18 => [
			'sector' => 40,
			'airtime' => '5:32 PM'
		]
	];

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
		$dotenv->required([ 'PASSWORD', 'DATA_PATH' ]);
	endif;
	$password = env( 'PASSWORD' );
	$data_path = env( 'DATA_PATH' );

	$reportsFile = $data_path . 'reports.json';

	foreach ( $times as $tr ) :
		$exp = explode( '-', $tr );
		if ( !preg_match( '/:/', $exp[0] ) ) :
			$exp[0] = str_replace( [ 'A', 'P' ], [ ':00A', ':00P' ], $exp[0] );
		endif;
		$times_unix[] = [
			'text' => $tr,
			'unix' => strtotime( $seed . $exp[0] . 'M' )
		];
	endforeach;

	$reports = json_decode( file_get_contents( $reportsFile ), true );
	if ( !empty( $reports ) ) :
		foreach ( $reports as $rep ) :
			$reportWeeks[ $rep['value'] ] = [ 'text' => $rep['text'], 'download' => $rep['download'] ] ;
		endforeach;
	endif;

	if ( empty( $_SESSION['pin'] ) || $_SESSION['pin'] !== $password ) :
		header("HTTP/1.1 403 Forbidden");
		echo '<p>You are not authorized to upload files. Please refresh and reenter your password.</p>';
		die;
	endif;
	if ( !empty( $_FILES ) ) :
		if ( !empty( $_FILES['acd'] ) ) :
			$filename = $_FILES['acd']['name'];
			$tempFile = $_FILES['acd']['tmp_name'];
		endif;

		if (
			$_FILES['acd']['type'] === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' ||
			$_FILES['acd']['type'] === 'application/octet-stream'
		) :
			$filetype = 'excel';
		elseif ( $_FILES['acd']['type'] === 'text/csv' ) :
			$filetype = 'csv';
		elseif ( $_FILES['acd']['type'] == 'application/vnd.ms-excel' ) :
			if ( preg_match( '/\.csv$/i', $filename ) ) :
				$filetype = 'csv';
			else :
				$filetype = 'excel';
			endif;
		endif;
		if ( $filetype === 'excel' ) :
			require __DIR__ . $ds . 'excel.php';
		elseif ( $filetype === 'csv' ) :
			if ( !empty( $_POST && !empty( $_POST['stationCall'] ) ) ) :
				$org_id = (int)$_POST['stationCall'];
				if ( !empty( $orgs[ $org_id ] ) ) :
					$station = $orgs[ $org_id ];
					require __DIR__ . $ds . 'csv.php';
				else :
					header("HTTP/1.1 403 Forbidden");
					echo 'Invalid NPR Org ID. Please upload your file again and enter a valid org ID when prompted.';
					die;
				endif;
			else :
				header("HTTP/1.1 403 Forbidden");
				echo 'No NPR Org ID entered. Please upload your file again and enter your Org ID when prompted.';
				die;
			endif;
		else :
			header("HTTP/1.1 403 Forbidden");
			echo '<p>Unsupported file type. Please try again with a supported type.</p>';
			die;
		endif;

		require __DIR__ . $ds . 'sheets.php';

		file_put_contents( $filepath, json_encode( $temp ) );
		$reports = [];
		krsort( $reportWeeks );
		foreach ( $reportWeeks as $rk => $rv ) :
			$reports[] = [
				'text' => $rv['text'],
				'value' => $rk,
				'download' => $rv['download']
			];
		endforeach;
		file_put_contents( $reportsFile, json_encode( $reports ) );

		$result = [
			'message' => '<p>Data has been updated. <a href="/hub/">Check it out here</a>.</p>'
		];
		header('Content-type: application/json');
		echo json_encode( $result );
	endif;
