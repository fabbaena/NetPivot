<div class="panel panel-default">
    <div class="panel-heading">
        <h4>Converted Files (<?= $v ?>)</h4>
    </div>
    <div class="panel-body">
        <button type="button" class="btn btn-success" id="btn_fileupload" >Add Files</button>
        <div class="table-responsive">
        <table class="table table-striped" data-toggle="bootgrid">
            <thead>
            <tr class="active">
                <th>File Name</th>
                <th>Upload Date</th>
                <th>Options</th>
                
            </tr>
            </thead>
            <tbody id="fl">
                <p class="text-danger" id="nofiles">No files uploaded yet.</p>
            </tbody>
        </table>
        </div>
    </div>
</div>