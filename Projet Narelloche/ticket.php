<?php
function getTicket($id) {
    // Configuration de la base de données
    $host = 'localhost'; // Hôte
    $dbname = 'phpticket_advanced'; // Nom de la base de données
    $username = 'admin'; // Utilisateur de la base de données
    $password = 'admin'; // Mot de passe de l'utilisateur

    try {
        // Connexion à la base de données avec PDO
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Requête pour récupérer les données
        $query = "SELECT title, msg, created, ticket_status FROM tickets WHERE account_id = $id"; // Remplacez 'tickets' par votre table
        $stmt = $pdo->query($query);

        // Affichage des données sous forme de table HTML
        if ($stmt->rowCount() > 0) {
            echo "<table border='1'>
                    <tr>
                        <th>Titre</th>
                        <th>Message</th>
                        <th>Date de Création</th>
                        <th>Statut</th>
                    </tr>";

            // Parcours des résultats et affichage dans la table
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>
                        <td>" . $row['title'] . "</td>
                        <td>" . $row['msg'] . "</td>
                        <td>" . $row['created'] . "</td>";
                        if ($row['ticket_status'] == "open") {
                            echo "<td><div class='status-open'>Ouvert</div></td>";
                        } elseif ($row['ticket_status'] == "resolved") {
                            echo "<td><div class='status-resolved'>Résolut</div></td>";
                        } elseif ($row['ticket_status'] == "closed") {
                            echo "<td><div class='status-closed'>Fermé</div></td>";
                        }
                echo "</tr>";
            }

            echo "</table>";
        } else {
            echo '<div class="ms-auto text-center pt-5"><p class="lead">Vous n\'avez pas encore de tickets..</p></div>
';
        }
    } catch (PDOException $e) {
        // Si une erreur se produit, afficher le message d'erreur
        echo "Erreur de connexion à la base de données : " . $e->getMessage();
    }
}

function getTicketAll() {
    // Configuration de la base de données
    $host = 'localhost'; // Hôte
    $dbname = 'phpticket_advanced'; // Nom de la base de données
    $username = 'admin'; // Utilisateur de la base de données
    $password = 'admin'; // Mot de passe de l'utilisateur
    $i = 0;

    try {
        // Connexion à la base de données avec PDO
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Requête pour récupérer les données
        $query = "SELECT id, title, msg, created, priority, ticket_status FROM tickets"; // Remplacez 'tickets' par votre table
        $stmt = $pdo->query($query);

        // Affichage des données sous forme de table HTML
        if ($stmt->rowCount() > 0) {
            echo "<table border='1'>
                    <tr>
                        <th>Titre</th>
                        <th>Message</th>
                        <th>Date de Création</th>
                        <th>Statut</th>
                        <th>Priorité</th>
                        <th>Actions</th>
                    </tr>";

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>
                        <td>" . $row['title'] . "</td>
                        <td>" . $row['msg'] . "</td>
                        <td>" . $row['created'] . "</td>";
                        if ($row['priority'] == "low") {
                            echo "<td><div class='prio-low'>Faible</div></td>";
                        } elseif ($row['priority'] == "medium") {
                            echo "<td><div class='prio-med'>Moyenne</div></td>";
                        } elseif ($row['priority'] == "high") {
                            echo "<td><div class='prio-high'>Elévée</div></td>";
                        }

                        if ($row['ticket_status'] == "open") {
                            echo "<td><div class='status-open'>Ouvert</div></td>";
                        } elseif ($row['ticket_status'] == "resolved") {
                            echo "<td><div class='status-resolved'>Résolut</div></td>";
                        } elseif ($row['ticket_status'] == "closed") {
                            echo "<td><div class='status-closed'>Fermé</div></td>";
                        }
                echo   "<td><a href='gerer-ticket.php?id=" . $row['id'] . "'class='btn-create-ticket'>Gérer</a></td>
                    </tr>";
            }

            echo "</table>";
        } else {
            echo "Aucun ticket trouvé.";
        }
    } catch (PDOException $e) {
        // Si une erreur se produit, afficher le message d'erreur
        echo "Erreur de connexion à la base de données : " . $e->getMessage();
    }
}


function creerTicket() {

    // Récupérer les données du formulaire
    $title = $_POST['title'];
    $message = $_POST['message'];
    $category_id = $_POST['category'];

    // Récupérer les données de session
    $full_name = $_SESSION['username'];
    $email = $_SESSION['email'];
    $account_id = $_SESSION['user_id'];

    // Informations de connexion à la base de données
    $host = 'localhost';
    $db_username = 'admin';
    $db_password = 'admin';
    $dbname = 'phpticket_advanced';

    // Créer la connexion à la base de données
    $conn = new mysqli($host, $db_username, $db_password, $dbname);

    // Vérifier la connexion
    if ($conn->connect_error) {
        die("Échec de la connexion : " . $conn->connect_error);
    }

    // Récupérer le dernier ID existant
    $result = $conn->query("SELECT MAX(id) AS max_id FROM tickets");
    if ($result) {
        $row = $result->fetch_assoc();
        $last_id = $row['max_id'] ?? 0; // Si aucun ID n'existe encore, démarrer à 0
    } else {
        die("Erreur lors de la récupération du dernier ID : " . $conn->error);
    }

    // Attribuer un nouvel ID
    $new_id = $last_id + 1;

    // Préparer la requête d'insertion
    $sql = "INSERT INTO tickets (id, title, msg, full_name, email, created, ticket_status, priority, category_id, private, account_id, approved) 
            VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP, 'open', 'low', ?, 0, ?, 1)";

    // Préparer la déclaration
    if ($stmt = $conn->prepare($sql)) {
        // Lier les paramètres
        $stmt->bind_param("issssii", $new_id, $title, $message, $full_name, $email, $category_id, $account_id);

        // Exécuter la requête
        if ($stmt->execute()) {
            header("Location: membre.php");
            exit();
        } else {
            echo "Erreur lors de la création du ticket : " . $stmt->error;
        }

        // Fermer la déclaration
        $stmt->close();
    } else {
        echo "Erreur de préparation de la requête : " . $conn->error;
    }

    // Fermer la connexion à la base de données
    $conn->close();
}

function gererTicket() {

        $ticket_status = $_POST['ticket_status'];
        $priority = $_POST['priority'];
        $ticket_id = intval($_GET['id']);  // Sécuriser l'ID du ticket

        // Informations de connexion à la base de données
        $host = 'localhost';
        $db_username = 'admin';
        $db_password = 'admin';
        $dbname = 'phpticket_advanced';

        // Créer la connexion à la base de données
        $conn = new mysqli($host, $db_username, $db_password, $dbname);

        // Vérifier la connexion
        if ($conn->connect_error) {
            die("Échec de la connexion : " . $conn->connect_error);
        }

        // Préparer la requête de mise à jour
        $sql = "UPDATE tickets SET ticket_status = ?, priority = ? WHERE id = ?";

        // Préparer la déclaration
        if ($stmt = $conn->prepare($sql)) {
            // Lier les paramètres
            $stmt->bind_param("ssi", $ticket_status, $priority, $ticket_id);

            // Exécuter la requête
            if ($stmt->execute()) {
                // Si la mise à jour est réussie, rediriger
                header("Location: gerer-ticket.php?id=" . $ticket_id . "&success=true"); 
                exit();
            } else {
                echo "Erreur lors de la mise à jour du ticket : " . $stmt->error;
            }

            // Fermer la déclaration
            $stmt->close();
        } else {
            echo "Erreur de préparation de la requête : " . $conn->error;
        }

        // Fermer la connexion à la base de données
        $conn->close();
}

function getModif() {
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
}

?>
