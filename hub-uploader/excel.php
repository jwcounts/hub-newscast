<?php
	$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load( $tempFile );
	$data = $spreadsheet->getActiveSheet()->toArray( null, true, true, true );
	$headers = $avg_aqh = $avg_aqh_rtg = $avg_share = $avg_wk_cume = [];
	foreach( $data as $d ) :
		if ( preg_match( '/([A-Z]+) ([0-9]{4}) \- Dates In\(([0-9]{8}) to ([0-9]{8})\)/', $d['B'], $match ) ) :
			$start = mktime( 0, 0, 0, substr( $match[3], 0, 2 ), substr( $match[3], 2, 2 ), substr( $match[3], 4, 4 ) );
			$end = mktime( 0, 0, 0, substr( $match[4], 0, 2 ), substr( $match[4], 2, 2 ), substr( $match[4], 4, 4 ) );
			$week = $match[1].' '.$match[2];

			$start_date = substr( $match[3], 0, 2 ) . '/' . substr( $match[3], 2, 2 ) . '/' . substr( $match[3], 4, 4 );
			$end_date = substr( $match[4], 0, 2 ) . '/' . substr( $match[4], 2, 2 ) . '/' . substr( $match[4], 4, 4 );

			$report_date = date( 'Y-m', $end );
			$filepath = $data_path . $report_date . '.json';
			if ( file_exists( $filepath ) ) :
				$temp = json_decode( file_get_contents( $filepath ), true );
			else :
				$temp = [];
			endif;

			$excel_file = $match[1].'_'.$match[2].'-Newscast_Summary.xlsx';
			$excel_file_path = $data_path . 'excel' . $ds . $excel_file;
			$reportWeeks[ $report_date ] = [
				'text' => ucwords( strtolower( $week ) ) . ' (' . $start_date . '-' . $end_date . ')',
				'download' => $excel_file
			];
		elseif ( trim( $d['A'] ) == 'Rank' ) :
			foreach ( $d as $k => $v ) :
				if ( trim( $v ) == 'AQH Persons' ) :
					$headers['aqh-persons'] = $k;
				elseif ( trim( $v ) == 'AQH Rtg%' ) :
					$headers['aqh-rtg'] = $k;
				elseif ( trim( $v ) == 'Share%' ) :
					$headers['share'] = $k;
				elseif ( trim( $v ) == 'AVG WK Cume' ) :
					$headers['avg-wk-cume'] = $k;
				elseif ( trim( $v ) == 'Time Period' ) :
					$headers['time-period'] = $k;
				elseif ( trim( $v ) == 'Outlet' ) :
					$headers['outlet'] = $k;
				endif;
			endforeach;
		elseif ( preg_match( '/^[A-Z a-z\-]{6,10}$/', trim( $d[ $headers['outlet'] ] ) ) ) :
			$station = str_replace( ' total', '-fm', strtolower( $d[ $headers['outlet'] ] ) );
			if ( count( $data ) > 30 && preg_match( '/Mo\-Fr [0-9:\-AP]+/', $d[ $headers['time-period'] ] ) ) :
				$date = explode( ' ', $d[ $headers['time-period'] ] );
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
						'aqh-persons' => str_replace( ',', '', $d[$headers['aqh-persons']] ),
						'aqh-rtg' => $d[$headers['aqh-rtg']],
						'share' => $d[$headers['share']],
						'avg-wk-cume' => str_replace( ',', '', $d[$headers['avg-wk-cume']] )
					];
				endif;
			else :
				$ros = true;
				$temp[ $station ]['ROS'] = [
					'aqh-persons' => str_replace( ',', '', $d[$headers['aqh-persons']] ),
					'aqh-rtg' => $d[$headers['aqh-rtg']],
					'share' => $d[$headers['share']],
					'avg-wk-cume' => str_replace( ',', '', $d[$headers['avg-wk-cume']] )
				];
			endif;
		endif;
	endforeach;
	if ( !$ros ) :
		foreach ( $temp[ $station ] as $tk => $tp ) :
			if ( $tk !== 'Averages' && $tk !== 'ROS' ) :
				$avg_aqh[] = $tp['during']['aqh-persons'];
				$avg_aqh_rtg[] = $tp['during']['aqh-rtg'];
				$avg_share[] = $tp['during']['share'];
				$avg_wk_cume[] = $tp['during']['avg-wk-cume'];
			endif;
		endforeach;
		$temp[ $station ][ 'Averages' ] = [
			'aqh-persons' => round( array_sum( $avg_aqh ) / count( $avg_aqh ), 0 ),
			'aqh-rtg' => round( array_sum( $avg_aqh_rtg ) / count( $avg_aqh_rtg ), 1 ),
			'share' => round( array_sum( $avg_share ) / count( $avg_share ), 1 ),
			'avg-wk-cume' => round( array_sum( $avg_wk_cume ) / count( $avg_wk_cume ), 0 )
		];
	endif;