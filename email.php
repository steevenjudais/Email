<?php
    // Connexion à la base de donnée
    $pdo = new PDO('mysql:host=localhost;dbname=Emails', 'root', 'joliverie'); 
    // Activation des erreurs
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $reelNom = "Emails";
    $id="id";
    $doublon=false;
    $liste= array("");
    $message= array("");
    $nbMessages=0;
    $nbEmail=0;
    $exp=false;
    $des=false;
    $envoie=false;

    // Clic sur bouton Connexion
    if (isset($_GET['connexion']) ? $_GET['connexion'] : NULL) {

        // On vérifie si l'email est valide
        $sql = 'SELECT nom from Email;';
        $req = $pdo->query($sql);
        while($row = $req->fetch()) {
            if ($row['nom'] == $_GET['connexion']) {
                $doublon = true;
            }
            if ($row['nom'].'1' == $_GET['connexion']) {
                $doublon = true;
                $envoie=true;
            }
        }

        // Si la personne a précedemment tenter d'envoyer un email à un destinataire n'existant pas,
        if ($envoie==true) {
        // On affiche ces informations normalement,
            $sql = 'SELECT nom from Email WHERE nom="'. substr($_GET['connexion'], 0, -1) .'";';
            $req = $pdo->query($sql);
            while($row = $req->fetch()) {
                //echo ($row['nom']);
                $reelNom = $row['nom'];

            }
            // Liste
            $sql = 'SELECT nom from Email;';
            $req = $pdo->query($sql);
            while($row = $req->fetch()) {
                $liste[]= $row['nom'] ."<br /><br>";
            }
            $nbEmail = count($liste);


            // Messages
            $sql = 'SELECT expediteur, destinataire, corp from Messages INNER JOIN Email ON Email.nom = Messages.expediteur or Email.nom = Messages.destinataire WHERE Email.nom = "'. $reelNom .'";';
            $req = $pdo->query($sql);
            while($row = $req->fetch()) {
                $message[] = "<div><p style= \"text-align: left;\">Expediteur : ". $row['expediteur'] ."<br><br> Destinataire : ". $row['destinataire'] ."<br><br></p><h3>Corps</h3>". $row['corp'] ."</div><br><br>";
            }
            $nbMessages = count($message);

            // Et on ajoute le message d'erreur
            echo 'L\'adresse email du destinataire n\'est pas une adresse email valide';


        }

        // Si l'email est valide, 
        if ($doublon==true) {
            // On change le libellé "Emails" par le nom de la personne connecté
            $sql = 'SELECT nom from Email WHERE nom="'. $_GET['connexion'] .'";';
            $req = $pdo->query($sql);
            while($row = $req->fetch()) {
                //echo ($row['nom']);
                $reelNom = $row['nom'];

            }

            // On remplit la Liste des emails
            $sql = 'SELECT nom from Email;';
            $req = $pdo->query($sql);
            while($row = $req->fetch()) {
                $liste[]= $row['nom'] ."<br /><br>";
            }
            $nbEmail = count($liste);


            // On remplit les Messages correspondant à l'utilisateur
            $sql = 'SELECT expediteur, destinataire, corp from Messages INNER JOIN Email ON Email.nom = Messages.expediteur or Email.nom = Messages.destinataire WHERE Email.nom = "'. $reelNom .'";';
            $req = $pdo->query($sql);
            while($row = $req->fetch()) {
                $message[] = "<div><p style= \"text-align: left;\">Expediteur : ". $row['expediteur'] ."<br><br> Destinataire : ". $row['destinataire'] ."<br><br></p><h3>Corps</h3>". $row['corp'] ."</div><br><br>";
            }
            $nbMessages = count($message);


        } else {
            echo "L'email utilisé n'est pas valide";
        }


        $req->closeCursor();

    // Clic sur bouton Ajouter
    } elseif (isset($_GET['nouvelEmail']) ? $_GET['nouvelEmail'] : NULL) {
            // On vérifie si l'email n'est pas déjà présent dans la base
            $sql = 'SELECT nom from Email;';
            $req = $pdo->query($sql);
            while($row = $req->fetch()) {
                if ($row['nom'] == $_GET['nouvelEmail']) {
                    //echo $row['nom'];
                    $doublon = true;
                }
            }
            // Si il n'est pas présent, on l'ajoute
            if ($doublon==false) {
                $sql = "INSERT INTO Email (id, nom) VALUES (?,?);";
                $req= $pdo->prepare($sql);
                $req->execute([$id, $_GET['nouvelEmail']]);
            }
            $req->closeCursor();
        
    // Si clic sur Envoyer
    } elseif ((isset($_GET['destinataire']) ? $_GET['destinataire'] : NULL) && (isset($_GET['corps']) ? $_GET['corps'] : NULL) && (isset($_GET['expediteur']) ? $_GET['expediteur'] : NULL)) {
        $exped = htmlspecialchars($_GET['expediteur']);
        $desti = htmlspecialchars($_GET['destinataire']);
        $corps = htmlspecialchars($_GET['corps']);
        
        // On vérifie que l'expediteur et le destinateur existe tout les 2 dans la base
        $sql = 'SELECT nom from Email;';
        $req = $pdo->query($sql);
        while($row = $req->fetch()) {
            if ($row['nom'] == $exped) {
                //echo $row['nom'];
                $exp = true;
            }
            if ($row['nom'] == $desti) {
                //echo $row['nom'];
                $des = true;
            }
        }

        // Si l'expediteur ET le destinataire existes dans la base
        if ($exp==true && $des == true) {
            // On envoie le message,
            $sql = "INSERT INTO Messages (id, expediteur, destinataire, corp) VALUES (?,?,?,?);";
            $req= $pdo->prepare($sql);
            $req->execute([$id, $exped, $desti, $corps]);
            // Et on se replace sur la page de l'utilisateur qui vient d'envoyer le message
            echo '<script>location.replace("email.php?connexion='.$exped.'"); </script>'; 
        // Si le destinataire n'existe pas dans la base
        } elseif ($exp==true && $des == false) {
            // On se replace sur la page de l'utilisateur qui envoie le message, avec affichage d'un message d'erreur
            echo '<script>location.replace("email.php?connexion='.$exped.'1");</script>';
        // Si l'expediteur n'existe pas dans la base
        } elseif ($exp==false) {
            // C'est que l'utilisateur essaie d'envoyer un message sans être connecté, on affiche alors un message d'erreur
            echo 'Vous devez vous connectez pour pouvoir envoyez des messages';
        }

        $req->closeCursor();        
    
    // Si clic sur bouton Déconnexion
    } elseif (isset($_POST['Déconnexion']) ? $_POST['Déconnexion'] : NULL) {
        // Renvoie sur l'index.php (email.php ici)
    } else {

    }
?>

<html>
    <head>
    <meta charset="utf-8">
        <style>
            body {
                text-align : center;
            }
            div {
                border-style : inset;
            }
            .colonne1 {
                width:25%;
                float:left;
                position: relative; 
            }
            .colonne2 {
                width:74%;
                float:left;
                position: relative; 
            }
            #texte {
                width:35%;
                text-align:center;
                vertical-align:middle;
            }
            #email {
                width:100%;
            }
            #titre {
                font-size: 40px;
            }
        </style>
    </head>
    <body>
        <div>
            <form style="float: right;" action = "<?php echo $_SERVER['PHP_SELF'];?>" method = "POST">
                <input type = "submit" name="Déconnexion" onclick='value="<?php echo($reelNom); ?>"' value="Déconnexion"/>
            </form>
            <p><h1 id = "titre"><?php echo $reelNom ?></h1></p>
            <form style="display: inline;" action = "<?php echo $_SERVER['PHP_SELF'];?>" method = "GET">
                Nouvel email :
                <input type = "text" name = "nouvelEmail"/>
                <input type = "submit" value="Ajouter"/>
            </form>
            <form style="display: inline;" action = "<?php echo $_SERVER['PHP_SELF'];?>" method = "GET">
                &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                Email :
                <input type = "text" name = "connexion"/>
                <input type = "submit" value="Connexion"/>
            </form>
            <br>
            </br>
            <div>
                <form style="display:inline;" action = "<?php echo $_SERVER['PHP_SELF'];?>" method = "GET">
                    <input type="hidden" name="expediteur" value="<?php echo($reelNom); ?>" >
                    Destinataire : 
                    <input type="text" id = "texte" name = "destinataire" style="height: 51px;" required>
                    Message : 
                    <textarea id="texte" name = "corps" required></textarea>
                    <input type = "submit" value="Envoyer"/>
                </form>
            </div>
        </div>
        <div class="colonne1">
            <h1>Liste</h1><br />
            <?php 
                // Affichage de tout les emails présents dans la base
                for ($i=1; $i < $nbEmail; $i++) { 
                    echo $liste[$i];
                }
            ?>
        </div>
        <div class="colonne2">
            <h1>Messages</h1><br />
            <?php 
                // Affichage de tout les messages en rapport avec l'utilisateur connecté
                for ($i=1; $i < $nbMessages; $i++) { 
                    echo $message[$i];
                }
            ?>
        </div>
    </body>
</html>
