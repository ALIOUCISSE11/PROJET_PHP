<!-- inclusion des variables et fonctions -->
<?php
session_start();
require_once(__DIR__ . '/config/mysql.php');
require_once(__DIR__ . '/databaseconnect.php');
require_once(__DIR__ . '/variables.php');
require_once(__DIR__ . '/functions.php');

// Traitement de la soumission du formulaire d'inscription
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Si le formulaire d'inscription a été soumis
    if (isset($_POST['inscription_email']) && isset($_POST['inscription_password']) && isset($_POST['prenom']) && isset($_POST['nom']) && isset($_POST['age'])) {
        $email = $_POST['inscription_email'];
        $password = $_POST['inscription_password'];
        $full_name = $_POST['prenom'] . ' ' . $_POST['nom'];
        $age = $_POST['age'];

        // Vérifier si la connexion à la base de données est établie
        if ($mysqlClient) {
            // Requête pour vérifier si l'utilisateur existe déjà dans la base de données
            $sql = "SELECT * FROM users WHERE email = :email";
            $stmt = $mysqlClient->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if ($stmt->rowCount() == 0) {
                // L'utilisateur n'existe pas, on peut l'inscrire
                $sql = "INSERT INTO users (full_name, email, password, age) VALUES (:full_name, :email, :password, :age)";
                $stmt = $mysqlClient->prepare($sql);
                $stmt->bindParam(':full_name', $full_name);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password', $password);
                $stmt->bindParam(':age', $age);
                $stmt->execute();

                $_SESSION['LOGGED_USER'] = array('email' => $email);
                
                // Rediriger l'utilisateur vers la page d'accueil après l'inscription
                header("Location: index.php");
                exit;
            } else {
                $_SESSION['LOGIN_ERROR_MESSAGE'] = "Un compte avec cet email existe déjà.";
            }
        } else {
            echo "Erreur de connexion à la base de données.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Site de recettes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .list-inline-item {
            margin-right: 5px;
        }
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .card {
            width: 100%;
            max-width: 400px;
            padding: 15px;
            margin: auto;
        }
        .card-body {
            padding: 15px;
        }
        .form-floating {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <div class="card-body">
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                <h1 class="h3 mb-3 fw-normal">Inscription</h1>

                <!-- si message d'erreur on l'affiche -->
                <?php if (isset($_SESSION['LOGIN_ERROR_MESSAGE'])) : ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $_SESSION['LOGIN_ERROR_MESSAGE'];
                        unset($_SESSION['LOGIN_ERROR_MESSAGE']); ?>
                    </div>
                <?php endif; ?>

                <div class="form-floating">
                    <input type="text" class="form-control" id="prenom" name="prenom" placeholder="John" required>
                    <label for="prenom">Prénom</label>
                </div>
                <div class="form-floating">
                    <input type="text" class="form-control" id="nom" name="nom" placeholder="Doe" required>
                    <label for="nom">Nom</label>
                </div>
                <div class="form-floating">
                    <input type="email" class="form-control" id="inscription_email" name="inscription_email"
                        placeholder="you@exemple.com" required>
                    <label for="inscription_email">Email</label>
                    
                </div>
                <div class="form-floating">
                    <input type="password" class="form-control" id="inscription_password" name="inscription_password" required>
                    <label for="inscription_password">Mot de passe</label>
                </div>
                <div class="form-floating">
                    <input type="number" class="form-control" id="age" name="age" placeholder="30" required>
                    <label for="age">Âge</label>
                </div>
                <button class="w-100 btn btn-lg btn-primary" type="submit">S'inscrire</button>
                <p class="mt-3 mb-3 text-muted">Vous avez déjà un compte ? <a href="login.php">Connectez-vous ici</a>.</p>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
