
<nav class="navbar navbar-inverse navbar-static-top">
    <div class="container-fluid">
		<div class="navbar-header">
            <a class="np-navbar-brand" href="../dashboard/index.php">
                <img src="../images/netpivot_web-logo-small.png">
            </a>
		</div>
		<div class="collapse navbar-collapse">      
            <ul class="nav navbar-nav navbar-right" id="account_options">
                <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" ><?= isset($usuario)?$usuario:"&nbsp;" ?><span class="caret"></span></a>
                <ul class="dropdown-menu">
                <?php if(isset($roles) && isset($roles[1])) { ?>
                    <li><a href="../admin">System Admin</a></li>
                <?php } ?>
                <?php if(isset($roles) && isset($roles[2])) { ?>
                    <li><a href="../sales">Sales</a></li>
                <?php } ?>
                <?php if(isset($roles) && isset($roles[3])) { ?>
                    <li><a href="../dashboard">Engineering</a></li>
                <?php } ?>
                </ul>
                </li>
            	<li><a href="../model/EndSession.php">Log Out</a></li>
            </ul>
		</div>
    </div>
</nav>
