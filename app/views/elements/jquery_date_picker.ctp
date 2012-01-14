<?php
//TO use this element, your layout must include jquery UI
//ex: <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.6/jquery-ui.min.js"></script>
?>
	<script>
	$(function() {
		var dates = $( "#<?php echo $startId; ?>, #<?php echo $endId; ?>" ).datepicker({
			defaultDate: "-1w",
			changeMonth: true,
			numberOfMonths: 1,
			dateFormat: "yy-mm-dd",
			<?php if (isset($minDate)): ?>
			minDate: "<?php echo $minDate; ?>",
			<?php endif; ?>
			<?php if (isset($maxDate)): ?>
			maxDate: "<?php echo $maxDate; ?>",
			<?php endif; ?>
			onSelect: function( selectedDate ) {
				var option = this.id == "<?php echo $startId; ?>" ? "minDate" : "maxDate",
					instance = $( this ).data( "datepicker" );
					date = $.datepicker.parseDate(
						instance.settings.dateFormat ||
						$.datepicker._defaults.dateFormat,
						selectedDate, instance.settings );
				dates.not( this ).datepicker( "option", option, date );
			}
		});
	});
	</script>

