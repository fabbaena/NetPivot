<div class="col-xs-12 col-md-10 content">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4>Event Log</h4>
        </div>
        <div class="panel-body">
            <div class="container-fluid">
            <div class="row">
                <div class="col-md-1">
                    <div class="input-group">
                      <button id="filter" type="button" class="btn btn-default">Filter</button>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                      <span class="input-group-addon" id="oldest_timestamp">Date Start</span>
                      <input type="text" class="form-control" placeholder="YYYY-MM-DD" aria-describedby="oldest_timestamp" id="oldest_timestamp_picker">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                      <span class="input-group-addon" id="newest_timestamp">Date End</span>
                      <input type="text" class="form-control" placeholder="YYYY-MM-DD" aria-describedby="newest_timestamp"id="newest_timestamp_picker">
                    </div>
                </div>
                <div class="col-md-5">
                    <?php if($admin) { ?>
                    <div class="input-group ui-widget">
                      <span class="input-group-addon" id="user_name">User Name</span>
                      <input type="text" id="user_name_input" class="form-control" placeholder="xxx@xxx.xxx" aria-describedby="user_name" value="<?= $username ?>">
                      <input type="hidden" id="user_id" value="0">
                    </div>
                    <?php } ?>
                </div>
            </div>
            </div>
            <hr>
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Time</th>
                    <th>User Name</th>
                    <th>Company</th>
                    <th>Event</th>
                </tr>
                </thead>
                <tbody id="event_logs">
                </tbody>
            </table>
            <div id="compare"></div>
        </div>
    </div>
</div>
