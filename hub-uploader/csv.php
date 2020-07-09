<?php
	$digital_hours = [ '12:30','13:30','14:30','18:00','19:00','21:30','22:30','23:30' ];

	$row = 0;
	$headers = $temp = $datatemp = [];
	if ( ( $handle = fopen( $tempFile, "r" ) ) !== FALSE ) :
		while ( ( $data = fgetcsv( $handle, 1000, "," ) ) !== FALSE ) :
			$datatemp[] = $data;
		endwhile;
	endif;
	fclose( $handle );

	$headtemp = array_shift( $datatemp );
	foreach ( $headtemp as $k => $v ) :
		if ( trim( $v ) == 'PublishHour' ) :
			$headers['publish'] = $k;
		elseif ( trim( $v ) == 'StationUserListens' ) :
			$headers['downloads'] = $k;
		elseif ( trim( $v ) == 'AverageCompletion' ) :
			$headers['avg-comp'] = $k;
		endif;
	endforeach;

	$num = round( ( ( count( $datatemp ) - 1 ) / 2 ), PHP_ROUND_HALF_UP );
	preg_match( '/^([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})$/', $datatemp[ $num ][ $headers['publish'] ], $digi_time );

	$report_date = $digi_time[1] . '-' . $digi_time[2];
	$filepath = $data_path . $report_date . '.json';
	if ( file_exists( $filepath ) ) :
		$temp = json_decode( file_get_contents( $filepath ), true );
	else :
		$temp = [];
	endif;

	$month_txt = date( 'F', mktime( 0, 0, 0, $digi_time[2], $digi_time[3], $digi_time[1] ) );

	$excel_file = strtoupper( $month_txt ) . '_' . $digi_time[1] . '-Newscast_Summary.xlsx';
	$excel_file_path = $data_path . 'excel' . $ds . $excel_file;
	if ( empty( $reportWeeks[ $report_date ] ) ) :
		$reportWeeks[ $report_date ] = [
			'text' => $month_txt . " " . $digi_time[1],
			'download' => $excel_file
		];
	endif;

	$digi = [];
	foreach ( $digital_hours as $dh ) :
		$digi[ $dh ] = 0;
	endforeach;
	$temp[ $station ]['digital'] = $digi;

	foreach ( $datatemp as $dt ) :
		preg_match( '/^([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})$/', $dt[ $headers['publish'] ], $digi_time );
		$digihour = $digi_time[4] . ':' . $digi_time[5];
		$temp[ $station ]['digital'][ $digihour ] += $dt[ $headers['downloads'] ];
	endforeach;
	$digi_avg = 0;
	foreach ( $temp[ $station ]['digital'] as $td ) :
		$digi_avg += $td;
	endforeach;
	$temp[ $station ]['digital']['Total'] = $digi_avg;
	$temp[ $station ]['digital']['Average'] = round( $digi_avg / count( $digital_hours ), 0 );