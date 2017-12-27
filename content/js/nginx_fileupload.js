var dialog = "";

function initFileUpload() {
    'use strict';
    // Change this to the location of your server-side upload handler:
    var url = '/engine/nginx_uploader.php';
    $("#btn_nsdownload").click(download_nsconfig);
    $("#btn_errorout").click(download_errorout);
    $('#fileupload').fileupload({
        url: url,
        dataType: 'json',
        add: function (e, data) {
            dialog = "lp";
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


function purge() {
    dialog = "lr";
    $("#lr").html(
        $("<li>")
            .html($("<i>")
                .addClass("glyphicon glyphicon-refresh")
                .attr("id", "convert_ico"))
            .append("&nbsp;Purging old data...")
            .addClass("list-group-item list-group-item-info")
            .attr("id", "purge")
        );
    $.ajax( {
        url: "/engine/purge.php",
        data: { 'uuid': reprocess_uuid },
        dataType: "json",
        success: function(data) {
            $("#purge").append(": " + data.message);
            if(data.result == "error") {
                $("#purge")
                    .removeClass("list-group-item-success")
                    .removeClass("list-group-item-info")
                    .addClass("list-group-item-danger");
                $(".glyphicon-refresh")
                    .addClass("glyphicon-remove")
                    .removeClass("glyphicon-refresh");
            } else {
                $("#purge")
                    .removeClass("list-group-item-danger")
                    .removeClass("list-group-item-info")
                    .addClass("list-group-item-success");
                $(".glyphicon-refresh")
                    .addClass("glyphicon-ok")
                    .removeClass("glyphicon-refresh");
                convert(reprocess_uuid);
            }
        },
        error: catch_error
    });
}


function convert(uuid) {
    $("#" + dialog).append(
        $("<li>")
            .html($("<i>")
                .addClass("glyphicon glyphicon-refresh")
                .attr("id", "convert_ico"))
            .append("&nbsp;Converting")
            .addClass("list-group-item list-group-item-info")
            .attr("id", "convert")
        );
    $.ajax( {
        url: "/engine/nginx_execute.php",
        data: { 'uuid': uuid , 'projectid': $("#projectid").val() },
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
                show_download_buttons(data.uuid);
            }
        },
        error: catch_error
    });
}

function show_download_buttons(uuid) {
    $(".result")
        .removeClass("hidden")
        .attr("uuid", uuid)
}

function download_nsconfig(event) {
    $('a#btn_nsdownload').attr({
        target: '_blank', 
        href  : '../engine/nginx_download.php?file=config&uuid=' + $(event.target).attr("uuid")});
}

function download_errorout(event) {
    $('a#btn_errorout').attr({
        target: '_blank', 
        href  : '../engine/nginx_download.php?file=error&uuid=' + $(event.target).attr("uuid")});
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

function showFilelist(data) {
    if(data.count == 0) return;
    $("#nofiles").html("");
    for (var i = 0; i < data.files.length; i++) {
        $("#fl").append($("<tr>")
            .attr("id", "f_" + data.files[i].uuid)
            .append($("<td>")
                .css("width", "55%")
                .html(data.files[i].filename)
                .append("&nbsp;")
                .append(version_alert(data.files[i])))
            .append($("<td>")
                .css("width", "25%")
                .html(data.files[i].upload_time.split(" ")[0]))
            .append($("<td>")
                .css("width", "20%")
                .append(content_button())
                .append("&nbsp;&nbsp;")
                .append(rename_button())
                .append("&nbsp;&nbsp;")
                .append(reprocess_button()))
            );
    }
}

function version_alert(data) {

    if(typeof data._conversion !== "undefined" && 
            data._conversion !== null && 
            typeof data._conversion.np_version !== "undefined" &&
            data._conversion.np_version !== null) {
        v = data._conversion.np_version;
        var file_ver = v.split(".");
        var np = np_version.split(".");
        if(np[0] == file_ver[0] && np[1] == file_ver[1] && np[2] == file_ver[2]) return "";
    }

    return $("<span>")
        .attr("data-toggle", "tooltip")
        .html("&nbsp;Please re-process")
        .addClass("glyphicon glyphicon-alert")
        .css("color", "orange");
}

function content_button() {
    return $("<a>")
        .addClass("btn")
        .addClass("btn-primary")
        .addClass("btn-xs")
        .html($("<span>")
            .addClass("glyphicon glyphicon-sunglasses")
            .attr("aria-hidden", "true"))
        .click(function (e, data) {
            window.location.replace('content.php?uuid='+
                e.currentTarget.parentNode.parentNode.id.substring(2));
        });
}

function reprocess_button() {
    return $("<a>")
        .addClass("btn")
        .addClass("btn-danger")
        .addClass("btn-xs")
        .html($("<span>")
            .addClass("glyphicon glyphicon-retweet")
            .attr("data-toggle", "modal")
            .attr("data-target", "#reprocessModal"))
        .click(function(e, data) { 
            reprocess_uuid = e.currentTarget.parentNode.parentNode.id.substring(2);
            });
}

function rename_button() {
    return $("<a>")
        .addClass("btn")
        .addClass("btn-warning")
        .addClass("btn-xs")
        .html($("<span>")
            .addClass("glyphicon glyphicon-pencil")
            .attr("aria-hidden", "true"))
        .click(function (e, data) {
            window.location.replace('rename.php?file='+e.currentTarget.parentNode.parentNode.id.substring(2));
        });
}


