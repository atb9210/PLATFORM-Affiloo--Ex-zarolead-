function loadDatePicker(id, date) {
    'use strict';

    if($('#' + id).length) {
        var dateObj = new Date();
        if (date != '') {
            dateObj = new Date(date);
        }
        var today = new Date(dateObj.getFullYear(), dateObj.getMonth(), dateObj.getDate());
        $('#' + id).datepicker({
            format: "yyyy-mm-dd",
            todayHighlight: true,
            autoclose: true
        });
        //$('#' + id).datepicker('setDate', today);
    }
}

$(function() {
    loadDatePicker('filter_date_from_picker');
    loadDatePicker('filter_date_to_picker');
});
