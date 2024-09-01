 <nav class="navbar navbar-expand-lg bg-body-tertiary">
     <div class="container-fluid">

         <img src="assets/logo.png" height="80" alt="">
         <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
             <span class="navbar-toggler-icon"></span>
         </button>
         <div class="collapse navbar-collapse mx-5" id="navbarSupportedContent">
             <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                 <li class="nav-item">
                     <a class="nav-link active" aria-current="page" href="index.php">Home</a>
                 </li>
                 <li class="nav-item"><a class="nav-link" href="stats.php">Hostel Report</a></li>
                 <li class="nav-item"><a class="nav-link" href="allotment.php">Allot Room</a></li>


             </ul>
             <div class="nav-item dropdown ">
                 <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                     Hostel
                 </a>
                 <ul class="dropdown-menu">
                     <li><a class="dropdown-item" href="hostels.php">Hostels</a></li>
                     <!-- <li><a class="dropdown-item" href="rooms.php">Rooms</a></li -->
                     <li><a class="dropdown-item" href="beds.php">Beds</a></li>

                 </ul>
             </div>
             <a href="logout.php" class="btn btn-secondary mx-4">Logout</a>
         </div>
     </div>
 </nav>