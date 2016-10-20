<div class="panel panel-default">
    <div class="panel-heading">
        <h4>Converted Files</h4>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
        <table class="table table-bordred table-striped" data-toggle="bootgrid">
            <thead>
            <tr class="active">
                <th>File Name</th>                            
                <th>Options</th>
                <th>Upload Date</th>
                
            </tr>
            </thead>
            <tbody>
            <?php if ($filelist->count > 0) { ?>
                <?php foreach ($filelist->files as &$f) { ?>
                    <tr uuid="<?= $f->uuid ?>">
                        <td style="width: 55%"><?= $f->filename ?></td>
                        <td style="width: 20%">
                            <a href="content.php?uuid=<?= $f->uuid ?>">
                                <span class="glyphicon glyphicon-sunglasses" aria-hidden="true"></span>
                            </a>&nbsp;
                            <a href="rename.php?file=<?= $f->uuid ?>">
                                <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                            </a>&nbsp;
                            <a href="../engine/reprocess.php?file=<?= $f->uuid ?>"><span class="glyphicon glyphicon-retweet" aria-hidden="true"></span></a>
                        </td>
                        <td style="width: 25%"><?= explode(" ", $f->upload_time)[0] ?></td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <p class="text-danger">No files uploaded yet.</p>
            <?php } ?>
            </tbody>
        </table>
        </div>
    </div>
</div>