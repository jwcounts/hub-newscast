<?php
	$row = 0;

	if ( ( $handle = fopen( $tempFile, "r" ) ) !== FALSE ) :
		while ( ( $data = fgetcsv( $handle, 1000, "," ) ) !== FALSE ) :
			if ( $row !== 0 ) :
				preg_match( '/cpa\.ds\.npr\.org\/newscasts\/([0-9]{2,3})\/([0-9]{4})\/([0-9]{2})\/[0-9]{2,3}\-newscast\-[0-9]{4}\-[0-9]{2}\-[0-9]{2}T([0-9]{1,2})\-([0-9]{2})\-[0-9]{2}\.mp3/', $data[0], $match );
				if ( !empty( $match ) ) :
					if ( $row == 1 ) :
						$report_date = $match[2] . '-' . $match[3];
						$filepath = $data_path . $report_date . '.json';
						if ( file_exists( $filepath ) ) :
							$temp = json_decode( file_get_contents( $filepath ), true );
						else :
							$temp = [];
						endif;

						$month_txt = date( 'F', mktime( 0, 0, 0, $match[3], 1, $match[2] ) );

						$excel_file = strtoupper( $month_txt ) . '_' . $match[2] . '-Newscast_Summary.xlsx';
						$excel_file_path = $data_path . 'excel' . $ds . $excel_file;
						if ( empty( $reportWeeks[ $report_date ] ) ) :
							$reportWeeks[ $report_date ] = [
								'text' => $month_txt . " " . $match[2],
								'download' => $excel_file
							];
						endif;

						$station = $orgs[ $match[1] ];
						$digi = [];
						foreach ( $hours as $h ) :
							$digi[ $h['airtime'] ] = 0;
						endforeach;
						$temp[ $station ]['digital'] = $digi;
					endif;
					if ( !empty( $hours[ $match[4] ] ) ) :
						if ( $match[5][0] == substr( $hours[ $match[4] ]['sector'], 0, 1 ) ) :
							$temp[ $station ]['digital'][ $hours[ $match[4] ]['airtime'] ] += $data[2];
						endif;
					endif;
				endif;
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