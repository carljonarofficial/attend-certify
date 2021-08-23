<!-- Navbar -->
        <div class="container-fluid mb-5">
            <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
                <!-- Logo -->
                <a class="navbar-brand" href="#" style="margin-right: 0.5rem;">
                    <img src="img/logo.svg" alt="Logo" width="50" onContextMenu="return false;"  ondragstart="return false;">
                </a>
                <!-- Button for Collapsible Navbar -->
                <button id="navbar-button" class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
                    <span id="navbar-button-icon" class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="collapsibleNavbar">
                    <div class="justify-content-center text-dark mt-2">
                        <img src="img/logo_text.svg" alt="ATTEND and CERTIFY" height="32.5" onContextMenu="return false;" ondragstart="return false;">
                        <!-- <h3 class="font-weight-bold">ATTEND and CERTIFY</h3> -->
                    </div>
                    <ul class="navbar-nav ml-auto mt-2">
                        <li class="nav-item h5 font-weight-bold mx-1 <?php if($activePage == 'home'){ echo 'active';}?>">
                            <a class="nav-link p-2 <?php if($activePage == 'home'){ echo 'text-white';} else { echo 'text-dark';} ?>" href="home.php"><i class="fa fa-home"></i> Home</a>
                        </li>
                        <li class="nav-item h5 font-weight-bold mx-1 <?php if($activePage == 'events'){ echo 'active';}?>">
                            <a class="nav-link p-2 <?php if($activePage == 'events'){ echo 'text-white';} else { echo 'text-dark';} ?>" href="events.php"><i class="fa fa-calendar"></i> Events</a>
                        </li>
                        <li class="nav-item h5 font-weight-bold mx-1 <?php if($activePage == 'certificates'){ echo 'active';}?>">
                            <a class="nav-link p-2 <?php if($activePage == 'certificates'){ echo 'text-white';} else { echo 'text-dark';} ?>" href="certificates.php"><i class="fa fa-certificate"></i> Certificates</a>
                        </li>
                        <li class="nav-item h5 font-weight-bold mx-1 <?php if($activePage == 'about'){ echo 'active';}?>">
                            <a class="nav-link p-2 <?php if($activePage == 'about'){ echo 'text-white';} else { echo 'text-dark';} ?>" href="about.php"><i class="fa fa-info-circle"></i> About</a>
                        </li>
                        <li class="nav-item dropdown h4 mx-1">
                            <a class="nav-link p-2 text-dark dropdown-toggle" href="" id="navbarAccount" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-user-circle"></i></a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarAccount">
                                <h5 class="ml-2">Hi, <?php echo $username; ?>!</h5>
                                <a class="dropdown-item" href="">Settings</a>
                                <a class="dropdown-item" href="logout.php">Logout</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>