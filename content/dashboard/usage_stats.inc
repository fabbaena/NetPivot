<div class="col-xs-12 col-md-10 content">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4>Usage Statistics</h4>
        </div>
        <div class="panel-body">
            <div class="container-fluid">
            <div class="row">
                <div class="col-md-3">
                    <div class="dropdown">
                      <button class="btn btn-secondary dropdown-toggle" type="button" id="lastx" data-toggle="dropdown">
                        Last 2 Days
                        <span class="caret"></span></button>
                      </button>
                      <ul class="dropdown-menu" id="lastx_items">
                        <li><a id="l2d" class="dropdown-item" href="#" sdays="2">Last 2 Days</a></li>
                        <li><a id="lw" class="dropdown-item" href="#" sdays="7">Last Week</a></li>
                        <li><a id="lm" class="dropdown-item" href="#" sdays="31">Last Month</a></li>
                        <li><a id="ly" class="dropdown-item" href="#" sdays="365">Last Year</a></li>
                      </ul>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="dropdown">
                      <button class="btn btn-secondary dropdown-toggle" type="button" id="etype" data-toggle="dropdown">
                        Logins
                        <span class="caret"></span></button>
                      </button>
                      <ul class="dropdown-menu" id="etype_items">
                        <li><a id="l2d" class="dropdown-item" href="#" event_type="1">Logins</a></li>
                        <li><a id="lw" class="dropdown-item" href="#" event_type="7">Conversions</a></li>
                      </ul>
                    </div>
                </div>
            </div>
            </div>
            <hr>
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>User Name</th>
                    <th>Company</th>
                    <th>Event Count</th>
                </tr>
                </thead>
                <tbody id="usage_stats">
                </tbody>
            </table>
            <div id="compare"></div>
        </div>
    </div>
</div>
