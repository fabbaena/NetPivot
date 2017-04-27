
<div id="fileUploadModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">File Upload</h4>
            </div>
            <div class="modal-body">
                <div id="customer" class="container-fluid">
                    Please choose the Customer associated with this conversion:
                    <div class="dropdown">
                        <button id="clistLabel" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Select a Customer<span class="caret"></span></button>
                        <ul id="customers" class="dropdown-menu" aria-labelledby="clistLabel">
                            <li><a href="#">hello1</a></li>
                        </ul>
                    </div>
                </div>
                <div class="hidden container-fluid" id="project">
                    Please choose the Quote associated with this conversion:
                    <div class="dropdown">
                        <button id="qlistLabel" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Select a Quote<span class="caret"></span></button>
                        <ul id="quotes" class="dropdown-menu" aria-labelledby="qlistLabel">
                            <li><a href="#">hello1</a></li>
                        </ul>
                    </div>
                </div>

                <br>
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
