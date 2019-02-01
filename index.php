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
                height:100%;
            }
            .colonne2 {
                width:74%;
                float:left;
                height:100%;
            }
            #texte {
                width:85%;
                text-align:center;
                vertical-align:middle;
            }
            #email {
                width:100%;
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
        <form style="display:inline;" action = "<?php echo $_SERVER['PHP_SELF'];?>" method = "GET">
            <textarea id="texte" name="story"></textarea>
            <input type = "submit" value="Compose"/>
        </form>
    </div>
   </div>
   <div class="colonne1">
        <h1>Liste</h1><br />
        <input id = "email" type = "submit" value="judais1998.steeven@hotmail.fr"/><br /><br>
        <input id = "email" type = "submit" value="albert1998.duchateau@hotmail.fr"/><br /><br>
    </div>
    <div class="colonne2">
        <h1>Messages</h1><br />
        - camion<br />
        - chient<br />
    </div>
   </body>
</html>
