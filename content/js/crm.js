var allCustomers = {};
var allContacts = {};
var allProjects = {};

function Customer(data) {
    if(typeof data === 'object') {
        if(data.constructor == FormData) {
            this.id = data.getAll('customerid');
            this.name = data.getAll('CustomerName');
            this.phone = data.getAll('Phone');
            this.usercreate = data.getAll('usercreate');
            this.userupdate = data.getAll('userupdate');
        } else {
            this.id = data.id;
            this.name = data.name;
            this.phone = data.phone;
            this.createdate = data.createdate;
            this.updatedate = data.updatedate;
            this.usercreate = data.usercreate;
            this.userupdate = data.userupdate;
            this.ip = data.ip;

        }
    }
}

Customer.prototype.option = function(id) {
    var option = $("<option>")
            .val(this.id)
            .append(this.name);
    if(typeof id !== 'undefined' && this.id == id) {
        option.attr("selected", 1);
    }

    return option;
}

Customer.prototype.tablerow = function() {
    var row = $("<tr>")
        .attr("customerid", this.id)
        .attr("actiontype", "customer")
        .attr("name", this.name)
        .attr("phone", this.phone)
        .attr("createdate", this.createdate)
        .attr("updatedate", this.updatedate)
        .append($("<td>").html(this.name))
        .append($("<td>").html(this.phone))
        .append($("<td>").html(this.createdate))
        .append($("<td>").html(
            addButton("Details", "btn-primary", "glyphicon-eye-open", "details")))
        .append($("<td>").html(
            addButton("Edit", "btn-warning", "glyphicon-pencil", "edit")))
        .append($("<td>").html(
            addButton("Delete", "btn-success", "glyphicon-trash", "delete")));
    return row;
}

Customer.prototype.delete = function() {
    $.ajax({
        type: "POST",
        data: {
            "action": "delete",
            "id": this.id
        },
        dataType: "json",
        url: "/engine/Customer.php",
        success: function (data) {
            var t = data.status=='ok'?'success':'danger';
            $.bootstrapGrowl(data.message, {
                type: t,
                delay: 2000
            });
            loadCustomers();
            $(".delete_confirm_dialog").modal('hide');
        }
    });

}

Customer.prototype.create = function(f) {
    $.ajax({
        type: "POST",
        url: "/engine/Customer.php",
        data: {
            "action": "create",
            "name": String(this.name),
            "phone": String(this.phone),
            "usercreate": String(this.usercreate),
        },
        success: f,
        dataType: 'json'
    });
}

Customer.prototype.edit = function(f) {
    $.ajax({
        type: "POST",
        url: "/engine/Customer.php",
        data: {
            "action": "edit",
            "id": parseInt(this.id),
            "name": String(this.name),
            "phone": String(this.phone),
            "userupdate": String(this.userupdate)
        },
        success: f,
        dataType: 'json'
    });
}

function Contact(data) {
    if(typeof data === 'object') {
        if(data.constructor == FormData) {
            this.id = data.getAll('contactid');
            this.name = data.getAll('contactname');
            this.position = data.getAll('contactposition');
            this.phone = data.getAll('contactphone');
            this.usercreate = data.getAll('usercreate');
            this.userupdate = data.getAll('userupdate');
            this.customerid = data.getAll('customerid');
        } else {
            this.id = data.id;
            this.name = data.name;
            this.position = data.position;
            this.phone = data.phone;
            this.createdate = data.createdate;
            this.updatedate = data.updatedate;
            this.usercreate = data.usercreate;
            this.userupdate = data.userupdate;
            this.ip = data.ip;
            this.customerid = data.customerid;
        }
    }

}

Contact.prototype.option = function(id) {
    var option = $("<option>")
            .val(this.id)
            .append(this.name);
    if(typeof id !== 'undefined' && this.id == id) {
        option.attr("selected", 1);
    }

    return option;
}

Contact.prototype.tablerow = function() {
    var row = $("<tr>")
        .attr("contactid", this.id)
        .attr("actiontype", "contact")
        .attr("name", this.name)
        .attr("phone", this.phone)
        .attr("position", this.position)
        .attr("createdate", this.createdate)
        .attr("updatedate", this.updatedate)
        .attr("customerid", this.customerid)
        .append($("<td>").html(this.name))
        .append($("<td>").html(this.position))
        .append($("<td>").html(this.phone))
        .append($("<td>").html(this.createdate))
        .append($("<td>").html(
            addButton("Edit", "btn-warning", "glyphicon-pencil", "edit")))
        .append($("<td>").html(
            addButton("Delete", "btn-success", "glyphicon-trash", "delete")));
    return row;
}


Contact.prototype.delete = function() {
    $.ajax({
        type: "POST",
        data: {
            "action": "delete",
            "id": this.id
        },
        dataType: "json",
        url: "/engine/Contact.php",
        success: function (data) {
            var t = data.status=='ok'?'success':'danger';
            $.bootstrapGrowl(data.message, {
                type: t,
                delay: 2000
            });
            loadContacts({'customerid': customerid }, fillTableContact);
            $(".delete_confirm_dialog").modal('hide');
        }
    });

}

Contact.prototype.create = function(f) {
    $.ajax({
        type: "POST",
        url: "/engine/Contact.php",
        data: {
            "action": "create",
            "name": String(this.name),
            "phone": String(this.phone),
            "position": String(this.position),
            "usercreate": String(this.usercreate),
            "customerid": parseInt(this.customerid)
        },
        success: f,
        dataType: 'json'
    });
}

Contact.prototype.edit = function(f) {
    $.ajax({
        type: "POST",
        url: "/engine/Contact.php",
        data: {
            "action": "edit",
            "id": parseInt(this.id),
            "name": String(this.name),
            "phone": String(this.phone),
            "position": String(this.position),
            "userupdate": String(this.userupdate),
            "customerid": parseInt(this.customerid)
        },
        success: f,
        dataType: 'json'
    });
}

function Project(data) {
    if(typeof data === 'object') {
        if(data.constructor == FormData) {
            this.id = data.getAll('projectid');
            this.name = data.getAll('projectname');
            this.description = data.getAll('description');
            this.total = data.getAll('total');
            this.usercreate = data.getAll('usercreate');
            this.userupdate = data.getAll('userupdate');
            this.customerid = data.getAll('customerid');
            this.opportunityid = data.getAll('opportunityid');
        } else {
            this.id = data.id;
            this.name = data.name;
            this.description = data.description;
            this.total = data.total;
            this.createdate = data.createdate;
            this.updatedate = data.updatedate;
            this.usercreate = data.usercreate;
            this.userupdate = data.userupdate;
            this.ip = data.ip;
            this.customerid = data.customerid;
            this.customername = data.customername;
            this.opportunityid = data.opportunityid;
        }
    }

}

Project.prototype.option = function(id) {
    var option = $("<option>")
            .val(this.id)
            .append(this.name);
    if(typeof id !== 'undefined' && this.id == id) {
        option.attr("selected", 1);
    }

    return option;
}

Project.prototype.tablerow = function() {
    var row = $("<tr>")
        .attr("projectid", this.id)
        .attr("actiontype", "project")
        .attr("name", this.name)
        .attr("description", this.description)
        .attr("total", this.total)
        .attr("createdate", this.createdate)
        .attr("updatedate", this.updatedate)
        .attr("customerid", this.customerid)
        .attr("customername", this.customername)
        .append($("<td>").html(this.name))
        .append($("<td>").html(this.customername))
        .append($("<td>").html("$ " + parseInt(this.total).formatMoney('2', '.', ',')))
        .append($("<td>").html(
            addButton("Edit", "btn-warning", "glyphicon-pencil", "edit")))
        .append($("<td>").html(
            addButton("Delete", "btn-success", "glyphicon-trash", "delete")));
    return row;
}


Project.prototype.delete = function() {
    $.ajax({
        type: "POST",
        data: {
            "action": "delete",
            "id": this.id
        },
        dataType: "json",
        url: "/engine/Project.php",
        success: function (data) {
            var t = data.status=='ok'?'success':'danger';
            $.bootstrapGrowl(data.message, {
                type: t,
                delay: 2000
            });
            loadProjects({'customerid': customerid }, fillTableProject);
            $(".delete_confirm_dialog").modal('hide');
        }
    });

}

Project.prototype.create = function(f) {
    $.ajax({
        type: "POST",
        url: "/engine/Project.php",
        data: {
            "action": "create",
            "name": String(this.name),
            "description": String(this.description),
            "usercreate": parseInt(this.usercreate),
            "customerid": parseInt(this.customerid),
            "opportunityid": String(this.opportunityid)
        },
        success: f,
        dataType: 'json'
    });
}

Project.prototype.edit = function(f) {
    $.ajax({
        type: "POST",
        url: "/engine/Project.php",
        data: {
            "action": "edit",
            "id": parseInt(this.id),
            "name": String(this.name),
            "description": String(this.description),
            "total": parseInt(this.total),
            "userupdate": parseInt(this.userupdate),
            "customerid": parseInt(this.customerid),
            "opportunityid": String(this.opportunityid)
        },
        success: f,
        dataType: 'json'
    });
}



function Material(data) {
    if(typeof data === 'object') {
        if(data.constructor == FormData) {
            this.id = data.getAll('materialid');
            this.sku = data.getAll('sku');
            this.description = data.getAll('description');
            this.quantity = data.getAll('quantity');
            this.price = data.getAll('price');
            this.projectid = data.getAll('projectid');
            this.usercreate = data.getAll('usercreate');
            this.userupdate = data.getAll('userupdate');
            this.customerid = data.getAll('customerid');
        } else {
            this.id = data.id;
            this.sku = data.sku;
            this.description = data.description;
            this.quantity = data.quantity;
            this.price = data.price;
            this.projectid = data.projectid;
            this.createdate = data.createdate;
            this.updatedate = data.updatedate;
            this.usercreate = data.usercreate;
            this.userupdate = data.userupdate;
        }
    }

}

Material.prototype.option = function(id) {
    var option = $("<option>")
            .val(this.id)
            .append(this.name);
    if(typeof id !== 'undefined' && this.id == id) {
        option.attr("selected", 1);
    }

    return option;
}

Material.prototype.tablerow = function() {
    var linetotal = this.quantity * this.price;
    var row = $("<tr>")
        .attr("materialid", this.id)
        .attr("actiontype", "material")
        .attr("sku", this.sku)
        .attr("description", this.description)
        .attr("quantity", this.quantity)
        .attr("price", this.price)
        .attr("projectid", this.projectid)
        .attr("createdate", this.createdate)
        .attr("updatedate", this.updatedate)
        .append($("<td>").html(this.sku))
        .append($("<td>").html(this.description))
        .append($("<td>").html(this.quantity))
        .append($("<td>").html("$ " + parseInt(this.price).formatMoney('2', '.', ',')))
        .append($("<td>").html("$ " + parseInt(linetotal).formatMoney('2', '.', ',')))
        .append($("<td>").html(
            addButton("Edit", "btn-warning", "glyphicon-pencil", "edit")))
        .append($("<td>").html(
            addButton("Delete", "btn-success", "glyphicon-trash", "delete")));
    return row;
}


Material.prototype.delete = function() {
    $.ajax({
        type: "POST",
        data: {
            "action": "delete",
            "id": this.id
        },
        dataType: "json",
        url: "/engine/Material.php",
        success: function (data) {
            var t = data.status=='ok'?'success':'danger';
            $.bootstrapGrowl(data.message, {
                type: t,
                delay: 2000
            });
            loadMaterials({'projectid': project_id }, fillTableMaterial);
            $(".delete_confirm_dialog").modal('hide');
        }
    });

}

Material.prototype.create = function(f) {
    $.ajax({
        type: "POST",
        url: "/engine/Material.php",
        data: {
            "action": "create",
            "sku": String(this.sku),
            "description": String(this.description),
            "quantity": parseInt(this.quantity),
            "price": parseInt(this.price),
            "usercreate": parseInt(this.usercreate),
            "projectid": parseInt(this.projectid)
        },
        success: f,
        dataType: 'json'
    });
}

Material.prototype.edit = function(f) {
    $.ajax({
        type: "POST",
        url: "/engine/Material.php",
        data: {
            "action": "edit",
            "id": parseInt(this.id),
            "sku": String(this.sku),
            "description": String(this.description),
            "quantity": parseInt(this.quantity),
            "price": parseInt(this.price),
            "userupdate": parseInt(this.userupdate),
            "projectid": parseInt(this.projectid)
        },
        success: f,
        dataType: 'json'
    });
}



function deleteCustomer(e) {
    var m = $(e.target);
    var id = m.attr("customerid");
    allCustomers[id].delete();
    m.attr("customerid", 0);
    $(".delete_confirm_dialog").modal('hide');
    loadCustomers(fillTableCustomer);
}


function deleteProject(e) {
    var m = $(e.target);
    var id = m.attr("projectid");
    allProjects[id].delete();
    m.attr("projectid", 0);
    $(".delete_confirm_dialog").modal('hide');
    loadProjects({}, fillTableProject);
}

function loadCustomers(f, d) {
    $.ajax({
        type: "GET",
        dataType: "json",
        url: "../../engine/GetCustomer.php",
        success: function(data) {
            allCustomers = {};
            $.each(data.Customers, function (i, c) {
                var customer = new Customer(c)
                allCustomers[customer.id] = (customer);
            });
            if(typeof f !== 'undefined') {
                f(d);
            }
        }
    });
}

function loadContacts(d, f) {
    $.ajax({
        type: "GET",
        data: d,
        dataType: "json",
        url: "../../engine/GetContact.php",
        success: function(data) {
            allContacts = {};
            $.each(data.Contacts, function (i, c) {
                var contact = new Contact(c)
                allContacts[contact.id] = contact;
            });
            if(typeof f !== 'undefined') {
                f();
            }
        }
    });
}

function loadProjects(d, f) {
    $.ajax({
        type: "GET",
        data: d,
        dataType: "json",
        url: "../../engine/GetProject.php",
        success: function(data) {
            allProjects = {};
            $.each(data.projects, function (i, c) {
                var project = new Project(c)
                allProjects[project.id] = project;
            });
            if(typeof f !== 'undefined') {
                f();
            }
        }
    });
}

function loadMaterials(d, f) {
    $.ajax({
        type: "GET",
        data: d,
        dataType: "json",
        url: "../../engine/GetMaterial.php",
        success: function(data) {
            allMaterials = {};
            $.each(data.Materials, function (i, m) {
                var material = new Material(m)
                allMaterials[material.id] = material;
            });
            if(typeof f !== 'undefined') {
                f();
            }
        }
    });
}

function fillTableCustomer() {
    $("#customerlist").html("");
    $.each(allCustomers, function (i, c) {
        $("#customerlist").append(c.tablerow());
    });

}

function fillSelectCustomer(d) {
    $("#customerid").html($("<option>")
        .val(0)
        .html("Select a Customer"));
    $.each(allCustomers, function (i, c) {
        var o = c.option(customer_id);
        if(c.id == d) o.attr("selected", 1);
        $("#customerid").append(o);
    });
    $("#customerid").append($("<option>")
        .val("new")
        .html("New Customer"));
}

function fillTableContact() {
    $("#contactlist").html("");
    $.each(allContacts, function (i, c) {
        $("#contactlist").append(c.tablerow());
    });

}

function fillTableProject() {

    $("#projectlist").html("");
    $.each(allProjects, function (i, c) {
        $("#projectlist").append(c.tablerow());
    });
    if($("#projectlist").html() == "") {
        $("#projectlist").html( $("<td>")
            .attr("colspan", "6")
            .append($("<p>")
                .addClass("text-danger")
                .attr("id", "nofiles")
                .html("No Quotes yet.")));
    }

}

function fillTableMaterial() {

    $("#materiallist").html("");
    var projecttotal = 0;
    $.each(allMaterials, function (i, c) {
        projecttotal += c.price * c.quantity;
        $("#materiallist").append(c.tablerow());
    });
    if($("#materiallist").html() == "") {
        $("#materiallist").html( $("<td>")
            .attr("colspan", "7")
            .append($("<p>")
                .addClass("text-danger")
                .attr("id", "nofiles")
                .html("No Lines yet.")));
    }
    $("#totalval").html("$ " + (projecttotal).formatMoney(2, '.', ','));
    $("#total").val(projecttotal);

}

function runAction(e) {
    var t = $(e.target);
    var row = t.closest("tr");
    var actiontype = row.attr("actiontype")
    var action = t.closest("a").attr("action");
    if(actiontype == "line") {
        var id = row.attr("lineid");
        if(action == "edit") {
            $(".modal-materials").modal();
            $("#linesModal").html("Edit Line");
            $("#lineid").val(row.attr("lineid"));
            $("#sku").val(row.attr("sku"));
            $("#linedescription").html(row.attr("description"));
            $("#quantity").val(row.attr("quantity"));
            $("#unitprice").val(row.attr("unitprice"));
        } else if(action == "delete") {
            deleteLine(id);
        }
    } else if(actiontype == "project") {
        var id = row.attr("projectid");
        if(action == "details") {
            document.location = "../Project/details.php?projectid=" + id;
        } else if(action == "edit") {
            document.location = "../Project/edit.php?projectid=" + id;
        } else if(action == "delete") {
            $("#deleteProject").attr("projectid", id);
            $(".delete_confirm_dialog").modal();
        }
    } else if(actiontype == 'customer') {
        var id = row.attr("customerid");
        if(action == "details") {
            document.location = "../Customer/details.php?customerid=" + id;
        } else if(action == "edit") {
            document.location = "../Customer/edit.php?customerid=" + id;
        } else if(action == "delete") {
            $("#deleteCustomer").attr("customerid", id);
            $(".delete_confirm_dialog").modal();
        }
    } else if(actiontype == 'contact') {
        var id = row.attr("contactid");
        if(action == "details") {
            alert("detail" + id);
        } else if(action == "edit") {
            $("#modal-contact-action").val("edit");
            $("#contactid").val(id);
            $("#contactname").val(allContacts[id].name);
            $("#contactposition").val(allContacts[id].position);
            $("#contactphone").val(allContacts[id].phone);
            $(".modal-contact").modal();
        } else if(action == "delete") {
            allContacts[id].delete();
        }
    } else if(actiontype == 'material') {
        var id = row.attr("materialid");
        if(action == "details") {
            alert("detail" + id);
        } else if(action == "edit") {
            $("#modal-material-action").val("edit");
            $("#materialid").val(id);
            $("#sku").val(allMaterials[id].sku);
            $("#materialdescription").val(allMaterials[id].description);
            $("#quantity").val(allMaterials[id].quantity);
            $("#price").val(allMaterials[id].price);
            $(".modal-material").modal();
        } else if(action == "delete") {
            allMaterials[id].delete();
        }
    }
}
function addButton(title, btntype, glyphicon, action) {
    return $("<p>")
        .attr("data-placement", "top")
        .attr("title", title)
        .append($("<a>")
            .attr("href", "#")
            .attr("role", "button")
            .attr("action", action)
            .addClass("btn")
            .addClass(btntype)
            .addClass("btn-xs")
            .addClass(action)
            .click(runAction)
            .append($("<span>")
                .addClass("glyphicon")
                .addClass(glyphicon)
                )
            );
}

function addLine() {
    $("#modal-material-action").val("create");
    $(".modal-material").modal();
    $("#linesModal").html("Add Line");
    $("#materialid").val("");
    $("#sku").val("");
    $("#materialdescription").html("");
    $("#quantity").val("");
    $("#price").val("");
}


Number.prototype.formatMoney = function(c, d, t){
var n = this, 
    c = isNaN(c = Math.abs(c)) ? 2 : c, 
    d = d == undefined ? "." : d, 
    t = t == undefined ? "," : t, 
    s = n < 0 ? "-" : "", 
    i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))), 
    j = (j = i.length) > 3 ? j % 3 : 0;
   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
 };
