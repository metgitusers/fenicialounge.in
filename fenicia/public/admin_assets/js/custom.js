// Datatable for custom excel button and page length
$(document).ready(function() {
			$('.export_btn_dt').DataTable( {
				dom: 'lBfrtip',
				lengthMenu: [[ 10, 25, 50, 100, -1 ],[ '10', '25', '50', '100', 'all' ]],
				pageLength: 100,
        buttons: [{ extend: 'excel', text: 'Download Excel' }]
			} );
	} );

