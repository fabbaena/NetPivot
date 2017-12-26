
<div id="fileUploadModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">File Upload</h4>
            </div>
            <div class="modal-body">
                <form id="form" role="form" data-toggle="validator">
                    <input id="projectid" type="hidden" name="projectid">
                    <div class="container-fluid" id="fu">
                        <span class="btn btn-success fileinput-button">
                            <span>Add file...</span>
                            <input id="fileupload" type="file" name="InputFile">
                        </span>
                        <div id="progress" class="progress">
                            <div class="progress-bar progress-bar-success"></div>
                        </div>
                        <div>
                            <ul class="list-group" id="lp">
                            </ul>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>
