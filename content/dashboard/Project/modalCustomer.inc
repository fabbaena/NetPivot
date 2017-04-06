<div class="modal  fade modal-customer" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header"> 
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="mySmallModalLabel">New Customer</h4> 
            </div>
            <div class="modal-body"> 
                <form id="form-customer" role="form-customer" action="" data-toggle="validator">
                    <input type="hidden" name="action" value="create">
                    <input type="hidden" name="usercreate" value="<?php echo $id ?>" />
                    <div class="form-group has-feedback">
                        <label class="control-label" for="CustomerName">Name:</label>
                        <input class="form-control" id="CustomerName" type="text" name="CustomerName"  pattern="^[.A-z0-9 ']{1,30}$" data-pattern-error="Please use only aphanumeric, apostrophe or space characters only. Max is 30 characters." required>
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group has-feedback">
                        <label class="control-label" for="Phone">Phone:</label>
                        <input class="form-control" id="Phone" type="text" name="Phone" placeholder="222-22-22 ext 123" pattern="^[.A-z0-9 ']{1,30}$" data-pattern-error="Please use only aphanumeric, apostrophe or space characters only. Max is 30 characters." required>
                        <div class="help-block with-errors"></div>
                    </div>

                    <br>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div> 
<script language="javascript">

function returnCustomerAction(data) {
    $.bootstrapGrowl(data.message, {
        type: data.status=='ok'?'success':'danger',
        delay: 2000,
    });
    loadCustomers(fillSelectCustomer, data.idInsert);
    $(".modal-customer").modal('hide');

}
$().ready(function () {
    $('#form-customer').validator().on('submit', function (e) {
        if (e.isDefaultPrevented()) {
            alert('There are errors in the form. Please review the data entered.');
        } else {
            var c = new Customer(new FormData(document.getElementById("form-customer")));
            c.usercreate = userid;
            c.userupdate = userid;
            c.create(returnCustomerAction);
        }
        return false;
    });
});


</script>