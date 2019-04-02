jQuery(document).ready(function(){ 

jQuery("#slider-range").slider({
    range: true,
    min: 0,
    max: 1440,
    step: 15,
    values: [autopoint, autovalue],
    slide: function (e, ui) { 
        var hours1 = Math.floor(ui.values[0] / 60);
        var minutes1 = ui.values[0] - (hours1 * 60);

        if (hours1.length == 1) hours1 = '0' + hours1;
        if (minutes1.length == 1) minutes1 = '0' + minutes1;
        if (minutes1 == 0) minutes1 = '00';
        if (hours1 >= 12) {
            if (hours1 == 12) {
                hours1 = hours1;
                minutes1 = minutes1 + " PM";
            } else {
                hours1 = hours1 - 12;
                minutes1 = minutes1 + " PM";
            }
        } else {
            hours1 = hours1;
            minutes1 = minutes1 + " AM";
        }
        if (hours1 == 0) {
            hours1 = 12;
            minutes1 = minutes1;
        } 

        jQuery('.slider-time').val(hours1 + ':' + minutes1);

        var hours2 = Math.floor(ui.values[1] / 60);
        var minutes2 = ui.values[1] - (hours2 * 60);

        if (hours2.length == 1) hours2 = '0' + hours2;
        if (minutes2.length == 1) minutes2 = '0' + minutes2;
        if (minutes2 == 0) minutes2 = '00';
        if (hours2 >= 12) {
            if (hours2 == 12) {
                hours2 = hours2;
                minutes2 = minutes2 + " PM";
            } else if (hours2 == 24) {
                hours2 = 11;
                minutes2 = "59 PM";
            } else {
                hours2 = hours2 - 12;
                minutes2 = minutes2 + " PM";
            }
        } else {
            hours2 = hours2;
            minutes2 = minutes2 + " AM";
        }

        jQuery('.slider-time2').val(hours2 + ':' + minutes2);
    }
});

function getSlideVal(timeVal) {
	timeVal = timeVal.split(' ');

	if (timeVal.length > 1) {
		if(timeVal[1].trim() == 'PM') {
			timeVal = jQuery.trim(timeVal[0]).split(':');
			timeVal[0] = parseInt(timeVal[0]);
			if(timeVal[0]<12) timeVal[0] = timeVal[0] + 12;
		} else if(timeVal[1].trim() == 'AM') {
			timeVal = jQuery.trim(timeVal[0]).split(':');
			timeVal[0] = parseInt(timeVal[0]);
			if(timeVal[0] == 12) timeVal[0] = 0;
		}
	} else {
		timeVal = jQuery.trim(timeVal[0]).split(':');
		timeVal[0] = parseInt(timeVal[0]);
	}

	if(timeVal.length > 1) {
		timeVal =  timeVal[0] * 60 +  parseInt(timeVal[1])
	} else {
		timeVal =  timeVal[0] * 60;
	}

	return timeVal;
} 

jQuery(".slider-range").each(function(index) {
	var time_start = getSlideVal(jQuery(this).parent().parent().find('.slider-time').val());
	var time_end = getSlideVal(jQuery(this).parent().parent().find('.slider-time2').val());

	jQuery(this).slider({
		range: true,
		min: 0,
		max: 1440,
		step: 15,
		values: [time_start, time_end],
		slide: function (e, ui) { 
			var hours1 = Math.floor(ui.values[0] / 60);
			var minutes1 = ui.values[0] - (hours1 * 60);

			if (hours1.length == 1) hours1 = '0' + hours1;
			if (minutes1.length == 1) minutes1 = '0' + minutes1;
			if (minutes1 == 0) minutes1 = '00';
			if (hours1 >= 12) {
				if (hours1 == 12) {
					hours1 = hours1;
					minutes1 = minutes1 + " PM";
				} else {
					hours1 = hours1 - 12;
					minutes1 = minutes1 + " PM";
				}
			} else {
				hours1 = hours1;
				minutes1 = minutes1 + " AM";
			}
			if (hours1 == 0) {
				hours1 = 12;
				minutes1 = minutes1;
			} 

			jQuery(this).parent().parent().find('.slider-time').val(hours1 + ':' + minutes1);

			var hours2 = Math.floor(ui.values[1] / 60);
			var minutes2 = ui.values[1] - (hours2 * 60);

			if (hours2.length == 1) hours2 = '0' + hours2;
			if (minutes2.length == 1) minutes2 = '0' + minutes2;
			if (minutes2 == 0) minutes2 = '00';
			if (hours2 >= 12) {
				if (hours2 == 12) {
					hours2 = hours2;
					minutes2 = minutes2 + " PM";
				} else if (hours2 == 24) {
					hours2 = 11;
					minutes2 = "59 PM";
				} else {
					hours2 = hours2 - 12;
					minutes2 = minutes2 + " PM";
				}
			} else {
				hours2 = hours2;
				minutes2 = minutes2 + " AM";
			}

			jQuery(this).parent().parent().find('.slider-time2').val(hours2 + ':' + minutes2);
		}
	});

});

jQuery("select.random_post").each(function(index){
	jQuery(this).val(jQuery(this).prev().val());
});

jQuery('.post_val').on('change', function() {
	var post_min = parseInt(jQuery(this).parent().find('.post_min').val());
	var post_max = parseInt(jQuery(this).parent().find('.post_max').val());

	if (post_min > post_max)
	{
		alert("Wrong value!");
		return false;
	} else {
		jQuery(this).parent().find('.eposts_per_select').val((post_min + post_max) /2).trigger('change');
	}

});

jQuery(".save_btn").on("click", function(){
	jQuery(this).parent().parent().next().find(".update_btn").click();
});

});
