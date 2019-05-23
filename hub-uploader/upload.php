<?php
	session_start();
	date_default_timezone_set( 'America/Chicago' );
	require_once 'vendor/autoload.php';
	if ( !defined( 'EOL' ) ) :
		define( 'EOL', ( PHP_SAPI == 'cli' ) ? PHP_EOL : '<br />' );
	endif;

	$seed = '2019/01/01 ';
	$days = $dates = $reportWeeks = $times_unix = [];
	$times = [
		'7:30A-7:45A',
		'8:30A-8:45A',
		'10:30A-10:45A',
		'1P-1:15P',
		'4:30P-4:45P',
		'5:30P-5:45P'
	];

	$password = 'altitude ferment dustpan';
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
	foreach ( $reports as $rep ) :
		$reportWeeks[ $rep['value'] ] = $rep['text'];
	endforeach;

	


	if ( !empty( $_SESSION['pin'] ) && $_SESSION['pin'] === $password ) :
		if ( !empty( $_FILES ) ) :
			if ( !empty( $_FILES['acd'] ) ) :
				$filename = $_FILES['acd']['name'];
				$tempFile = $_FILES['acd']['tmp_name'];
			endif;

			$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load( $tempFile );
			$data = $spreadsheet->getActiveSheet()->toArray( null, true, true, true );
			foreach( $data as $d ) :
				if ( preg_match( '/([A-Z]+) ([0-9]{4}) \- Dates In\(([0-9]{8}) to ([0-9]{8})\)/', $d['B'], $match ) ) :
					$start = mktime( 0, 0, 0, substr( $match[3], 0, 2 ), substr( $match[3], 2, 2 ), substr( $match[3], 4, 4 ) );
					$week = $match[1].' '.$match[2];

					$report_date = date( 'Y-m-d', $start );
					$filepath = $data_path . $report_date . '.json';
					if ( file_exists( $filepath ) ) :
						$temp = json_decode( file_get_contents( $filepath ), true );
					else :
						$temp = [];
					endif;

					if ( empty( $reportWeeks[ $report_date ] ) ) :
						$reportWeeks[ $report_date ] = ucwords( strtolower( $week ) );
					endif;
				elseif ( preg_match( '/^[A-Z\-]{5,7}$/', $d['B'] ) ) :
					$station = $d['B'];
					
					$date = explode( ' ', $d['E'] );
					$dp_start = explode( '-', $date[1] );
					if ( !preg_match( '/:/', $dp_start[0] ) ) :
						$dp_start[0] = str_replace( [ 'A', 'P' ], [ ':00A', ':00P' ], $dp_start[0] );
					endif;
					$row_unix = strtotime( $seed . $dp_start[0] . 'M' );
					$dayp = false;
					$qh = $qh_rel = '';

					foreach ( $times_unix as $tu ) :
						if ( $row_unix == $tu['unix'] ) :
							$qh = $tu['text'];
							$qh_rel = 'during';
							$dayp = true;
						elseif ( ( $row_unix - 900 ) == $tu['unix'] ) :
							$qh = $tu['text'];
							$qh_rel = 'after';
							$dayp = true;
						elseif ( ( $row_unix + 900 ) == $tu['unix'] ) :
							$qh = $tu['text'];
							$qh_rel = 'before';
							$dayp = true;
						endif;
					endforeach;

					if ( $dayp ) :
						if ( empty( $temp[ $station ] ) ) :
							$temp[ $station ] = [];
							foreach ( $times as $t ) :
								$temp[ $station ][ $t ] = [
									'before' => [],
									'during' => [],
									'after' => []
								];
							endforeach;
						endif;
						$temp[ $station ][ $qh ][ $qh_rel ] = [
							'aqh-persons' => intval( str_replace( ',', '', $d['F'] ) ),
							'share' => $d['G'],
							'avg-wk-cume' => intval( str_replace( ',', '', $d['H'] ) ),
							'pumm' => intval( str_replace( ',', '', $d['I'] ) )
						];
					endif;
				elseif ( $d['B'] == 'Analysis Total' ) :
					$temp[ $station ][ 'Totals' ] = [
						'aqh-persons' => intval( str_replace( ',', '', $d['F'] ) ),
						'share' => $d['G'],
						'avg-wk-cume' => intval( str_replace( ',', '', $d['H'] ) ),
						'pumm' => intval( str_replace( ',', '', $d['I'] ) )
					];
				endif;
			endforeach;
			file_put_contents( $filepath, json_encode( $temp ) );
			$reports = [];
			krsort( $reportWeeks );
			foreach ( $reportWeeks as $rk => $rv ) :
				$reports[] = [
					'text' => $rv,
					'value' => $rk
				];
			endforeach;
			file_put_contents( $reportsFile, json_encode( $reports ) );

			$result = [
				'message' => '<p>Data has been updated. <a href="/hub/">Check it out here</a>.</p>'
			];
			header('Content-type: application/json');
			echo json_encode( $result );
		endif;
	else :
		header("HTTP/1.1 403 Forbidden");
		echo '<p>You are not authorized to upload files. Please refresh and reenter your password.</p>';
		die;
	endif;