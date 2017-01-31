
<div id="reprocessModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Reprocess</h4>
            </div>
            <div class="modal-body">
                <form id="form" role="form" data-toggle="validator">
                    <div class="container-fluid" id="rep">
                        This process will eliminate old statistics and will replace with new ones. 
                        If you are sure that you want to continue press "Reprocess".<br>
                        <span class="btn btn-success fileinput-button" id="start_rep">Reprocess</span>
                        <div>
                            <ul class="list-group" id="lr">
                            </ul>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" id="close_rep">Close</button>
            </div>
        </div>

    </div>
</div>
