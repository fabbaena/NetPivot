var curobjid;
var s;
var e;
var c = [];
var curnav="brief";
var filename;
var npmodules2;
var curmodule = "ltm";
var curobjectgroup = "virtual";
var curobjname;
var attributes;
var breadcrumbs = [];

function loaddata() {
    if (typeof filename == 'undefined' || filename == '') {
        $.getJSON("../engine/filename.php", function(data) {
            filename = data;
            showBrief();
        });
    }
}

function showBreadcrumbs() {
    var b = $(".breadcrumb")
        .html($("<li>").html($("<a>")
            .html("Files")
            .attr("href", "index.php")))
        .append($("<li>").html(filename));
    for(i=0; i<breadcrumbs.length; i++) {
        var link_mod = breadcrumbs[i].mod;
        var link_og = breadcrumbs[i].og;
        var link_name = breadcrumbs[i].on;

        b.append($("<li>").html(
            $("<a>").html(link_og + "(" + link_name + ")")
                .attr("link_mod", link_mod)
                .attr("link_og", link_og)
                .attr("link_name", link_name)
                .click(clickBreadcrumb)));
    }
}

function clickBreadcrumb(event) {
    var link_mod = $(event.target).attr("link_mod");
    var link_og = $(event.target).attr("link_og");
    var link_name = $(event.target).attr("link_name");

    for(i=0; i<breadcrumbs.length; i++) {
        if(breadcrumbs[i].mod == link_mod && breadcrumbs[i].og == link_og && breadcrumbs[i].on == link_name) {
            breadcrumbs.splice(i);
            break;
        }
    }

    gotoObject(link_mod, link_og, link_name);
}

function showBrief() {
    $(".nav_buttons").removeClass("disabled");
    $(".nav_brief").addClass("disabled");
    curnav = "brief";
    $(".nav_title").html("Dashboard");
    $(".objects").remove();
    $(".modules").remove();
    if(typeof npmodules2 == 'undefined') {
        $.getJSON("../engine/npmodules2.php", showBrieftable);
    } else {
        showBrieftable(npmodules2);
    }
    showBreadcrumbs();
}

function showBrieftable(data) {
    if(typeof npmodules2 == 'undefined') {
        npmodules2 = data;
    }
    $(".objectstats_view").remove();
    $(".tabs_view").remove();
    $(".objects_view").remove();
    showBriefBigstats($("#content"));
    showBriefSmallstats($("#content"));
}

function showBriefSmallstats(out) {
    var row = $("<div>")
        .addClass("row")
        .addClass("dashboard_view")
        .css("height", "30px");
    out.append(row)
    row = $("<div>")
        .addClass("row")
        .addClass("dashboard_view");

    var table = $("<table>").addClass("table")
            .append($("<tr>").addClass("active")
                .append($("<th>").addClass("text-center")
                    .css("width", "16%")
                    .html("F5 Module"))
                .append($("<th>").addClass("text-center")
                    .css("width", "25%")
                    .html("NetScaler Module"))
                .append($("<th>").addClass("text-center")
                    .css("width", "25%")
                    .html("Converted"))
                .append($("<th>").addClass("text-center")
                    .css("width", "34%")
                    .html("Actions"))
            );
    for(var modulename in npmodules2) {
        if(modulename.substring(0,1) != '_' && npmodules2[modulename]["ns_name"] != '') {
            table.append($("<tr>")
                .append($("<td>").html(npmodules2[modulename]["friendly_name"].toUpperCase()))
                .append($("<td>").html(npmodules2[modulename]["ns_name"].toUpperCase()))
                .append($("<td>").html($("<span>")
                    .html(npmodules2[modulename]["p_converted"])
                    .append("%")
                    .addClass("badge")
                    .addClass("badge_bkground_green_sm")
                    ).addClass("text-center"))
                .append($("<td>").addClass("text-center").html(
                    $("<a>").html("View Module")))
                )
                ;
        }
    }

    row.append($("<div>").addClass("col-xs-7")
        .append(table));

    out.append(row);
}

function showBigstat(logo, logotype, name, value, perc) {
    var out;

    out = $("<div>").addClass("col-md-4")
        .append($("<div>").addClass("container-fluid")
            .append($("<div>").addClass("row")
                .append($("<div>").addClass("col-md-4")
                    .append($("<a>").addClass("btn")
                        .addClass(logotype)
                        .addClass("btn-lg")
                        .addClass("glyphicon")
                        .addClass(logo)
                        .addClass(value==0?'disabled':'')
                        .addClass("db-button")))
                    .append($("<div>").addClass("col-md-8")
                        .append($("<div>").addClass("db-perc")
                            .html(value)
                            .append(perc?"%":""))
                        .append($("<div>").addClass("db-title")
                            .html(name)))
                ));
    return out;
}

function showBriefBigstats(out) {
    var row;

    row = $("<div>").addClass("row").addClass("dashboard_view").css("height", "30px");
    out.append(row);

    row = $("<div>")
        .addClass("row")
        .addClass("dashboard_view")
        .append(showBigstat("glyphicon-signal", "btn-success", "LOADBALANCING CONVERSION", 
            (typeof npmodules2["ltm"] != "undefined")?npmodules2["ltm"]["p_converted"]:0, true))
        .append(showBigstat("glyphicon-home", "btn-primary", "MODULES FOUND", 
            npmodules2["_data"]["module_count"], false))
        .append(showBigstat("glyphicon-flash", "btn-danger", "iRULES", 
            (typeof npmodules2["rule"] != "undefined")?npmodules2["rule"]["object_count"]:0, false));

    out.append(row);
    row = $("<div>")
        .addClass("row")
        .addClass("dashboard_view")
        .append(showBigstat("glyphicon-globe", "btn-warning", "GSLB", 
            (typeof npmodules2["gtm"] != "undefined")?npmodules2["gtm"]["object_count"]:0, false))
        .append(showBigstat("glyphicon-lock", "btn-warning", "AAA", 
            (typeof npmodules2["apm"] != "undefined")?npmodules2["apm"]["object_count"]:0, false))
        .append(showBigstat("glyphicon-fire", "btn-warning", "APPFIREWALL", 
            (typeof npmodules2["asm"] != "undefined")?npmodules2["asm"]["object_count"]:0, false))
    out.append(row);
}

function showModules() {
    $(".nav_buttons").removeClass("disabled");
    $(".nav_modules").addClass("disabled");
    curnav = "modules";
    $(".nav_title").html("Module Details");
    $(".modules").remove();
    $(".dashboard_view").remove();
    $(".objects_view").remove();
    $(".tabs_view").remove();
    showModuleTabs($("#content"));
    showModuleTable();
    getModuleData(curmodule);
}

function showModuleTabs(out) {
    var tabs;
    tabs = $("<ul>").addClass("nav").addClass("nav-pills");
    for(var modulename in npmodules2) {
        if(modulename.substring(0,1) == '_') continue;
        tabs.append($("<li>")
            .attr("role", "presentation")
            .addClass(curmodule==modulename?"active":"")
            .addClass("tab")
            .addClass("tab_"+modulename)
            .html($("<a>")
                .html(npmodules2[modulename]["friendly_name"].toUpperCase())
                .attr("id", modulename)
                .click(clickModuleTab)
                ));
    }
    out.append($("<div>")
        .addClass("row")
        .addClass("tabs_view")
        .append($("<div>").addClass("col-md-12")
            .append(tabs)));
}

function getModuleData(modulename) {
    if(typeof modulename == "undefined") return;
    if(typeof npmodules2[modulename]["object_groups"] == "undefined") {
        $.getJSON("../engine/objectgroups.php", {"module": modulename}, function(data) {
            npmodules2[modulename]["object_groups"] = data;
            showModuleData();
        })
    } else {
        showModuleData()
    }
}

function clickModuleTab(event) {
    curmodule = event.target.id;
    $(".active.tab").removeClass("active");
    $(".tab_" + curmodule).addClass("active");
    getModuleData(event.target.id);
    breadcrumbs = [];
}

function showModuleTable() {
    $(".objectstats_view").remove();

    var table = $("<table>")
        .addClass("table")
        .addClass("objectstats_view")
        .addClass("objectstats_table")
        .append($("<tr>").addClass("active")
            .append($("<th>")
                .css("width", "55%")
                .html(curmodule!='rule'?"F5 Object Groups":"iRule Name")
                .append("&nbsp;")
                .append($("<div>")
                    .addClass("sortStat")
                    .addClass("sortStatName")
                    .addClass("glyphicon glyphicon-sort")
                    .click(clickSortStats)))
            .append($("<th>").css("width", "15%")
                .html(curmodule!='rule'?"# Objects":"")
                .append("&nbsp;")
                .append($("<div>")
                    .addClass("sortStat")
                    .addClass("sortStatObjects")
                    .addClass(curmodule!='rule'?"glyphicon glyphicon-sort":"")
                    .click(clickSortStats)))
            .append($("<th>").css("width", "15%")
                .html("% Converted")
                .append("&nbsp;")
                .append($("<div>")
                    .addClass("sortStat")
                    .addClass("sortStatConverted")
                    .addClass("glyphicon glyphicon-sort")
                    .click(clickSortStats)))
            .append($("<th>").css("width", "15%").html("Actions"))
        );

    $("#content").append($("<div>")
        .addClass("objectstats_view")
        .addClass("custom-margin-top")
        .html(table));
}

function showModuleData() {

    $(".statsData").remove();
    var table = $(".objectstats_table");

    if(typeof npmodules2[curmodule]["_print_order"] == "undefined" || npmodules2[curmodule]["_print_order"].length == 0) {
        setOGPrintOrder("name");
    } 
    for(var keyval in npmodules2[curmodule]["_print_order"]) {
        ogname = npmodules2[curmodule]["_print_order"][keyval]["key"];
        var color = npmodules2[curmodule]["object_groups"][ogname]["p_converted"] == 100 ? 
            "text_color_green" : "text_color_red"
        table.append($("<tr>")
            .addClass("statsData")
            .append($("<td>").html(npmodules2[curmodule]["object_groups"][ogname]["name"]))
            .append($("<td>").html(npmodules2[curmodule]["object_groups"][ogname]["object_count"]))
            .append($("<td>")
                .addClass(color)
                .html($("<strong>").html(npmodules2[curmodule]["object_groups"][ogname]["p_converted"]))
                .append("%")
                )
            .append($("<td>").html(
                $("<a>").attr("id", "vo_"+ogname).html("View Object").click(
                    function(event) {
                        curobjectgroup = event.target.id.substring(3);
                        showObjects();
                    })))
            );

    }
    if(curobjectgroup != "") {
        getObjectData();
    }
}

function clickSortStats(data) {
    var asc;
    if($(data.target).hasClass("glyphicon-sort-by-attributes")) {
        /* sort z-a */
        asc = 0;
        $(data.target).addClass("glyphicon-sort-by-attributes-alt")
        $(data.target).removeClass("glyphicon-sort-by-attributes");
    } else if($(data.target).hasClass("glyphicon-sort-by-attributes-alt")){
        /* sort a-z */
        $(data.target).addClass("glyphicon-sort-by-attributes")
        $(data.target).removeClass("glyphicon-sort-by-attributes-alt");
        asc = 1;
    } else {
        $(".sortStat")
            .removeClass("glyphicon-sort-by-attributes")
            .removeClass("glyphicon-sort-by-attributes-alt");
        $(data.target).addClass("glyphicon-sort-by-attributes")
        asc = 1;
    }

    npmodules2[curmodule]["_print_order"] = [];
    if($(data.target).hasClass("sortStatName")) {
        setOGPrintOrder("name");
        npmodules2[curmodule]["_print_order"].sort(asc?sortStats_asc:sortStats_desc);
        showModuleData();
    } else if($(data.target).hasClass("sortStatObjects")) {
        setOGPrintOrder("object_count");
        npmodules2[curmodule]["_print_order"].sort(asc?sortStats_asc:sortStats_desc);
        showModuleData();
    } else if($(data.target).hasClass("sortStatConverted")) {
        setOGPrintOrder("p_converted");
        npmodules2[curmodule]["_print_order"].sort(asc?sortStats_asc:sortStats_desc);
        showModuleData();
    } else if($(data.target).hasClass("sortStatOmitted")) {
        setOGPrintOrder("attribute_omitted");
        npmodules2[curmodule]["_print_order"].sort(asc?sortStats_asc:sortStats_desc);
        showModuleData();
    } else if($(data.target).hasClass("sortObjectName")) {
        setOPrintOrder("name");
        npmodules2[curmodule]["object_groups"][curobjectgroup]["_print_order"].sort(asc?sortStats_asc:sortStats_desc);
        showObjectData();
    } else if($(data.target).hasClass("sortObjectAttributes")) {
        setOPrintOrder("attribute_count");
        npmodules2[curmodule]["object_groups"][curobjectgroup]["_print_order"].sort(asc?sortStats_asc:sortStats_desc);
        showObjectData();
    } else if($(data.target).hasClass("sortObjectConverted")) {
        setOPrintOrder("p_converted");
        npmodules2[curmodule]["object_groups"][curobjectgroup]["_print_order"].sort(asc?sortStats_asc:sortStats_desc);
        showObjectData();
    } else {
        alert("Something is wrong");
    }
}

function setOGPrintOrder(byvalue) {
    npmodules2[curmodule]["_print_order"] = [];
    for(ogname in npmodules2[curmodule]["object_groups"]) {
        npmodules2[curmodule]["_print_order"].push({
            "key": ogname, 
            "value": npmodules2[curmodule]["object_groups"][ogname][byvalue]});
    }
}

function setOPrintOrder(byvalue) {
    npmodules2[curmodule]["object_groups"][curobjectgroup]["_print_order"] = [];
    for(oname in npmodules2[curmodule]["object_groups"][curobjectgroup]["objects"]) {
        npmodules2[curmodule]["object_groups"][curobjectgroup]["_print_order"].push({
            "key": oname, 
            "value": npmodules2[curmodule]["object_groups"][curobjectgroup]["objects"][oname][byvalue]});
    }
}

function sortStats_asc(a, b) {
    if(a["value"] == b["value"]) return 0;
    if(a["value"] < b["value"]) return -1;
    else return 1;
}

function sortStats_desc(a, b) {
    if(a["value"] == b["value"]) return 0;
    if(a["value"] > b["value"]) return -1;
    else return 1;
}

function showObjects() {
    $(".nav_buttons").removeClass("disabled");
    $(".nav_objects").addClass("disabled");
    curnav = "objects";
    $(".nav_title").html("Object Details")
    $(".objects").remove();
    $(".dashboard_view").remove();
    $(".objectstats_view").remove();
    $(".tabs_view").remove();


    showObjectTabs();
    showObjectPane();
    showObjectSidebar();
    getObjectSidebarData();
    //showObjectTable();
    //getObjectData();
}

function showObjectPane() {
    $("#content").append($("<div>")
        .addClass("row").append($("<div>")
            .addClass("col-xs-3")
            .addClass("no-padding")
            .addClass("custom-margin-top")
            .addClass("objects_view")
            .addClass("objectsSidebar"))
        .append($("<div>")
            .addClass("col-xs-9")
            .addClass("no-padding")
            .addClass("custom-margin-top")
            .addClass("objectstats_view")
            .addClass("objectsData")
            .css("overflow", "scroll"))
        );    
}

function showObjectTabs() {
    var tabs;
    tabs = $("<ul>").addClass("nav").addClass("nav-pills");
    for(var modulename in npmodules2) {
        if(modulename.substring(0,1) == '_') continue;
        tabs.append($("<li>")
            .attr("role", "presentation")
            .addClass(curmodule==modulename?"active":"")
            .addClass("tab")
            .addClass("tab_"+modulename)
            .html($("<a>")
                .html(npmodules2[modulename]["friendly_name"].toUpperCase())
                .attr("id", modulename)
                .click(clickObjectTab)
                ));
    }
    
    $("#content").append($("<div>")
            .addClass("row").addClass("tabs_view")
            .append($("<div>").addClass("col-md-12")
                .append(tabs)));
}

function clickObjectTab(data) {
    curmodule = event.target.id;
    $(".active.tab").removeClass("active");
    $(".tab_" + curmodule).addClass("active");
    $(".objectObjectsTable").remove();
    getObjectSidebarData();
    breadcrumbs = [];
    showBreadcrumbs();
}

function getObjectSidebarData() {
    if(typeof curmodule == "undefined") return;
    if(typeof npmodules2[curmodule]["object_groups"] == "undefined") {
        $.getJSON("../engine/objectgroups.php", {"module" : curmodule}, function(data) {
            npmodules2[curmodule]["object_groups"] = data;
            showObjectOGData();
        });
    } else {
        showObjectOGData();
    }
}

function showObjectSidebar() {
    $(".objectsSidebar").append($("<div>")
        .addClass("side-menu")
        .append($("<nav>")
            .addClass("navbar")
            .addClass("navbar-default")
            .addClass("no-black")
            .attr("role", "navigation")
            .append($("<ul>")
                .addClass("side-menu-container")
                .addClass("nav")
                .addClass("nav-pills")
                .addClass("nav-stacked")
                .addClass("custom-side-menu")
                .addClass("og_sidemenu")
                .append($("<li>")
                    .addClass("list-group-item")
                    .addClass("text-center")
                    .addClass("gray_backgr")
                    .append($("<strong>")
                        .html(curmodule=='rule'?"iRule Name":"Object Group Name")
                    )
                )
            )
        ));
}

function showObjectOGData(data) {
    $(".og_sidemenu_item").remove();
    for(var ogname in npmodules2[curmodule]["object_groups"]) {
        $(".og_sidemenu").append($("<li>")
            .addClass("og_sidemenu_item")
            .addClass(ogname==curobjectgroup?"active":"")
            .click(clickObjectOG)
            .html($("<a>")
                .attr("id", npmodules2[curmodule]["object_groups"][ogname]["name"])
                .addClass("og_sidemenu_item")
                .html(npmodules2[curmodule]["object_groups"][ogname]["name"])));
    }
    if(curobjectgroup != "") {
        showObjectTable();
        getObjectData();
    }
}

function clickObjectOG(data) {
    curobjectgroup = data.target.id;
    curobjid = 0;
    curobjname = "";
    $(".active.og_sidemenu_item").removeClass("active");
    $(data.target).parent().addClass("active");

    showObjectTable();
    getObjectData();
    breadcrumbs = [];
    showBreadcrumbs();
}

function showObjectTable() {
    $(".objectsData")
        .css("overflow", "scroll")
        .css("height", "800px")
        .html($("<table>")
        .addClass("table")
        .addClass("objectObjectsTable")
        .css("table-layout", "fixed")
        .css("width", "100%")
        .append($("<tr>").addClass("active")
            .append($("<th>")
                .css("width", "70%")
                .append("Object Name")
                .append("&nbsp;")
                .append($("<div>")
                    .addClass("glyphicon")
                    .addClass("glyphicon-sort")
                    .addClass("sortStat")
                    .addClass("sortObjectName")
                    .click(clickSortStats)))
            .append($("<th>")
                .css("width", "15%")
                .append("Attributes")
                .append("&nbsp;")
                .append($("<div>")
                    .addClass("glyphicon")
                    .addClass("glyphicon-sort")
                    .addClass("sortStat")
                    .addClass("sortObjectAttributes")
                    .click(clickSortStats)))
            .append($("<th>")
                .css("width", "15%")
                .append("Converted")
                .append("&nbsp;")
                .append($("<div>")
                    .addClass("glyphicon")
                    .addClass("glyphicon-sort")
                    .addClass("sortStat")
                    .addClass("sortObjectConverted")
                    .click(clickSortStats)))
            ));
}

function getObjectData() {
    if(typeof curmodule == "undefined" || typeof curobjectgroup == "undefined") return;
    if(typeof npmodules2[curmodule]["object_groups"] == "undefined") {
        getModuleData(curmodule);
        return;
    }
    if(typeof npmodules2[curmodule]["object_groups"][curobjectgroup] == "undefined") {
        curobjectgroup = "";
        return;
    }
    if(typeof npmodules2[curmodule]["object_groups"][curobjectgroup]["objects"] == "undefined") {
        $.getJSON("../engine/objects.php", {
                "module" : curmodule, 
                "object_group": curobjectgroup
            }, function(data) {
                npmodules2[curmodule]["object_groups"][curobjectgroup]["objects"] = data;
                showObjectData();
            })
    } else {
        showObjectData();
    }
}

function showObjectData() {

    if(typeof npmodules2[curmodule]["object_groups"][curobjectgroup]["_print_order"] == "undefined") {
        setOPrintOrder("name");
    }

    $(".object_item").remove();
    var curog = npmodules2[curmodule]["object_groups"][curobjectgroup];
    for(var keyval in curog["_print_order"]) {
        oname = curog["_print_order"][keyval]["key"];
        var curog_id = curog["objects"][oname]["id"];
        var curog_ac = curog["objects"][oname]["attribute_count"];
        var curog_pc = curog["objects"][oname]["p_converted"];
        var perc_color = curog_pc==100?"text_color_green":"text_color_red";
        $(".objectObjectsTable")
            .append($("<tr>")
                .addClass("object_item")
                .append($("<td>")
                    .append($("<span>")
                        .addClass("glyphicon")
                        .addClass("glyphicon-collapse-down")
                        .attr("id", "b_details_" + curog_id)
                        .attr("objid", curog_id)
                        .attr("objname", oname)
                        .click(clickObjectAttributes))
                    .append($("<span>")
                        .attr("id", "l_details_" + curog_id)
                        .append(oname)))
                .append($("<td>")
                    .append(curog_ac))
                .append($("<td>")
                    .addClass(perc_color)
                    .append($("<strong>")
                        .append(curog_pc)
                        .append("%")))
                )
            .append($("<tr>")
                .addClass("object_item")
                .append($("<td>")
                    .css("border-top", "none")
                    .attr("colspan", 4)
                    .attr("id", "div_details_" + curog_id))
                );
    }
    if(typeof curobjname != "undefined" && curobjname != "") {
        if(typeof npmodules2[curmodule]["object_groups"][curobjectgroup]["objects"][curobjname] != "undefined") {
            var refid = npmodules2[curmodule]["object_groups"][curobjectgroup]["objects"][curobjname].id;
            loadObjectAttributes(refid, curobjname);
        } else {
            alert("Object not found in file");
        }
    }
}

function clickObjectAttributes(event) {
    var target_id   = $(event.target).attr("id").substring(10);
    var target_name = $(event.target).attr("objname");
    breadcrumbs = [];

    loadObjectAttributes(target_id, target_name);
}

function loadObjectAttributes(target_id, target_name) {
    $("#b_details_" + target_id)
        .addClass("glyphicon-collapse-down")
        .removeClass("glyphicon-collapse-up");

    $("#div_details_" + curobjid).html("");
    if(curobjid != target_id) {
        curobjid = target_id;
        curobjname = target_name;
        breadcrumbs.push({
            "mod": curmodule,
            "og": curobjectgroup, 
            "oid": curobjid,
            "on": curobjname
        });

        $.getJSON("../engine/loadattrs.php", { "objid": target_id }, showdata);
    } else {
        curobjid = 0;
        curobjname = "";
        breadcrumbs = [];
    }
    showBreadcrumbs();
}

function loadattrs(data) {
    $("#b_details_" + curobjid)
        .addClass("glyphicon-collapse-down")
        .removeClass("glyphicon-collapse-up");

    $("#" + curobjid).html("");
    if(curobjid != data) {
        curobjid = data;
        $.getJSON("../engine/loadattrs.php", { "objid": data }, showdata);
    } else {
        curobjid = 0;
    }
}

function okIcon() {
    return $("<span>").addClass("glyphicon glyphicon-ok-sign");
}

function noIcon() {
    return $("<span>").addClass("glyphicon glyphicon-remove-sign");
}

function ignoreIcon() {
    return $("<span>").addClass("glyphicon glyphicon-question-sign");
}

function gotoIcon(link) {
    return $("<span>")
        .addClass("glyphicon glyphicon-open")
        .click(clickGotoObject)
        .attr("link_mod", link.mod)
        .attr("link_og", link.og)
        .attr("link_name", link.on);
}

function showdata(data) {
    var attributelist = $("<table>").addClass("table");

    attributelist.append($("<tr>")
        .append($("<th>").width("50px").html("Line"))
        .append($("<th>").width("40px").html("Conv"))
        .append($("<th>").width("40px").html("Link"))
        .append($("<th>").html("F5 Source Code")));

    for(var lineno in data) {
        attributelist.append($("<tr>")
            .append($("<td>")
                .html(lineno))
            .append($("<td>")
                .attr("id", "conv" + lineno))
            .append($("<td>")
                .attr("id", "link" + lineno))
            .append($("<td>")
                .html(data[lineno]["source"].replace(/ /g, '&nbsp;')))
            .attr("id", "lineno" + lineno));
    }

    $("#div_details_" + curobjid)
        .html(attributelist);

    $("#b_details_" + curobjid)
        .removeClass("glyphicon-collapse-down")
        .addClass("glyphicon-collapse-up");
    $.getJSON("../engine/get_attributes_json.php", 
        {
            "objid": curobjid,
            "object_group": curobjectgroup, 
            "object_name": $("#l_details_" + curobjid).text()
        }, 
        function(attributes) {
            for(attribute in attributes) {
                lineno = attributes[attribute].line;
                if(attributes[attribute].converted == 1) {
                    $("#lineno" + lineno).addClass("converted_line");
                    $("#conv" + lineno).html(okIcon());
                } else {
                    $("#lineno" + lineno).addClass("notconverted_line");
                    $("#conv" + lineno).html(noIcon());
                }
                switch(attributes[attribute].name) {
                    case "persist":
                        for(i in attributes[attribute].value) {
                            persistence = attributes[attribute].value[i];
                            p = persistence.name;
                            var pname = p.substring(0,1)=='/'?p.split("/")[2]:p;
                            setLink(persistence.line, "ltm", "persistence", pname);
                        }
                        break;
                    case "profiles":
                        for(i in attributes[attribute].value) {
                            profile = attributes[attribute].value[i];
                            p = profile.name;
                            var pname = p.substring(0,1)=='/'?p.split("/")[2]:p;
                            setLink(profile.line, "ltm", "profile", pname);
                        }
                        break;
                    case "destination":
                        if(curobjectgroup != "virtual") break;
                        var d = attributes[attribute].value;
                        var dname = d.substring(0,1)=='/'?d.split("/")[2].split(":")[0]:d.split(":")[0];
                        setLink(attributes[attribute].line, "ltm", "virtual-address", dname);
                        break;
                    case "pool":
                        var p = attributes[attribute].value;
                        var pname = p.substring(0,1)=='/'?p.split("/")[2]:p;
                        setLink(attributes[attribute].line, "ltm", "pool", pname);
                        break;
                    case "members":
                        if(Object.prototype.toString.call(attributes[attribute].value) === "[object Array]") {
                            for(i in attributes[attribute].value) {
                                member = attributes[attribute].value[i];
                                m = member.name;
                                var mname = m.substring(0,1)=='/'?m.split("/")[2].split(":")[0]:m.split(":")[0];
                                if(curobjectgroup == "pool")
                                    setLink(member.line, "ltm", "node", mname);
                                for(j in member.attributes){
                                    mattr = member.attributes[j];
                                    if(mattr.converted == 1) {
                                        $("#lineno" + mattr.line).addClass("converted_line");
                                        $("#conv" + mattr.line).html(okIcon());
                                    } else {
                                        $("#lineno" + mattr.line).addClass("notconverted_line");
                                        $("#conv" + mattr.line).html(noIcon());
                                    }
                                }
                            }
                        } else {
                            member = attributes[attribute].value;
                            m = member.name;
                            var mname = m.substring(0,1)=='/'?m.split("/")[2].split(":")[0]:m.split(":")[0];
                            if(curobjectgroup == "pool") 
                                setLink(member.line, "ltm", "node", mname);
                            for(j in member.attributes){
                                mattr = member.attributes[j];
                                if(mattr.converted == 1) {
                                    $("#lineno" + mattr.line).addClass("converted_line");
                                    $("#conv" + mattr.line).html(okIcon());
                                } else {
                                    $("#lineno" + mattr.line).addClass("notconverted_line");
                                    $("#conv" + mattr.line).html(noIcon());
                                }
                            }
                            if(member.converted == 1) {
                                $("#lineno" + member.line).addClass("converted_line");
                                $("#conv" + member.line).html(okIcon());
                            } else {
                                $("#lineno" + member.line).addClass("notconverted_line");
                                $("#conv" + member.line).html(noIcon());
                            }
                        }
                        break;
                    case "rules":
                        for(i in attributes[attribute].value) {
                            rule = attributes[attribute].value[i];
                            r = rule.name;
                            var rname = r.substring(0,1)=='/'?r.split("/")[2]:r;
                            setLink(rule.line, "rule", rname, rname);
                        }
                        break;
                    case "source-address-translation":
                        for(i in attributes[attribute].value) {
                            sat = attributes[attribute].value[i];
                            $("#lineno" + sat.line).addClass("converted_line");
                            $("#conv" + sat.line).html(okIcon());
                        }
                        break;
                    case "snatpool":
                        snatpool = attributes[attribute];
                        s = snatpool.value;
                        spname = s.substring(0,1)=='/'?s.split("/")[2]:s.split(":")[0];
                        setLink(snatpool.line, "ltm", "snatpool", spname);
                        break;
                    default:
                        break;
                }
            }
        });
    var tabletop = $("#b_details_" + curobjid).parent().parent().parent().position().top;
    var objtop = $("#b_details_" + curobjid).parent().parent().position().top;
    $(".objectsData").scrollTop(objtop-tabletop);
}

function setLink(line, mod, og, on) {
    var link = {
            "mod": mod, 
            "og": og, 
            "on": on
        };
    //$("#lineno" + line).addClass("linked_line");
    $("#link" + line).html(gotoIcon(link));
}

function clickGotoObject(event) {
    var link_mod = $(event.target).attr("link_mod");
    var link_og = $(event.target).attr("link_og");
    var link_name = $(event.target).attr("link_name");

    gotoObject(link_mod, link_og, link_name);
}

function gotoObject(link_mod, link_og, link_name) {

    var module = link_mod;
    curobjectgroup = link_og;
    curobjname = link_name;

    $(".active.og_sidemenu_item").removeClass("active");
    $("#" + curobjectgroup).parent().addClass("active");

    showBreadcrumbs();
    if(module != curmodule) {
        curmodule = module;
        $(".active.tab").removeClass("active");
        $(".tab_" + curmodule).addClass("active");
        $(".objectObjectsTable").remove();
        getObjectSidebarData();
    } else {
        showObjectTable();
        getObjectData();
    }
}

function loadRoles(modifyData) {
    $.getJSON("../engine/load_roles.php",
        function(data) {
            for(var role in data.roles) {
                $("<label>")
                    .addClass("btn")
                    .addClass("btn-primary")
                    .addClass("btn-role")
                    .append($("<input>")
                        .change(modifyData)
                        .attr("id", "role_" + data.roles[role].id)
                        .attr("type", "checkbox"))
                    .append(data.roles[role].name)
                    .appendTo(".btn-group");
            }
        })
}
function loadUser(id) {
    $.getJSON("../engine/load_user.php", {"id" : id}, 
        function(data) {
            $("#panel-title").append(data.name);
            $("#max_files").attr("value", data.max_files);
            $("#max_conversions").attr("value", data.max_conversions);
            for(var role in data.roles) {
                $("#role_" + data.roles[role].id)
                    .attr("checked", "checked")
                    .parent().addClass("active");
            }
        })
}
