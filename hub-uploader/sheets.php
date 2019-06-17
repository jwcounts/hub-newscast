<?php
	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
	$spreadsheet = new Spreadsheet();
	$metrics = [
		[ 'slug' => 'aqh-persons', 'name' => 'AQH Persons' ],
		[ 'slug' => 'aqh-rtg', 'name' => 'AQH Rating %' ],
		[ 'slug' => 'share', 'name' => 'Share %' ],
		[ 'slug' => 'avg-wk-cume', 'name' => 'Average Weekly Cume' ],
		[ 'slug' => 'pumm', 'name' => 'PUMM' ]
	];
	$ros_avg = [
		'aqh-persons' => [],
		'aqh-rtg' => [],
		'share' => [],
		'avg-wk-cume' => [],
		'pumm' => []
	];
	$nc_avg = [];
	foreach ( $times as $tt ) :
		$nc_avg[ $tt ] = [];
		foreach ( $metrics as $mt ) :
			$nc_avg[ $tt ][ $mt['slug'] ] = [];
		endforeach;
	endforeach;

	$station_headers = [ 'Timeframe', 'AQH Persons', 'AQH Rating %', 'Share %', 'Average Weekly Cume', 'PUMM' ];
	$sheets = [
		'Broadcast Overview' => [
			[ 'Broadcast Overview (Run-of-Station)', '', '', '', '', '' ],
			[ 'Station', 'AQH Persons', 'AQH Rating %', 'Share %', 'Average Weekly Cume', 'PUMM' ]
		],
		'Newscasts Summary' => [
			[ 'Newscasts Summary', '', '', '', '', '', '', '', '' ],
			[ 'Station', 'Metric', '7:30A-7:45A', '8:30A-8:45A', '10:30A-10:45A', '1P-1:15P', '4:30P-4:45P', '5:30P-5:45P',   'Averages' ]
		]
	];
	foreach ( $temp as $kt => $kv ) :
		// Add station row to ROS sheet
		if ( !empty( $kv['ROS'] ) ) :
			$sheets['Broadcast Overview'][] = [
				strtoupper( $kt ),
				$kv['ROS']['aqh-persons'],
				$kv['ROS']['aqh-rtg'],
				$kv['ROS']['share'],
				$kv['ROS']['avg-wk-cume'],
				$kv['ROS']['pumm']
			];
			foreach( $ros_avg as $rk => $rv ) :
				$ros_avg[$rk][] = $kv['ROS'][$rk];
			endforeach;
		endif;

		// Add station rows to newscast summary sheet
		for ( $i = 0; $i < count( $metrics ); $i ++ ) :
			$met_temp = [];
			if ( $i == 0 ) :
				$met_temp[] = strtoupper( $kt );
			else :
				$met_temp[] = '';
			endif;
			$met_temp[] = $metrics[$i]['name'];
			foreach ( $times as $tt ) :
				$met_temp[] = $kv[$tt]['during'][ $metrics[$i]['slug'] ];
				$nc_avg[ $tt ][ $metrics[$i]['slug'] ][] = $kv[$tt]['during'][ $metrics[$i]['slug'] ];
			endforeach;
			$met_temp[] = $kv['Averages'][ $metrics[$i]['slug'] ];
			$sheets['Newscasts Summary'][] = $met_temp;
		endfor;

		// Add station sheet with all newscast numbers
		$sheet_name = strtoupper( $kt ) . ' Newscasts';
		$sheets[ $sheet_name ][] = [
			$sheet_name, '', '', '', '', ''
		];
		$sheets[ $sheet_name ][] = $station_headers;
		foreach ( $times as $tt ) :
			$before_temp = $during_temp = $after_temp = [];
			$ttx = explode( '-', $tt );
			$du_start = strtotime( $seed . $ttx[0] . 'M' );
			$du_end = strtotime( $seed . $ttx[1] . 'M' );
			$during = date( 'g:iA', $du_start ) . '-' . date( 'g:iA', $du_end );
			$before = date( 'g:iA', $du_start - 900 ) . '-' . date( 'g:iA', $du_start );
			$after = date( 'g:iA', $du_end ) . '-' . date( 'g:iA', $du_end + 900 );
			$before_temp[] = $before;
			$during_temp[] = $during;
			$after_temp[] = $after;
			for ( $i = 0; $i < count( $metrics ); $i ++ ) :
				$before_temp[] = $kv[$tt]['before'][ $metrics[$i]['slug'] ];
				$during_temp[] = $kv[$tt]['during'][ $metrics[$i]['slug'] ];
				$after_temp[] = $kv[$tt]['after'][ $metrics[$i]['slug'] ];
			endfor;
			$sheets[ $sheet_name ][] = $before_temp;
			$sheets[ $sheet_name ][] = $during_temp;
			$sheets[ $sheet_name ][] = $after_temp;
		endforeach;
	endforeach;

	// Adding all station averages to the ROS sheet
	if ( count( $sheets['Broadcast Overview'] ) > 2 ) :
		$ros_temp = [ 'All Stations (Average)' ];
		foreach ( $ros_avg as $ra ) :
			$ros_temp[] = round( array_sum( $ra ) / count( $ra ), 1 );
		endforeach;
		$sheets['Broadcast Overview'][] = $ros_temp;
	endif;

	// Adding all station averages to the Newscast summary sheet
	for ( $i = 0; $i < count( $metrics ); $i ++ ) :
		$nc_temp = $nc_meta = [];
		if ( $i == 0 ) :
			$nc_temp[] = 'All Stations (Averages)';
		else :
			$nc_temp[] = '';
		endif;
		$nc_temp[] = $metrics[$i]['name'];
		foreach ( $times as $tt ) :
			$nc_temp[] = round( array_sum( $nc_avg[$tt][ $metrics[$i]['slug'] ] ) / count( $nc_avg[$tt][ $metrics[$i]['slug'] ] ), 1 );
			$nc_meta[] = round( array_sum( $nc_avg[$tt][ $metrics[$i]['slug'] ] ) / count( $nc_avg[$tt][ $metrics[$i]['slug'] ] ), 1 );
		endforeach;
		$nc_temp[] = round( array_sum( $nc_meta ) / count( $nc_meta ), 1 );
		$sheets['Newscasts Summary'][] = $nc_temp;
	endfor;
	
	$rsheets = array_reverse( $sheets );
	foreach ( $rsheets as $k => $v ) :
		$myWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet( $spreadsheet, $k );
		$spreadsheet->addSheet( $myWorkSheet, 0 );
		$spreadsheet->setActiveSheetIndexByName( $k );
		$spreadsheet->getActiveSheet()->fromArray( $v, NULL, 'A1' );
	endforeach;

	// Remove the default worksheet that is created
	$sheetIndex = $spreadsheet->getIndex(
		$spreadsheet->getSheetByName('Worksheet')
	);
	$spreadsheet->removeSheetByIndex($sheetIndex);

	// Auto size columns for each worksheet
	foreach ( $spreadsheet->getWorksheetIterator() as $worksheet ) :

		$spreadsheet->setActiveSheetIndex( $spreadsheet->getIndex( $worksheet ) );

		$sheet = $spreadsheet->getActiveSheet();
		$cellIterator = $sheet->getRowIterator()->current()->getCellIterator();
		$cellIterator->setIterateOnlyExistingCells( true );
		foreach ( $cellIterator as $cell ) :
			$sheet->getColumnDimension( $cell->getColumn() )->setAutoSize( true );
		endforeach;
	endforeach;

	// Write XLSX file
	$writer =  new Xlsx( $spreadsheet );
	$writer->save( $excel_file_path );