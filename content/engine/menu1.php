
<nav class="navbar navbar-inverse navbar-static-top">
    <div class="container-fluid">
		<div class="navbar-header">
            <a class="np-navbar-brand" href="../dashboard/index.php">
                <img src="../images/netpivot_web-logo-small.png">
            </a>
		</div>
		<div class="collapse navbar-collapse">      
            <ul class="nav navbar-nav navbar-right" id="account_options">
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" >
                    <?= isset($user)?$user->name:"&nbsp;" ?><span class="caret"></span>
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
