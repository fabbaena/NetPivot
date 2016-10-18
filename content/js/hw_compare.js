var categories = { 
	'l7_req_per_sec': 'L7 req/s (M)',
	'l7_gpbs': 'System Gbps (L7)',
	'ssl_tps_incl': 'Incl SSL tr/sec (k)',
	'ssl_tps_max': 'Max SSL tr/sec (k)',
	'ssl_gbps': 'SSL Gbps',
	'ssl_fips': 'FIPS',
	'cmp_gbps_incl': 'Incl. Compr. (Gbps)',
	'cmp_gbps_max': 'Max. Compr. (Gbps)',
	'virtual_instances_incl': 'Inc. Virtual instances',
	'virtual_instances_max': 'Max. Virtual instances',
	'memory': 'RAM',
	'cpu': 'CPU total cores'
}

function hw_table() {
	$("#compare").html(hw_header);
	for(cat in categories) {
		$("#compare").append(hw_row(cat));
	}

	addTypes("F5", ["BigIP", "Viprion Blade"]);
	addTypes("NetScaler", ["MPX", "SDX"]);
	loadModels("F5", $("#F5_selectedType").html());
	loadModels("NetScaler", $("#NetScaler_selectedType").html());
}

function addTypes(brand, types) {
	for(var i in types) {
		$("#" + brand + "_types").append(
			$("<li>").html(
				$("<a>").attr("href", "#")
					.html(types[i])
					.click({"brand": brand, "type": types[i]}, loadModels2))
			);
	}
}

function loadModels2(e) {
	$("#" + e.data.brand + "_selectedType").html(e.data.type);
	$("#np" + e.data.brand).html("Select Model");
	for(cat in categories) {
		$("#" + cat + "_" + e.data.brand).html("-");
	}

	loadModels(e.data.brand, e.data.type);
}

function loadModels(brand, type) {
	$.ajax( {
		url: "/engine/hw_compare.php",
		data: { 'brand': brand, 'type': type },
		dataType: "json",
		success: function(d) {
			if(d.result == "error") {
				alert("error");
			} else {
				$("#"+brand+"_models").html("");
				for(var i in d.data) {
					$("#"+brand+"_models")
						.append($("<li>").html(
							$("<a>")
								.attr("href", "#")
								.html(d.data[i]["model"])
								.click(d.data[i], clickModel)

							));
				}
			}
		},
		error: function() { alert(error); }
	});	
}

function clickModel(e) {
	$("#np" + e.data.brand).html(e.data.model);
	for(cat in categories) {
		$("#" + cat + "_" + e.data.brand).html(e.data[cat]);

		valNS = parseInt($("#" + cat + "_NetScaler").html());
		valF5 = parseInt($("#" + cat + "_F5").html());
		if(valNS < valF5) {
			$("#" + cat + "_NetScaler").css("background-color", "pink");
		} else {
			$("#" + cat + "_NetScaler").css("background-color", "LimeGreen");
		}
	}
}

function hw_row(cat) {
	return $("<div>").addClass("row")
				.append($("<div>")
					.addClass("col-xs-6")
					.attr("id", cat + "_name")
					.html(categories[cat]))
				.append($("<div>")
					.addClass("col-xs-3 text-right")
					.attr("id", cat + "_F5")
					.html("-"))
				.append($("<div>")
					.addClass("col-xs-3 text-right")
					.attr("id", cat + "_NetScaler")
					.html("-"));

}

function hw_header() {
	return $("<div>")
				.addClass("row")
				.append($("<div>")
					.addClass("col-xs-6")
					.attr("id", "hname")
					.html("&nbsp;"))
				.append($("<div>")
					.addClass("col-xs-3 text-right")
					.attr("id", "hF5")
					.html("F5"))
				.append($("<div>")
					.addClass("col-xs-3 text-right")
					.attr("id", "hNetScaler")
					.html("NetScaler"));
}