<?php 

function login() {

    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];
    
        $host = 'localhost';
        $username = 'admin'; 
        $db_password = 'admin';  
        $dbname = 'phpticket_advanced';
    
        $conn = new mysqli($host, $username, $db_password, $dbname);
    
        if ($conn->connect_error) {
            die("Échec de la connexion : " . $conn->connect_error);
        }
    
        $sql = "SELECT * FROM accounts WHERE email = ?";
    
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $email);
    
            $stmt->execute();
    
            $result = $stmt->get_result();
    
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
    
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
                echo "Aucun compte trouvé avec cet email.";
            }
    
            $stmt->close();
        }
    
        $conn->close();
    } else {
        echo "Veuillez fournir un email et un mot de passe.";
    }

}

function creerCompte() {

    if (isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $role = 'Member'; 

        $host = 'localhost';
        $db_username = 'admin';
        $db_password = 'admin';
        $dbname = 'phpticket_advanced';

        $conn = new mysqli($host, $db_username, $db_password, $dbname);

        if ($conn->connect_error) {
            die("Échec de la connexion : " . $conn->connect_error);
        }

        $result = $conn->query("SELECT MAX(id) AS max_id FROM accounts");
        if ($result) {
            $row = $result->fetch_assoc();
            $last_id = $row['max_id'] ?? 0;
        } else {
            die("Erreur lors de la récupération du dernier ID : " . $conn->error);
        }

        $new_id = $last_id + 1;

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $sql = "INSERT INTO accounts (id, full_name, password, email, role) VALUES (?, ?, ?, ?, ?)";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("issss", $new_id, $username, $hashedPassword, $email, $role);

            if ($stmt->execute()) {
                header("Location: mail.php?action=2&user=" .$username. ""); 
                exit();
            } else {
                echo "Erreur lors de la création du compte : " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Erreur de préparation de la requête : " . $conn->error;
        }

        $conn->close();
    } else {
        echo "Veuillez fournir un nom d'utilisateur, un email et un mot de passe.";
    }
}