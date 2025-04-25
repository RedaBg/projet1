<?php

ob_start();
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] == 'Member') {
    header('Location: ../login.php');
    exit();
}

require "../database.php";

$conn = new mysqli($host, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $ticket_id = intval($_GET['id']);

    $sql = "SELECT id, title, msg, created, ticket_status, priority FROM tickets WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $ticket_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $ticket = $result->fetch_assoc(); 
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

$conn->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
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
                    <li class="nav-item mx-0 mx-lg-1"><a class="nav-link py-3 px-0 px-lg-3 rounded" href="admin.php">Gerer les tickets</a></li>
                    <li class="nav-item mx-0 mx-lg-1"><a class="nav-link py-3 px-0 px-lg-3 rounded" href="../logout.php">Se déconnecter</a></li>
                    <li class="nav-item mx-0 mx-lg-1"><a class="nav-link py-3 px-0 px-lg-3 rounded" href="../about.php">à propos</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="masthead bg-primary text-white text-center">
    <a href="admin.php" class="btn-back" style="margin-right: 650px;" style=""><img class="retour" src="../assets/img/fleche.png" style="height:30px;margin-top:-1px; padding-right:5px;"> Retour</a>

        <div class="container d-flex align-items-center flex-column">
            <h2 class="page-section-heading text-center text-uppercase text-white" style="margin-top: -50px;">Répondre au ticket</h2>
            <div class="divider-custom divider-light">
                <div class="divider-custom-line"></div>
                <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
                <div class="divider-custom-line"></div>
            </div>
            <?php
            if (isset($_GET['success']) && $_GET['success'] == 'true') {
                echo '<div class="alert alert-success" role="alert">Message envoyé.</div>';
            }
            try {           
                if(@$_POST['reponse']) {
                    require '../ticket.php';
                    reponseTicket();
                }
            }
            catch(PDOException $pe) {
                echo 'Erreur :'.$pe->getMessage();
            }
        ?>
        <style>
            textarea.form-control {
                resize: both; 
                overflow: auto;
                min-height: 100px; 
                min-width: 250px; 
            }
        </style>
 <div class="row">
            <div class="col me-5">
            <form action="?page=2&id=<?php echo $ticket_id ?>" method="post">

                    <label for="title" class="mb-2">Titre :</label>
                    <input type="text" class="form-control" id="title" name="title" style="width:450px;color: grey;" value="<?php echo htmlspecialchars($ticket['title']); ?>" readonly><br>

                    <label for="message" class="mb-2">Message :</label>
                    <textarea id="message" class="form-control" name="message" style="color: grey;" readonly><?php echo htmlspecialchars($ticket['msg']); ?></textarea><br>

                    <label for="created" class="mb-2">Date de création :</label>
                    <input type="text" class="form-control" id="created" name="created" value="<?php echo htmlspecialchars($ticket['created']); ?>" style="color: grey;" readonly><br>
                    </select><br>

            </div>
            <div class="col">
                <div class="box">
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        var chatContainer = document.querySelector(".chat-container");
                        chatContainer.scrollTop = chatContainer.scrollHeight; // Affiche directement le bas
                    });
                </script>
                    <?php require '../ticket.php'; chat(); ?>
                    <div class="row ms-auto chatbot">
                    <input type="text" class="form-control reponse" name="rep" placeholder="Tapez un message..." style="width:647px;" required><br>
                    <input type="submit" name="send" style="width:100px;position:absolute;right:0;background-color: rgba(var(--bs-secondary-rgb)" class="btn btn-primary" value="Envoyer">
                    </div>
                </div>
            </div>
            </form>

 <?php if (isset($_POST['send']) && !empty($_POST['rep'])) {
    require "../database.php";

    $msg = $_POST['rep'];
    $ticket_id = intval($_GET['id']);
    $account_id = $_SESSION['user_id'];

    $conn = new mysqli($host, $db_username, $db_password, $dbname);

    if ($conn->connect_error) {
        die("Échec de la connexion : " . $conn->connect_error);
    }

    $sql = "INSERT INTO tickets_comments (ticket_id, msg, account_id, notif) VALUES (?, ?, ?, 1)";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("isi", $ticket_id, $msg, $account_id);
        if ($stmt->execute()) {
            header("Location: ?id=$ticket_id&success=true");
            exit();
        } else {
            echo "Erreur d'exécution : " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Erreur de préparation : " . $conn->error;
    }

    $conn->close();
}
?>           

    </header>
    <div class="fixed-bottom copyright py-3 text-center text-white">
        <div class="container"><small>&copy Essaye de voler l'idée et BRUNET Loan te cassera la gueule</small></div>
    </div>
</body>
</html>
<?php ob_end_flush(); ?>