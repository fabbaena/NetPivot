function initFileUpload() {
    'use strict';
    // Change this to the location of your server-side upload handler:
    var url = '/engine/uploader.php';
    $('#fileupload').fileupload({
        url: url,
        dataType: 'json',
        add: function (e, data) {
        	$(".progress-bar").html("Uploading")
				.removeClass("progress-bar-danger")
				.removeClass("progress-bar-success")
				.addClass("progress-bar-info");
			$("#lp").html(
				$("<li>")
					.html($("<i>")
						.addClass("glyphicon glyphicon-refresh")
						.attr("id", "upload_ico"))
					.append("&nbsp;Uploading")
					.addClass("list-group-item list-group-item-info")
					.attr("id", "upload")
				)
        	data.submit();
        },
        done: function (e, data) {
            $("#upload").append(": " + data.result.message);
        	$(".progress-bar").html(data.result.result);
        	if(data.result.result == "Error") {
        		$(".progress-bar")
        			.removeClass("progress-bar-info")
        			.removeClass("progress-bar-success")
        			.addClass("progress-bar-danger");
        		$("#upload")
        			.removeClass("list-group-item-success")
        			.removeClass("list-group-item-info")
        			.addClass("list-group-item-danger");
        		$(".glyphicon-refresh")
        			.addClass("glyphicon-remove")
        			.removeClass("glyphicon-refresh");
        	} else {
        		$(".progress-bar")
        			.removeClass("progress-bar-info")
        			.removeClass("progress-bar-danger")
        			.addClass("progress-bar-success");
        		$("#upload")
        			.removeClass("list-group-item-info")
        			.removeClass("list-group-item-danger")
        			.addClass("list-group-item-success");
        		$(".glyphicon-refresh")
        			.removeClass("glyphicon-refresh")
        			.addClass("glyphicon-ok");
        		convert(data.result.uuid);
        	}
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress .progress-bar').css(
                'width',
                progress + '%'
            );
        }
    }).prop('disabled', !$.support.fileInput)
        .parent().addClass($.support.fileInput ? undefined : 'disabled');
}

function convert(uuid) {
	$("#lp").append(
		$("<li>")
			.html($("<i>")
				.addClass("glyphicon glyphicon-refresh")
				.attr("id", "convert_ico"))
			.append("&nbsp;Converting")
			.addClass("list-group-item list-group-item-info")
			.attr("id", "convert")
		);
	$.ajax( {
		url: "/engine/execute.php",
		data: { 'uuid': uuid },
		dataType: "json",
		success: function(data) {
			$("#convert").append(": " + data.message);
			if(data.result == "error") {
				$("#convert")
					.removeClass("list-group-item-success")
					.removeClass("list-group-item-info")
	    			.addClass("list-group-item-danger");
	    		$(".glyphicon-refresh")
	    			.addClass("glyphicon-remove")
	    			.removeClass("glyphicon-refresh");
			} else {
				$("#convert")
					.removeClass("list-group-item-danger")
					.removeClass("list-group-item-info")
	    			.addClass("list-group-item-success");
	    		$(".glyphicon-refresh")
	    			.addClass("glyphicon-ok")
	    			.removeClass("glyphicon-refresh");
			}
			stats(data.uuid);
		},
		error: catch_error
	});
}

function stats(uuid) {
	$("#lp").append(
		$("<li>")
			.html($("<i>")
				.addClass("glyphicon glyphicon-refresh")
				.attr("id", "stats_ico"))
			.append("&nbsp;Generating Stats")
			.addClass("list-group-item list-group-item-info")
			.attr("id", "stats")
		);
	$.ajax( {
		url: "/engine/stats.php",
		data: { 'uuid': uuid },
		dataType: "json",
		success: function(data) {
			$("#stats").append(": " + data.message);
			if(data.result == "error") {
				$("#stats")
					.removeClass("list-group-item-success")
					.removeClass("list-group-item-info")
	    			.addClass("list-group-item-danger");
	    		$(".glyphicon-refresh")
	    			.addClass("glyphicon-remove")
	    			.removeClass("glyphicon-refresh");
			} else {
				$("#stats")
					.removeClass("list-group-item-danger")
					.removeClass("list-group-item-info")
	    			.addClass("list-group-item-success");
	    		$(".glyphicon-refresh")
	    			.addClass("glyphicon-ok")
	    			.removeClass("glyphicon-refresh");
			}
			goto_dashboard(data.uuid);
		},
		error: catch_error
	});
}

function goto_dashboard(uuid) {
	$("#lp").append(
		$("<li>")
			.html("Click here to view the conversion")
			.addClass("list-group-item list-group-item-info")
			.attr("id", "stats")
			.click(function() {
				window.location.replace("content.php");
			})
		);
}

function catch_error(jqxhr, textStatus, errorThrown) {
	url = this.url;
	uuid = url.substr(url.indexOf("uuid=")+1);
	$("#convert").append(": System error. Please contact " +
		"the administrator with the following code " + uuid);
	$("#convert")
		.removeClass("list-group-item-success")
		.removeClass("list-group-item-info")
		.addClass("list-group-item-danger");
	$(".glyphicon-refresh")
		.addClass("glyphicon-remove")
		.removeClass("glyphicon-refresh");
}