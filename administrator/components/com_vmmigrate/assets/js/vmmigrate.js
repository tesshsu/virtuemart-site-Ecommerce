// JavaScript Document

jQuery.noConflict();


jQuery(document).ready(function($) {
	
	var pause_process = false;
	
	$.fn.scrollTo = function(elem, speed) { 
		$(this).animate({
			scrollTop:  $(this).scrollTop() - $(this).offset().top + $(elem).offset().top 
		}, speed == undefined ? 1000 : speed); 
		return this; 
	};
	
	$(".pg").progressbar({
		create: function( event, ui ) {
			//var default_value = jQuery(this).attr( "default" );
			var default_value = parseInt($(this).attr( "default" ));
			if (default_value==-1) {default_value=false;}
			$(this).progressbar( "option", {
				value: default_value
			})
		},
		change: function() {
			var val = $(this).progressbar( "value" );
			var lbl = $(this).children('em');
			progressbarValue = $(this).find( ".ui-progressbar-value" );
			if (val===false) {
				lbl.text(vmmigrate_waiting);
				progressbarValue.css({"background": '#EEE'});
			} else if (val==0) {
				lbl.text(vmmigrate_skip);
			} else {
				lbl.text(val+'%');
				if ($(this).hasClass('with_errors')) {
					progressbarValue.css({"background": '#F03'});
				} else if ($(this).hasClass('with_warnings')) {
					progressbarValue.css({"background": '#F90'});
				} else {
					if (val==100) {
						progressbarValue.css({"background": '#0F0'});
					} else {
						progressbarValue.css({"background": '#FF3'});
					}
				}
			}
      	},
      	complete: function() {
			progressbarValue = $(this).find( ".ui-progressbar-value" );
			if ($(this).hasClass('with_errors')) {
				$(this).children('em').text(vmmigrate_completed_errors);
				progressbarValue.css({"background": '#F03'});
			} else if ($(this).hasClass('with_warnings')) {
				$(this).children('em').text(vmmigrate_completed_warnings);
				progressbarValue.css({"background": '#F90'});
			} else {
				$(this).children('em').text(vmmigrate_completed);
				progressbarValue.css({"background": '#0F0'});
			}
      	}
	});

	$('.stepcbk').change(function() {
		//var rel = $.parseJSON($(this).attr('rel'));
		var extension = $(this).data('extension'); //rel.extension;
		var stepname = $(this).data('step'); //rel.step;
		var checked = $(this).prop('checked');
		var pb = $('tbody#'+extension+' #step_'+stepname);

		pb.progressbar( "option", {
			value: (checked) ? false : 0
		});
	});
	
	$('.checkall-toggle').change(function() {
		var extension = $(this).data('extension'); //$(this).attr('rel');
		var checked = $(this).prop('checked');
		$('tbody#'+extension+' .stepcbk').prop('checked',checked).trigger('change');
	});
	
	
	$('.btn_run').click(function() {
		$(document).scrollTop(0);
		pause_process = false;
		$('.scrollToLog').hide();
		var extension = $(this).data('extension'); //$(this).attr('rel');
		var confirmMessage = '';
		$('tbody#'+extension+' .stepcbk').each(function() {
			//var rel = $.parseJSON($(this).attr('rel'));
			var stepname = $(this).data('step'); //rel.step;
			var checked = $(this).prop('checked');
			var pb = $('tbody#'+extension+' #step_'+stepname);
			if (checked && $(this).data('warning') != undefined && $(this).data('warning') != '') {
				confirmMessage += $(this).data('warning')+'\n'; //rel.warning +'\n';
			}
			pb.removeClass('with_errors');
			pb.removeClass('with_warnings');
			pb.progressbar( "option", {
				value: (checked) ? false : 0
			});
		});
		if (confirmMessage) {
			
			if (confirm(warning_message_label+'\n\n'+confirmMessage+'\n\n'+confirm_message_label)) {
				$('#live_log').empty();
				processStep(extension,'start');
			}
		} else {
			$('#live_log').empty();
			processStep(extension,'start');
		}
		return false;
	});
	
	$('.btn_stop').click(function() {
		pause_process = true;
		return false;
	});
	
	function processStep(extension,step) {
		if (pause_process) {
			alert(vmmigrate_process_paused);
			return false;
		}
		var form = $('#vmMigrateForm_'+extension);
		var formaction = 'index.php?ext='+extension+'&step='+step;
		var logger = $('#live_log');
		//if (!$(logger).find('a[name='+extension+'_'+step+']')) {
			logger.append('<a name="'+extension+'_'+step+'">');
		//}
		$('#scrollToLog_'+extension+'_'+step).show();
		var request = $.ajax({
			url: formaction,
			type: "post",
			data: $(form).serialize(),
			dataType: "json",
			error: function (jqXHR, textStatus, errorThrown) {
				if (jqXHR.responseText) {
					var newLogs = '';
					newLogs += '<ul class="critical"><span class="stepname">'+step+': Something went wrong... '+errorThrown+'</span>';
					newLogs += '<li class="critical">'+jqXHR.responseText+'</li>';
					newLogs += '</ul>';
					logger.append(newLogs);
	                //alert(jqXHR.responseText);
				} else {
					var newLogs = '';
					newLogs += '<ul class="critical"><span class="stepname">'+step+': Something went wrong... '+errorThrown+'</span>';
					newLogs += '<li class="critical">No additional information provided on the error. Please review your system logs</li>';
					newLogs += '</ul>';
					logger.append(newLogs);
					//alert('Something went wrong...');
					//console.log(errorThrown);
				}
            },
		});
		request.done(function(jsonData) {
			var pb = $('tbody#'+extension+' #step_'+jsonData.step);
			var newLogs = '';
			newLogs += '<ul><span class="stepname">'+jsonData.stepName+'</span>';
			if (jsonData.logs) {
				$(jsonData.logs).each(function(entrynum,logentry) {
					newLogs += '<li class="'+logentry.type+'">'+logentry.message+'</li>';
					//logger.append('<li class="'+logentry.type+'">'+logentry.message+'</li>');
					if (logentry.type=='error') {
						pb.addClass('with_errors');
					}
					if (logentry.type=='warning') {
						pb.addClass('with_warnings');
					}
				});
			}
			if (jsonData.systemerror) {
				newLogs += '<li class="systemerror">'+jsonData.systemerror+'</li>';
			}
			newLogs += '</ul>';
			logger.append(newLogs);
			handleLogTogglers();
			if (jsonData.percentage) {
				pb.progressbar( "option", {
					value: jsonData.percentage
				});
			}
			if (jsonData.next) {
				processStep(extension,jsonData.next);
			}
			//$("#live_log_container").animate({ scrollTop: $('#live_log').height()}, 500);
			$("#live_log_container").scrollTop($('#live_log').height());
			if (jsonData.allcompleted) {
				alert(vmmigrate_all_completed);
			}
		});
		request.fail(function(jqXHR, textStatus) {
			//console.log(jqXHR);
			$('#live_log').append("Request failed: " + textStatus);
		});
	}
	
	handleLogToggleInfo = function() {
		if ($('#toggleLogInfo').hasClass('off')) {
			$('#live_log li.info').hide();
		} else {
			$('#live_log li.info').show();
		}
	}
	handleLogToggleWarning = function() {
		if ($('#toggleLogWarnings').hasClass('off')) {
			$('#live_log li.warning').hide();
		} else {
			$('#live_log li.warning').show();
		}
	}
	handleLogToggleError = function() {
		if ($('#toggleLogErrors').hasClass('off')) {
			$('#live_log li.error').hide();
		} else {
			$('#live_log li.error').show();
		}
	}
	handleLogToggleTranslations = function() {
		if ($('#toggleLogTranslations').hasClass('off')) {
			$('#live_log li.translation').hide();
		} else {
			$('#live_log li.translation').show();
		}
	}
	handleLogToggleSystemErrors = function() {
		if ($('#toggleLogSystemErrors').hasClass('off')) {
			$('#live_log li.systemerror').hide();
		} else {
			$('#live_log li.systemerror').show();
		}
	}
	handleLogToggleDebug = function() {
		if ($('#toggleLogDebug').hasClass('off')) {
			$('#live_log li.debug').hide();
		} else {
			$('#live_log li.debug').show();
		}
	}
	handleLogToggleContainer = function() {
		$('#live_log ul').each(function() {
			if($(this).children('li:visible').length == 0) {
				$(this).children('span.stepname').hide();
			} else {
				$(this).children('span.stepname').show();
			}
		});
	}
	handleLogTogglers = function() {
		handleLogToggleInfo();
		handleLogToggleWarning();
		handleLogToggleError();
		handleLogToggleTranslations();
		handleLogToggleSystemErrors();
		handleLogToggleDebug();
		handleLogToggleContainer();
	}
	$('#toggleLogInfo').click(function() {
		$('#toggleLogInfo').toggleClass('off');
		handleLogToggleInfo();
		handleLogToggleContainer();
	})
	$('#toggleLogWarnings').click(function() {
		$('#toggleLogWarnings').toggleClass('off');
		handleLogToggleWarning();
		handleLogToggleContainer();
	})
	$('#toggleLogErrors').click(function() {
		$('#toggleLogErrors').toggleClass('off');
		handleLogToggleError();
		handleLogToggleContainer();
	})
	$('#toggleLogTranslations').click(function() {
		$('#toggleLogTranslations').toggleClass('off');
		handleLogToggleTranslations();
		handleLogToggleContainer();
	})
	$('#toggleLogSystemErrors').click(function() {
		$('#toggleLogSystemErrors').toggleClass('off');
		handleLogToggleSystemErrors();
		handleLogToggleContainer();
	})
	$('#toggleLogDebug').click(function() {
		$('#toggleLogDebug').toggleClass('off');
		handleLogToggleDebug();
		handleLogToggleContainer();
	})
	
	$('.scrollToLog').click(function(){
		var target = $(this).data('target');
		$("#live_log_container").scrollTo($('a[name='+target+']'), 1000);
	});
	
} );