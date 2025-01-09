<?php 

function login() {

    if (isset($_POST['email']) && isset($_POST['password'])) {
        // Récupérer l'email et le mot de passe saisi
        $email = $_POST['email'];
        $password = $_POST['password'];
    
        // Informations de connexion à la base de données
        $host = 'localhost';       // Hôte de la base de données
        $username = 'admin';        // Nom d'utilisateur de la base de données
        $db_password = 'admin';     // Mot de passe de la base de données
        $dbname = 'phpticket_advanced'; // Nom de la base de données
    
        // Créer la connexion à la base de données
        $conn = new mysqli($host, $username, $db_password, $dbname);
    
        // Vérifier la connexion
        if ($conn->connect_error) {
            die("Échec de la connexion : " . $conn->connect_error);
        }
    
        // Préparer la requête pour rechercher l'utilisateur par email
        $sql = "SELECT * FROM accounts WHERE email = ?";
    
        // Préparer la déclaration
        if ($stmt = $conn->prepare($sql)) {
            // Lier les paramètres (email de l'utilisateur)
            $stmt->bind_param("s", $email);
    
            // Exécuter la requête
            $stmt->execute();
    
            // Récupérer les résultats
            $result = $stmt->get_result();
    
            // Vérifier si un compte avec cet email a été trouvé
            if ($result->num_rows > 0) {
                // Un compte avec cet email a été trouvé, récupérer les informations
                $row = $result->fetch_assoc();
    
                // Vérifier si le mot de passe saisi correspond au mot de passe hashé stocké
                if (password_verify($password, $row['password'])) {
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['username'] = $row['full_name'];
                    $_SESSION['email'] = $row['email'];
                    $_SESSION['role'] = $row['role'];

                    if ($row['role'] == "Member")
                        header("Location: Membres/membre.php");
                    else
                        header("Location: Admin/admin.php");
                }
                else {
                    echo "Mot de passe incorrect.";
                }
            } else {
                // Aucun compte trouvé avec cet email
                echo "Aucun compte trouvé avec cet email.";
            }
    
            // Fermer la déclaration
            $stmt->close();
        }
    
        // Fermer la connexion à la base de données
        $conn->close();
    } else {
        // Si les champs email ou mot de passe ne sont pas remplis
        echo "Veuillez fournir un email et un mot de passe.";
    }

}

function creerCompte() {

    if (isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password'])) {
        // Récupérer les données du formulaire
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $role = 'Member'; 

        // Informations de connexion à la base de données
        $host = 'localhost';       // Hôte de la base de données
        $db_username = 'admin';     // Nom d'utilisateur de la base de données
        $db_password = 'admin';     // Mot de passe de la base de données
        $dbname = 'phpticket_advanced'; // Nom de la base de données

        // Créer la connexion à la base de données
        $conn = new mysqli($host, $db_username, $db_password, $dbname);

        // Vérifier la connexion
        if ($conn->connect_error) {
            die("Échec de la connexion : " . $conn->connect_error);
        }

        // Récupérer le dernier ID existant
        $result = $conn->query("SELECT MAX(id) AS max_id FROM accounts");
        if ($result) {
            $row = $result->fetch_assoc();
            $last_id = $row['max_id'] ?? 0; // Si aucun ID n'existe encore, démarrer à 0
        } else {
            die("Erreur lors de la récupération du dernier ID : " . $conn->error);
        }

        // Attribuer un nouvel ID
        $new_id = $last_id + 1;

        // Hachage du mot de passe pour la sécurité
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Préparer la requête d'insertion
        $sql = "INSERT INTO accounts (id, full_name, password, email, role) VALUES (?, ?, ?, ?, ?)";

        // Préparer la déclaration
        if ($stmt = $conn->prepare($sql)) {
            // Lier les paramètres (id, nom d'utilisateur, email, mot de passe, rôle)
            $stmt->bind_param("issss", $new_id, $username, $hashedPassword, $email, $role);

            // Exécuter la requête
            if ($stmt->execute()) {
                header("Location: Membres/membre.php");
            } else {
                echo "Erreur lors de la création du compte : " . $stmt->error;
            }

            // Fermer la déclaration
            $stmt->close();
        } else {
            echo "Erreur de préparation de la requête : " . $conn->error;
        }

        // Fermer la connexion à la base de données
        $conn->close();
    } else {
        // Si les champs ne sont pas remplis
        echo "Veuillez fournir un nom d'utilisateur, un email et un mot de passe.";
    }
}