<?php
function getConnection() {
        $mysql = "localhost"; // nom du serveur
        $login = "root"; // identifiant de l'utilisateur
        $pwd = "nana"; // mot de passe de l'utilisateur
        $base = "pubmed"; // nom de la base
        $id_connect = mysql_connect($mysql, $login, $pwd)
        or die("Erreur interne : connexion au serveur de BD refus&eacute;e<br>");
        mysql_select_db($base)
        or die("Erreur interne : connexipon &agrave; la BD refus&eacute;e<br>");
}
function XMLtoARRAY($fichier,$item,$champs) {
  // on lit le fichier
  if(file_exists($fichier)){
  $tmp3 = array();
        if($chaine = @implode("",@file($fichier))) {
                
                // on éclate les objets <item>
                $tmp = preg_split("/<\/?".$item.">/",$chaine);
                
                // on parcours les <item>
                for($i=0;$i<sizeof($tmp)-1;$i+=1){
                        // on recherche les champs demandés
                           foreach($champs as $champ) {
                                $tmp2 = preg_split("/<\/?".$champ.">/",$tmp[$i]);
                                // on ajoute l'élément au tableau
                                $tmp3[$i][$champ] = @$tmp2[1];
                        }

                }
                // retourne le tableau associatif
                return $tmp3;
        }
  }else{
          return "Le fichier n'existe pas";
  }
}
function XMLtoSQL($fichier,$item,$champs,$test=0){

        //On recupère le tableau PHP correspondant au fichier XML
        $xml = XMLtoARRAY($fichier,$item,$champs);
        if(is_array($xml)){
                getconnection();
                $nom_table=$item;
                $requetes_insert=array();
                $requete="";
                foreach($xml as $un_enregistrement){
                        $requete="INSERT INTO ".$nom_table."  ";
                        $col_name="(";
                        $value="(";
                        foreach($un_enregistrement as $champs=>$valeur){
                                $col_name.=$champs.",";
                                $value.="\"".$valeur."\",";
                        }
                        $col_name=substr($col_name,0,-1);
                        $value=substr($value,0,-1);
                        $requete.=$col_name.") VALUES ".$value.")";
                        $requetes_insert[]=$requete;
                }
                
                //Si tout est ok on vide la table
                $vidange_table="TRUNCATE TABLE ".$nom_table;
                if($test==0){
                        $res_vidange=mysql_query($vidange_table);
                        if(!$res_vidange){
                                return "Erreur lors de la vidange de la table : ".$nom_table;
                        }
                }
                //Puis on execute les requêtes une par une
                foreach($requetes_insert as $key=>$une_requete){
                        if($test==0){
                                $res_requete=mysql_query($une_requete);
                                if(!$res_requete){
                                        return "Erreur lors de l'execution de la requete num ".$key." : ".$une_requete;
                                }
                        }
                }
                return "Importation des ".sizeof($requetes_insert)." requetes reussie";
        }else{
                return "L'erreur suivante a ete detectee : ".$xml;
        }
}
echo XMLtoSQL("testArticle.xml","items",array("NO","MC","IdInnovChamp"));

?>