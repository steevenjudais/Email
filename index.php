<?php
	$texte;
	if (isset($_GET['name']) ? $_GET['name'] : NULL) {
		$texte = "Vous avez saisi : ". $_GET['name'];
	} else {
		$texte = "Vous n'avez rien saisi";
	}
?>
<html>
    <head>
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
            }
            .colonne2 {
                width:74%;
                float:left;
            }
            #texte {
                width:85%;
            }
        </style>
    </head>
   <body>
   <div>
   <p><h1>Email</h1></p>
   <!--<?php echo $texte ?>-->
      <form action = "<?php echo $_SERVER['PHP_SELF'];?>" method = "GET">
            Nouvel email :
         <input type = "text" name = "name"/>
         <input type = "submit" value="Ajouter"/>
      </form>
    <div>
        <form action = "<?php echo $_SERVER['PHP_SELF'];?>" method = "GET">
            <textarea id="texte" name="story"></textarea>
            <input type = "submit" value="Compose"/>
        </form>
    </div>
   </div>
   <div class="colonne1">
        <h1>Liste</h1><br />
        - couture<br />
        - Cheval<br />
    </div>
    <div class="colonne2">
        - voiture<br />
        - camion<br />
        - chient<br />
    </div>
   </body>
</html>
