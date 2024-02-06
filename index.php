<?php
$serveurName = 'localhost';
$userName = 'root';
$password = 'Romainjulie1402';
$error = true;

function regex($nom, $prenom, $mail, $codepostal)
{
    $regexNom = "/^[a-zA-ZÀ-ÿ\-]+$/";
    $regexPrenom = "/^[a-zA-ZÀ-ÿ\-]+$/";
    $regexMail = "/^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$/";
    $regexCodePostal = "/^[0-9]{5}$/";
    if (!preg_match($regexNom, $nom)) {
        return 'Nom invalide.';
    }
    
    if (!preg_match($regexPrenom, $prenom)) {
        return 'Prénom invalide.';
    }

    if (!preg_match($regexMail, $mail)) {
        return 'Email invalide.';
    }

    if (!preg_match($regexCodePostal, $codepostal)) {
        return 'Code Postal invalide.';
    }
    return true;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $db = new PDO("mysql:host=$serveurName;dbname=projet-php-sql", $userName, $password);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db -> beginTransaction();
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'ajouter':
                    
                    $codeSQL = $db->prepare("INSERT INTO Users(Nom, Prénom, Mail, CodePostal) VALUES (:nom, :prenom, :mail, :codepostal)");
                    $codeSQL->bindParam(':nom', $_POST['nom']);
                    $codeSQL->bindParam(':prenom', $_POST['prenom']);
                    $codeSQL->bindParam(':mail', $_POST['mail']);
                    $codeSQL->bindParam(':codepostal', $_POST['code_postal']);
                    $error = regex($_POST['nom'], $_POST['prenom'], $_POST['mail'], $_POST['code_postal']);
                    if ($error === true) {
                        $codeSQL->execute();
                    }

                    break;
                case 'save':
                    $codeSQL = $db->prepare("UPDATE Users SET Nom = :nom, Prénom = :prenom, Mail = :mail, CodePostal = :codepostal WHERE idUsers = :id");
                    $codeSQL->bindParam(':id', $_POST['idUsers']);
                    $codeSQL->bindParam(':nom', $_POST['nom_modif']);
                    $codeSQL->bindParam(':prenom', $_POST['prenom_modif']);
                    $codeSQL->bindParam(':mail', $_POST['mail_modif']);
                    $codeSQL->bindParam(':codepostal', $_POST['code_postal_modif']);
                    $error = regex($_POST['nom_modif'], $_POST['prenom_modif'], $_POST['mail_modif'], $_POST['code_postal_modif']);
                    if ($error === true) {
                        $codeSQL->execute();
                        
                    }
                    break;
                case 'supprimer':
                    
                    $codeSQL = $db->prepare("DELETE FROM Users WHERE idUsers = :id");
                    $codeSQL->bindParam(':id', $_POST['idUsers']);
                    $codeSQL->execute();
                    break;
            }
        }

        $db -> commit();
        $db = null;
    } catch (PDOException $e) {
        $db -> rollback();
        echo 'Erreur : ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style.css">
    <title>Gestion des Utilisateurs</title>
</head>

<body>
    <h1>Gestion des Utilisateurs</h1>
    <div id="container">
        <div id="addUsers">
            <form id="form" method="post">
                <label for="nom">Nom:</label>
                <input type="text" name="nom" required>
                <br>
                <br>
                <label for="prenom">Prénom:</label>
                <input type="text" name="prenom" required>
                <br>
                <br>
                <label for="mail">E-mail:</label>
                <input type="email" name="mail" required>
                <br>
                <br>
                <label for="code_postal">Code Postal:</label>
                <input type="text" name="code_postal" required>
                <br>
                <br>
                <input type="hidden" value="ajouter">
                <button style="cursor: pointer" name="action" value="ajouter" type="submit">Ajouter</button>
            </form>
        </div>

        <table id="table">
            <tr>
                <th class="array">Nom</th>
                <th class="array">Prénom</th>
                <th class="array">Mail</th>
                <th class="array">Code Postal</th>
                <!-- <th>Actions</th> -->
            </tr>

            <?php
            try {
                $db = new PDO("mysql:host=$serveurName;dbname=projet-php-sql", $userName, $password);
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $db -> beginTransaction();
                $result = $db->query("SELECT * FROM Users");

                foreach ($result as $utilisateur) {
                    echo "<tr><form action='index.php' method='post' style='display:inline;'>";
                    if ($_POST['action'] == 'modifier' && $_POST['idUsers'] == $utilisateur["idUsers"]) {
                        echo "<td> <input type='text' name='nom_modif' placeholder='Nouveau Nom' value='" . $utilisateur["Nom"] . "' required> </td>";
                        echo "<td> <input type='text' name='prenom_modif' placeholder='Nouveau Prénom' value='" . $utilisateur["Prénom"] . "' required> </td>";
                        echo "<td> <input type='email' name='mail_modif' placeholder='Nouvel E-mail' value='" . $utilisateur["Mail"] . "' required> </td>";
                        echo "<td> <input type='text' name='code_postal_modif' placeholder='Nouveau Code Postal' value='" . $utilisateur["CodePostal"] . "' required> </td>";
                        echo "<td> 
                                
                                    <input type='hidden' name='idUsers' value='" . $utilisateur["idUsers"] . "'>
                                    <button style='cursor: pointer' type='submit' name='action' value='save'>Enregistrer</button> 
                                
                            </td>";
                        echo "<td> 
                                    <input type='hidden' name='idUsers' value='" . $utilisateur["idUsers"] . "'>
                                    <button style='cursor: pointer' type='submit' name='action' value='supprimer'>Supprimer</button> 
                            </td>";
                    } else {
                        echo "<td class='array'>{$utilisateur["Nom"]}</td>";
                        echo "<td class='array'>{$utilisateur["Prénom"]}</td>";
                        echo "<td class='array'>{$utilisateur["Mail"]}</td>";
                        echo "<td class='array'>{$utilisateur["CodePostal"]}</td>";
                        echo "<td> 
                                    <input type='hidden' name='idUsers' value='" . $utilisateur["idUsers"] . "'>
                                    <button style='cursor: pointer' type='submit' name='action' value='modifier'>Modifier</button> 
                            </td>";
                        echo "<td> 
                                    <input type='hidden' name='idUsers' value='" . $utilisateur["idUsers"] . "'>
                                    <button style='cursor: pointer' type='submit' name='action' value='supprimer'>Supprimer</button> 
                            </td>";
                    }
                    echo "</form></tr>";
                }

                $db -> commit();
                $db = null;
            } catch (PDOException $e) {
                $db -> rollback();
                echo 'Erreur : ' . $e->getMessage();
            }

            if($error!==true){
                echo $error;
            }
            ?>
        </table>
    </div>
</body>

</html>