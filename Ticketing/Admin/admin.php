<?php 

session_start();
if (!isset($_GET['sort']) || ($_GET['sort'] != 'recent' && $_GET['sort'] != 'none' && $_GET['sort'] != 'old' && $_GET['sort'] != 'state' && $_GET['sort'] != 'prio')) {
    header('Location: ?sort=none');
    exit();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] == 'Member') {
    header('Location: ../login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width,height=device-height, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />-
        <title>Service de Ticketing</title>
        <link rel="icon" type="../image/x-icon" href="../assets/favicon.ico" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
        <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css" />
        <link href="https://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic" rel="stylesheet" type="text/css" />
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="js/scripts.js"></script>
        <script src="https://cdn.startbootstrap.com/sb-forms-latest.js"></script>
        <link href="../css/styles.css" rel="stylesheet" />
        <link href="../css/table.css" rel="stylesheet">
        <link href="../css/btn.css" rel="stylesheet">
    </head>

    <body id="page-top" class="bg-primary">
    <nav class="navbar navbar-expand-lg bg-secondary text-uppercase fixed-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand" href="../index.php">Service de Ticketing</a>
            <button class="navbar-toggler text-uppercase font-weight-bold bg-primary text-white rounded" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                Menu
                <i class="fas fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item mx-0 mx-lg-1"><a class="nav-link py-3 px-0 px-lg-3 rounded" href="../logout.php">Se déconnecter</a></li>
                    <li class="nav-item mx-0 mx-lg-1"><a class="nav-link py-3 px-0 px-lg-3 rounded" href="../about.php">à propos</a></li>
                </ul>
            </div>
        </div>
    </nav>

        <section class="page-section bg-primary text-white" id="about">
            <div class="container">
                <h2 class="page-section-heading text-center text-uppercase text-white pt-5">Gestion des tickets</h2>
                <div class="divider-custom divider-light">
                    <div class="divider-custom-line"></div>
                    <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
                    <div class="divider-custom-line"></div>
                </div>
                <div class="ms-auto text-center lead">
                    <a href='admin.php?sort=recent' class='btn-create-ticket me-3'> Les plus récents</a>
                    <a href='admin.php?sort=old' class='btn-create-ticket me-3'>Les plus anciens</a>
                    <div class="dropdown">
                        <button class="dropbtn btn-create-ticket me-3">Statut</button>
                        <div class="dropdown-content">
                            <a href="admin.php?sort=state&state='open'">Ouvert</a>
                            <a href="admin.php?sort=state&state='closed'">Fermé</a>
                            <a href="admin.php?sort=state&state='resolved'">Résolut</a>
                        </div>
                    </div>
                    <div class="dropdown">
                        <button class="dropbtn btn-create-ticket ms-3">Priorité</button>
                        <div class="dropdown-content ms-3">
                            <a href="admin.php?sort=prio&prio='low'">Faible</a>
                            <a href="admin.php?sort=prio&prio='medium'">Moyenne</a>
                            <a href="admin.php?sort=prio&prio='high'">Élevée</a>
                        </div>
                    </div>
                </div>
                <div>

                <?php 
                    require '../ticket.php';
                    $sort = $_GET['sort'];
                    getTicketAll($sort);
                ?>

                </div>
            </div>
        </section>
            <div class="fixed-bottom copyright py-3 text-center text-white">
                <div class="container"><small>&copy Essaye de voler l'idée et BRUNET Loan te cassera la gueule</small></div>
            </div>

    </body>