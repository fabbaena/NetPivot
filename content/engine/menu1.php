

<nav class="navbar navbar-default navbar-static-top">
    <div class="container-fluid">
		<!-- Brand and toggle get grouped for better mobile display -->
	<div class="navbar-header ">
            
            <a id="titulo" class="navbar-brand" href="../dashboard/index.php">
                <img src="../images/netpivot-logo-basic-01.png" >
            </a>
	</div>
		<!-- Collect the nav links, forms, and other content for toggling -->
	<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">      

            <ul class="nav navbar-nav navbar-right">
		
                
               
                        <?php
			  if ($user_type == 'Administrator'){
                              echo '<li class="dropdown ">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Account<span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">';
                              
                              
                              echo '<li class=""><a href="settings.php">Settings</a></li>';
                              echo '<li class=""><a href="admin_users.php">Users</a></li>';
                              echo '<li class="divider"></li>
                        <li><a href="../model/EndSession.php">Log Out</a></li>
                    </ul>
		</li>';
                          } else {
                              echo '<li><a href="../model/EndSession.php">Log Out</a></li>';
                          }
                        ?>
			
			
            </ul>
	</div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>  	
<div class="container-fluid main-container">
  <div class="col-md-2 sidebar">
  	<div class="row">
	<!-- uncomment code for absolute positioning tweek see top comment in css -->
            <div class="absolute-wrapper"> </div>
	<!-- Menu
            <div class="side-menu">
		<nav class="navbar navbar-default" role="navigation">
			<!-- Main Menu -->
                   <!-- <div class="side-menu-container">
			<ul class="nav navbar-nav"> -->