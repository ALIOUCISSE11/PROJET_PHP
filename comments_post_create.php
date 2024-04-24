<?php
session_start();
require_once(__DIR__ . '/config/mysql.php');
require_once(__DIR__ . '/databaseconnect.php');

// Vérification de la soumission du formulaire de commentaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Si le formulaire de commentaire a été soumis
    if (isset($_POST['comment']) && isset($_SESSION['LOGGED_USER'])) {
        $comment = $_POST['comment'];
        $email = $_SESSION['LOGGED_USER']['email'];

        // Récupération de l'ID de l'utilisateur depuis la base de données
        $getUserID = $mysqlClient->prepare('SELECT user_id FROM users WHERE email = :email');
        $getUserID->execute([
            'email' => $email
        ]);
        $user = $getUserID->fetch(PDO::FETCH_ASSOC);
        $user_id = $user['user_id'];

        // Vérification si la connexion à la base de données est établie
        if ($mysqlClient) {
            // Requête pour insérer le commentaire dans la base de données
            $sql = "INSERT INTO comments (comment, user_id, recipe_id) VALUES (:comment, :user_id, :recipe_id)";
            $stmt = $mysqlClient->prepare($sql);
            $stmt->bindParam(':comment', $comment);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':recipe_id', $_POST['recipe_id']);
            $stmt->execute();

            // Redirection de l'utilisateur vers la page de recette après avoir posté le commentaire
            header("Location: {$_SERVER['HTTP_REFERER']}");
            exit;
        } else {
            echo "Erreur de connexion à la base de données.";
        }
    }
}
?>
