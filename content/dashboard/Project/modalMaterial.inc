<div class="modal  fade modal-material" tabindex="-1" role="dialog" aria-labelledby="linesModal">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header"> 
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="linesModal"></h4> 
            </div>
            <div class="modal-body"> 
                <form id="form-material" role="form-material" action="" data-toggle="validator">
                    <input type="hidden" id="modal-material-action" name="action">
                    <input type="hidden" id="materialid" name="materialid" />
                    <div class="form-group has-feedback">
                        <label class="control-label" for="sku">Sku:</label>
                        <input class="form-control" id="sku" type="text" name="sku"  pattern="^[.A-z0-9 ']{1,30}$" data-pattern-error="Please use only aphanumeric, apostrophe or space characters only. Max is 30 characters." required>
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group has-feedback">
                        <label class="control-label" for="description">Description:</label>
                        <textarea class="form-control" id="materialdescription" type="text" name="description"  pattern="^[.A-z0-9 ']{1,30}$" data-pattern-error="Please use only aphanumeric, apostrophe or space characters only. Max is 30 characters."  required></textarea>
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group has-feedback">
                        <label class="control-label" for="quantity">Quantity:</label>
                        <input class="form-control" id="quantity" type="text" name="quantity"  pattern="^[0-9]{1,5}$" data-pattern-error="Please use only numeric characters only. Max is 5 characters." required>
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group has-feedback">
                        <label class="control-label" for="unitprice">Price:</label>
                        <input class="form-control" id="price" type="text" name="price"  pattern="^[0-9]{1,10}$" data-pattern-error="Please use only numeric characters only. Max is 10 characters." required>
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
function returnMaterialAction(data) {
    $.bootstrapGrowl(data.message, {
        type: data.status=='ok'?'success':'danger',
        delay: 2000,
    });
    loadMaterials({'projectid': project_id }, fillTableMaterial);
    if(data.status=='ok') {
        $(".modal-material").modal('hide');
    }

}
$().ready(function () {
    $('#form-material').validator().on('submit', function (e) {
        if (e.isDefaultPrevented()) {
            alert('There are errors in the form. Please review the data entered.');
        } else {
            var c = new Material(new FormData(document.getElementById("form-material")));
            c.projectid = project_id;
            c.usercreate = user_id;
            c.userupdate = user_id;
            if($("#modal-material-action").val() == "create") {
                c.create(returnMaterialAction);
            } else if($("#modal-material-action").val() == "edit") {
                c.edit(returnMaterialAction);
            }
        }
        return false;
    });
});
</script>