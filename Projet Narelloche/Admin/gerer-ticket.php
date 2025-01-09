<?php 
ob_start();
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] == 'Member') {
    header('Location: ../login.php');
    exit();
}

$host = 'localhost';
$db_username = 'admin';
$db_password = 'admin';
$dbname = 'phpticket_advanced';

// Connexion à la base de données
$conn = new mysqli($host, $db_username, $db_password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Vérifier si l'ID du ticket est présent dans l'URL
if (isset($_GET['id'])) {
    $ticket_id = intval($_GET['id']); // Sécuriser l'entrée

    // Requête pour récupérer les données du ticket
    $sql = "SELECT id, title, msg, created, ticket_status, priority FROM tickets WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $ticket_id); // Associer l'ID à la requête
        $stmt->execute();
        $result = $stmt->get_result();

        // Vérifier si le ticket existe
        if ($result->num_rows > 0) {
            $ticket = $result->fetch_assoc(); // Récupérer les données du ticket
        } else {
            die("Ticket introuvable.");
        }

        $stmt->close();
    } else {
        die("Erreur de préparation de la requête : " . $conn->error);
    }
} else {
    die("ID du ticket manquant.");
}

// Fermer la connexion
$conn->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Freelancer - Start Bootstrap Theme</title>
    <!-- Favicon-->
    <link rel="icon" type="../image/x-icon" href="../assets/favicon.ico" />
    <!-- Font Awesome icons (free version)-->
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <!-- Google fonts-->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js"></script>
    <script src="https://cdn.startbootstrap.com/sb-forms-latest.js"></script>
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="../css/styles.css" rel="stylesheet" />
    <link href="../css/table.css" rel="stylesheet">
</head>

<body id="page-top" class="bg-primary">
    <!-- Navigation-->
    <nav class="navbar navbar-expand-lg bg-secondary text-uppercase fixed-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand" href="../index.php">Service de Ticketing</a>
            <button class="navbar-toggler text-uppercase font-weight-bold bg-primary text-white rounded" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                Menu
                <i class="fas fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item mx-0 mx-lg-1"><a class="nav-link py-3 px-0 px-lg-3 rounded" href="admin.php">Gerer les tickets</a></li>
                    <li class="nav-item mx-0 mx-lg-1"><a class="nav-link py-3 px-0 px-lg-3 rounded" href="../logout.php">Se déconnecter</a></li>
                    <li class="nav-item mx-0 mx-lg-1"><a class="nav-link py-3 px-0 px-lg-3 rounded" href="../about.php">à propos</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Form Section -->
    <header class="masthead bg-primary text-white text-center" style="height: 100vh;">
        <div class="container d-flex align-items-center flex-column">
        <h2 class="page-section-heading text-center text-uppercase text-white" style="margin-top: -50px;">Gérer le ticket</h2>
            <div class="divider-custom divider-light">
                <div class="divider-custom-line"></div>
                <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
                <div class="divider-custom-line"></div>
            </div>
            <?php
            // Vérifier si le message de succès est passé dans l'URL
            if (isset($_GET['success']) && $_GET['success'] == 'true') {
                echo '<div class="alert alert-success" role="alert">Le ticket a bien été modifié.</div>';               
            }
            try {           
                if(@$_POST['modif']) {
                    require '../ticket.php';
                    gererTicket();
                }
            }
            catch(PDOException $pe) {
                echo 'Erreur :'.$pe->getMessage();
            }
        ?>
        <style>
            textarea.form-control {
                resize: both; /* Permet de redimensionner horizontalement et verticalement */
                overflow: auto; /* Affiche une barre de défilement si nécessaire */
                min-height: 100px; /* Définir une hauteur minimale */
                min-width: 250px;  /* Définir une largeur minimale */
            }
        </style>
            <form action="" method="post">

                    <label for="title" class="mb-2">Titre :</label>
                    <input type="text" class="form-control" id="title" name="title" style="width:450px;color: grey;" value="<?php echo htmlspecialchars($ticket['title']); ?>" readonly><br>

                    <label for="message" class="mb-2">Message :</label>
                    <textarea id="message" class="form-control" name="message" style="color: grey;" readonly><?php echo htmlspecialchars($ticket['msg']); ?></textarea><br>

                    <label for="created" class="mb-2">Date de création :</label>
                    <input type="text" class="form-control" id="created" name="created" value="<?php echo htmlspecialchars($ticket['created']); ?>" style="color: grey;" readonly><br>

                    <label for="priority" class="mb-2">Priorité :</label>
                    <select name="priority" id="priority" class="form-control">
                        <option value="low" <?php echo ($ticket['priority'] === 'low') ? 'selected' : ''; ?>>Low</option>
                        <option value="medium" <?php echo ($ticket['priority'] === 'medium') ? 'selected' : ''; ?>>Medium</option>
                        <option value="high" <?php echo ($ticket['priority'] === 'high') ? 'selected' : ''; ?>>High</option>
                    </select><br>

                    <label for="ticket_status" class="mb-2">Statut :</label>
                    <select name="ticket_status" id="ticket_status" class="form-control">
                        <option value="open" <?php echo ($ticket['ticket_status'] === 'open') ? 'selected' : ''; ?>>Ouvert</option>
                        <option value="resolved" <?php echo ($ticket['ticket_status'] === 'resolved') ? 'selected' : ''; ?>>Résolut</option>
                        <option value="closed" <?php echo ($ticket['ticket_status'] === 'closed') ? 'selected' : ''; ?>>Fermé</option>
                    </select><br>

                <input type="submit" name="modif" class="btn mt-3 mb-3 btn-primary border border-white" value="Modifier le ticket">
            </form>
        </div>
    </header>

</body>
</html>
<?php ob_end_flush(); ?>