$( document ).ready(function() {
	var oldStart = 0;
   	$('#UserList').dataTable({				
			"order": [[ 2, "asc" ]],
			"iDisplayLength": 25,
			"processing": true,
        "serverSide": true,
        "ajax": "<?php echo $local->get_url('/app/users', 'ajax_ssp_users_list', $q); ?>",
			"oLanguage": {            
            "sEmptyTable": "There are no users to display."
        },	
			 "fnDrawCallback": function (o) {
            if ( o._iDisplayStart != oldStart ) {
                var targetOffset = $('#AdminListHeader').offset().top;
                $('html,body').animate({scrollTop: targetOffset}, 100);
                oldStart = o._iDisplayStart;
            }
        }
		});
		
	

	$("#AdminList").fadeIn(500);
	
});
