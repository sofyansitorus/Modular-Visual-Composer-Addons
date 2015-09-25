jQuery(document).ready(function($){
	function doughnutChart(){
		if( $( '.avca-doughnut' ).length > 0 ){
			var chart = $( '.avca-doughnut' );
		
			// ---------------------
			// fetch value from html
			$( chart ).each( function() {
				$( this ).fadeIn( function() {
					var currentChart = $(this),
						currentSize = currentChart.attr( 'data-size' ),
						currentLine = currentChart.attr( 'data-line' ),
						currentBgColor = currentChart.attr( 'data-bgcolor' ),
						currentTrackColor = currentChart.attr( 'data-trackcolor' );
						currentStyle = currentChart.attr( 'data-barstyle' );
						currentChart.easyPieChart({
							animate: 1000,
							barColor: currentBgColor,
							trackColor: currentTrackColor,
							lineWidth: currentLine,
							size: currentSize,
							lineCap: currentStyle,
							scaleColor: false,
						});
				});
			});

		} // end if
	};

	doughnutChart();
});

