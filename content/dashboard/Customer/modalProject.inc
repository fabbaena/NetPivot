<div class="modal fade modal-project" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button id="contactclose" type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button> 
                <h4 class="modal-title" id="mySmallModalLabel">Quote</h4>
            </div>
            <div class="modal-body">
                <form id="form-project" role="form" action="" data-toggle="validator">
                    <input type="hidden" id="modal-contact-action" name="action" value="create">
                    <input type="hidden" id="contactid" name="contactid">
                    <div class="form-group has-feedback">
                        <input type="text" class="form-control" name="projectname" id="projectname" placeholder="Quote Name" />
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

function returnProjectAction(data) {
    if(data.status=="error") {
        $.bootstrapGrowl(data.message, {
            type: 'danger',
            delay: 2000,
        });
        return;
    }
    document.location = "../Project/edit.php?projectid=" + data.idInsert;
}
$().ready(function () {
    $('#form-project').validator().on('submit', function (e) {
        if (e.isDefaultPrevented()) {
            alert('There are errors in the form. Please review the data entered.');
        } else {
            var c = new Project(new FormData(document.getElementById("form-project")));
            c.customerid = customerid;
            c.usercreate = userid;
            c.userupdate = userid;
            c.create(returnProjectAction);
        }
        return false;
    });
});
</script>