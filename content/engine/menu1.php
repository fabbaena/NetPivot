
<nav class="navbar navbar-inverse navbar-static-top">
    <div class="container-fluid">
		<div class="navbar-header">
		</div>
        <div class="navbar-header">
            <a class="np-navbar-brand" href="../dashboard/index.php">
                <img src="../images/netpivot_web-logo-small.png">
            </a>
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#account_options" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div> 
        <div class="collapse navbar-collapse" id="account_options">       
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" >
                    <?= isset($user)?$user->firstname:"&nbsp;" ?><span class="caret"></span>
                    </a>
                <ul class="dropdown-menu">
                <?php if(isset($user)) foreach($user->roles as $r)  { ?>
                    <li><a href="../<?= $r->starturl ?>"><?= $r->name ?></a></li>
                <?php } ?>
                </ul>
                </li>
                <?php if(isset($user)) { ?>
                <li><a href="../engine/endsession.php">Log Out</a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
</nav>
