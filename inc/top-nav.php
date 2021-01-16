<!-- Navigation Bar-->
        <header id="topnav">
            <!-- Topbar Start -->
            <div class="navbar-custom">
                <div class="container-fluid">
                    <ul class="list-unstyled topnav-menu float-right mb-0">
                        <li class="dropdown notification-list">
                            <a class="nav-link dropdown-toggle nav-user mr-0 waves-effect waves-light" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                <span class="pro-user-name ml-1">
                                    Theme Changer
                                </span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right profile-dropdown ">
                              <!-- theme-->
                              <a href="inc/backend/user/auth/setTheme.php?q=default" class="dropdown-item notify-item">
                                <i class="ti-spray m-r-5"></i> Default
                              </a>
                              <!-- theme-->
                              <a href="inc/backend/user/auth/setTheme.php?q=discord" class="dropdown-item notify-item">
                                <i class="ti-spray m-r-5"></i> Discord
                              </a>
                              <!-- theme-->
                              <a href="inc/backend/user/auth/setTheme.php?q=material" class="dropdown-item notify-item">
                                <i class="ti-spray m-r-5"></i> Material
                              </a>
                              <!-- theme-->
                              <a href="inc/backend/user/auth/setTheme.php?q=blue-light" class="dropdown-item notify-item">
                                <i class="ti-spray m-r-5"></i> Blue (Light)
                              </a>
                              <!-- theme-->
                              <a href="inc/backend/user/auth/setTheme.php?q=green-light" class="dropdown-item notify-item">
                                <i class="ti-spray m-r-5"></i> Green (Light)
                              </a>
                              <!-- theme-->
                              <a href="inc/backend/user/auth/setTheme.php?q=red-light" class="dropdown-item notify-item">
                                <i class="ti-spray m-r-5"></i> Red (Light)
                              </a>
                              <!-- theme-->
                              <a href="inc/backend/user/auth/setTheme.php?q=multi-light" class="dropdown-item notify-item">
                                <i class="ti-spray m-r-5"></i> Multi (Light)
                              </a>
                            </div>
                        </li>

                        <li class="dropdown notification-list">
                            <a class="nav-link dropdown-toggle nav-user mr-0 waves-effect waves-light" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                <?php
                                if (empty($user['avatar'])) {
                                    echo '<img src="assets/images/users/placeholder.png" alt="user-image" class="rounded-circle">';
                                } else {
                                    echo '<img src="'. $user['avatar'] .'" alt="user-image" class="rounded-circle">';
                                }
                                ?>
                                <span class="pro-user-name ml-1">
                                    <?php echo $user['username']; ?> <i class="mdi mdi-chevron-down"></i>
                                </span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right profile-dropdown ">
                              <!-- item-->
                              <a href="<?php echo $url['settings']; ?>" class="dropdown-item notify-item">
                                <i class="ti-settings m-r-5"></i> Settings
                              </a>
                              <!-- item-->
                              <a href="<?php echo $url['user-identities']; ?>" class="dropdown-item notify-item">
                                <i class="fa fa-id-card-o m-r-5"></i> My Identities
                              </a>
                              <!-- item-->
                              <a href="<?php echo $url['logout']; ?>" class="dropdown-item notify-item">
                                <i class="ti-power-off m-r-5"></i> Logout
                              </a>


                            </div>
                        </li>

                        <li class="dropdown notification-list">
                            <a href="javascript:void(0);" class="nav-link right-bar-toggle waves-effect waves-light">
                                <i class="fe-settings noti-icon"></i>
                            </a>
                        </li>

                    </ul>

                    <!-- LOGO -->
                    <div class="logo-box">
                        <a href="index.php" class="logo text-center">
                            <span class="logo-lg">
                                <!-- <img src="assets/images/logo-light.png" alt="" height="20"> -->
                                <span class="logo-lg-text-light"><?php echo $settings['name']; ?></span>
                            </span>
                            <span class="logo-sm">
                                <span class="logo-sm-text-dark"><?php echo $settings['name']; ?></span>
                                <!-- <img src="assets/images/logo-sm.png" alt="" height="24"> -->
                            </span>
                        </a>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <!-- end Topbar -->

            <div class="topbar-menu">
                <div class="container-fluid">
                    <div id="navigation">
                        <!-- Navigation Menu-->
                        <ul class="navigation-menu">
                          <li class="has-submenu">
                            <a href="<?php echo $url['index']; ?>"><i class="mdi mdi-home"></i> <span> Home </span> </a>
                          </li>
                          <li class="has-submenu">
                              <a href="<?php echo $url['civilian']; ?>?v=nosession"><i class="mdi mdi-contacts"></i> <span> Civilian </span> </a>
                          </li>
                          <li class="has-submenu">
                              <a href="<?php echo $url['leo']; ?>?v=nosession"><i class="mdi mdi-pistol"></i> <span> Law Enforcement </span> </a>
                          </li>
                          <li class="has-submenu">
                              <a href="fire.php?v=nosession"><i class="mdi mdi-heart-pulse"></i> <span> Fire / EMS </span> </a>
                          </li>
                          <li class="has-submenu">
                              <a href="dispatch.php?v=nosession"><i class="mdi mdi-phone-in-talk"></i> <span> Dispatch </span> </a>
                          </li>
                          <?php if (staff_access === 'true'): ?>
                            <li class="has-submenu">
                                <a href="staff.php"><i class="mdi mdi-lock"></i> <span> Staff </span> </a>
                            </li>
                          <?php endif; ?>

                        </ul>
                        <!-- End navigation menu -->

                        <div class="clearfix"></div>
                    </div>
                    <!-- end #navigation -->
                </div>
                <!-- end container -->
            </div>
            <!-- end navbar-custom -->

        </header>
        <!-- End Navigation Bar-->
