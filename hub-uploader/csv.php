<?php
	$digital_hours = [ '12:30','13:30','18:00','21:30','22:30' ];

	$row = 0;
	$headers = $temp = [];
	if ( ( $handle = fopen( $tempFile, "r" ) ) !== FALSE ) :
		while ( ( $data = fgetcsv( $handle, 1000, "," ) ) !== FALSE ) :
			if ( $row === 0 ) :
				foreach ( $data as $k => $v ) :
					if ( trim( $v ) == 'PublishHour' ) :
						$headers['publish'] = $k;
					elseif ( trim( $v ) == 'StationUserListens' ) :
						$headers['downloads'] = $k;
					elseif ( trim( $v ) == 'AverageCompletion' ) :
						$headers['avg-comp'] = $k;
					endif;
				endforeach;
			else :
				$temp_time = explode( ' ', $data[ $headers['publish'] ] );
				$digi_time = [
					'date' => explode( '-', $temp_time[0] ),
					'time' => explode( ':', $temp_time[1] )
				];
				$digihour = $digi_time['time'][0] . ':' . $digi_time['time'][1];
				if ( $row === 1 ) :
					$report_date = $digi_time['date'][0] . '-' . $digi_time['date'][1];
					$filepath = $data_path . $report_date . '.json';
					if ( file_exists( $filepath ) ) :
						$temp = json_decode( file_get_contents( $filepath ), true );
					else :
						$temp = [];
					endif;

					$month_txt = date( 'F', mktime( 0, 0, 0, $digi_time['date'][1], $digi_time['date'][2], $digi_time['date'][0] ) );

					$excel_file = strtoupper( $month_txt ) . '_' . $digi_time['date'][0] . '-Newscast_Summary.xlsx';
					$excel_file_path = $data_path . 'excel' . $ds . $excel_file;
					if ( empty( $reportWeeks[ $report_date ] ) ) :
						$reportWeeks[ $report_date ] = [
							'text' => $month_txt . " " . $digi_time['date'][0],
							'download' => $excel_file
						];
					endif;

					$digi = [];
					foreach ( $digital_hours as $dh ) :
						$digi[ $dh ] = 0;
					endforeach;
					$temp[ $station ]['digital'] = $digi;
				endif;

				$temp[ $station ]['digital'][ $digihour ] += $data[ $headers['downloads'] ];
			endif;
			$row++;
		endwhile;
	endif;
	$digi_avg = 0;
	foreach ( $temp[ $station ]['digital'] as $td ) :
		$digi_avg += $td;
	endforeach;
	$temp[ $station ]['digital']['Total'] = $digi_avg;
	$temp[ $station ]['digital']['Average'] = round( $digi_avg / count( $temp[ $station ]['digital'] ), 0 );