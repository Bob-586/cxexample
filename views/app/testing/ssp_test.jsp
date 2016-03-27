$(document).ready(function () {
    var oldStart = 0;
    $('#AdminList').dataTable({
        "order": [[1, "asc"]],
        "iDisplayLength": 25,
        "processing": true,
        "serverSide": true,
        "ajax": "<?php echo $local->get_url('/app/testing', 'ajax_ssp', $q); ?>",
        "oLanguage": {
            "sEmptyTable": "There are no admins to display."
        },
        "fnDrawCallback": function (o) {
            if (o._iDisplayStart != oldStart) {
                var targetOffset = $('#AdminListHeader').offset().top;
                $('html,body').animate({scrollTop: targetOffset}, 100);
                oldStart = o._iDisplayStart;
            }
        }
    });

    $("#AdminList").fadeIn(500);

});
