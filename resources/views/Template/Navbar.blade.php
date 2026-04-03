<nav class="app-header navbar navbar-expand" style="background-color: rgb(91, 209, 123)">
    <!--begin::Container-->
    <div class="container-fluid">
        <!--begin::Start Navbar Links-->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                    <i class="bi bi-list"></i>
                </a>
            </li>
        </ul>
        <!--end::Start Navbar Links-->
        <!--begin::End Navbar Links-->
        <ul class="navbar-nav ms-auto">          
            <li class="nav-item dropdown user-menu">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    <img src="./public/img/user.png" class="user-image rounded-circle shadow"
                        alt="User Image" />
                    <span class="d-none d-md-inline">{{ auth()->user()->nama}}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                    <!--begin::User Image-->
                    <li class="user-header text-bg-teal">
                        <img src="./public/img/user.png" class="rounded-circle shadow"
                            alt="User Image" />
                        <p>
                            {{ auth()->user()->nama}} | {{ auth()->user()->username}} 
                            <small></small>
                        </p>
                    </li>
                    <li class="user-footer">
                        <a onclick="logout()" class="btn btn-default btn-flat float-end">Log out</a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
    <!--end::Container-->
</nav>