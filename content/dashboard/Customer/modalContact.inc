<div class="modal fade modal-contact" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button id="contactclose" type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button> 
                <h4 class="modal-title" id="mySmallModalLabel">Contact</h4>
            </div>
            <div class="modal-body">
                <form id="form-contact" role="form" action="" data-toggle="validator">
                    <input type="hidden" id="modal-contact-action" name="action">
                    <input type="hidden" id="contactid" name="contactid">
                    <div class="form-group has-feedback">
                        <input type="text" class="form-control" name="contactname" id="contactname" placeholder="Contact Name" />
                    </div>
                    <div class="form-group has-feedback">
                        <input type="text" class="form-control" name="contactposition" id="contactposition" placeholder="Position"/>
                    </div>
                    <div class="form-group has-feedback">
                        <input type="phone" class="form-control" name="contactphone" id="contactphone" placeholder="Phone"/>
                    </div>
                    <div class="form-group ">                                            
                        <button type="submit" class="btn btn-success ">Save</button>                                          
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script language="javascript">

function returnConctactAction(data) {
    $.bootstrapGrowl(data.message, {
        type: data.status=='ok'?'success':'danger',
        delay: 2000,
    });
    loadContacts({'customerid': customerid }, fillTableContact);
    if(data.status=='ok') {
        $(".modal-contact").modal('hide');
    }

}
$().ready(function () {
    $('#form-contact').validator().on('submit', function (e) {
        if (e.isDefaultPrevented()) {
            alert('There are errors in the form. Please review the data entered.');
        } else {
            var c = new Contact(new FormData(document.getElementById("form-contact")));
            c.customerid = customerid;
            c.usercreate = userid;
            c.userupdate = userid;
            if($("#modal-contact-action").val() == "create") {
                c.create(returnConctactAction);
            } else if($("#modal-contact-action").val() == "edit") {
                c.edit(returnConctactAction);
            }
        }
        return false;
    });
});
</script>