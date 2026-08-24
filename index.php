<?php
/******************************************************************************
MIT License

Copyright (c) 2026 paul-laval

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

******************************************************************************/

require_once("private.php");
require_once("tools.php");

error_reporting(E_ALL);
ini_set("display_errors", 1);
opcache_reset();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta property="og:title" content="Je suis une personne !" />
    <meta property="og:description" content="Site de détection de robot personnalisé" />
    <meta property="og:image" content="https://jesuisunepersonne.fr/logo.png" />
    <meta property="og:url" content="https://jesuisunepersonne.fr" />
    <meta name="description" content="Site de détection de robot personnalisé" />
    <link rel="icon" href="https://jesuisunepersonne.fr/logo.ico" />
    <link rel="image_src" type="image/png" href="https://jesuisunepersonne.fr/logo.png" />
    <link rel="stylesheet" href="styles.css" />
    <title>Je suis une personne !</title>
    <script src="script.js"></script>
<?php
$connect = @mysqli_connect(getMySqlLocalhost(), getMySqlUser(), getMySqlPassword(), getMySqlDB());
$error = (empty($connect))? 1:0;
if ($error == 0) {

    $date = getdate();
    if (!empty($_POST) && empty($_POST["id"])) {

        $response = checkReCaptcha(getReCaptchaKey());
        if ($response && $response->success) {

            // Purge
            $query = "DELETE FROM personne WHERE date + 86400 < {$date[0]}";
            $result = mysqli_query($connect, $query);
            if ($result) {

                $uuid = generate_uuid();
                $query = "SELECT COUNT(*) FROM personne WHERE uuid = '{$uuid}'";
                $result = mysqli_query($connect, $query);
                $count = mysqli_fetch_row($result);
                if ($count[0] == 0) {

                    $delay = ($_POST["delay"] * 60) + 30;
                    $query = "INSERT INTO personne (id, date, delay, uuid, robot) VALUES (NULL, {$date[0]}, {$delay}, '{$uuid}', TRUE)";
                    $result = mysqli_query($connect, $query);
                    if ($result) { ///////////////////////////////////////////////////////////////////////////////// Link created
?>
    <script>
        var delay = <?php echo $delay; ?> * 1000;

        function copy() {
            navigator.clipboard.writeText("https://jesuisunepersonne.fr/index.php?id=<?php echo $uuid; ?>")
                .then(() => {
                    var snackBar = document.getElementById("snackbar");
                    snackBar.className = "show";
                    setTimeout(function() { snackBar.className = snackBar.className.replace("show", ""); }, 2000);
                })
                .catch(() => {
                    alert("Erreur de copie, veuillez cliquer sur le lien");
                });
        }

        setInterval(() => {
            delay = displayExpirationTime(delay);
        }, 1000);
        delay = displayExpirationTime();
    </script>
</head>
<body style="margin-top: 62px">
	<h1 id="expire">&nbsp;</h1>
    <table>
        <tr>
            <td><a href="https://jesuisunepersonne.fr/index.php?id=<?php echo $uuid; ?>" class="link">https://jesuisunepersonne.fr/index.php?id=<?php echo $uuid; ?></a></td>
        </tr>
        <tr>
            <td style="text-align:center" onclick="copy()"><button type="submit">Copier le lien</button></td>
        </tr>
    </table>
    <table onclick="openSource()" style="cursor: pointer">
        <tr>
            <td style="text-align:center">OpenSource by&nbsp;&nbsp;<img src="openSource.png"></td>
        </tr>
    </table>
    <div id="snackbar">Lien copié</div>
<?php
                    }
                    else { /// Database error (insert)
                        traceError("DB insert");
                        $error = 2;
                    }
                }
                else { /// Database error (existing UUID)
                    traceError("existing UUID");
                    $error = 2;
                }
            }
            else { /// Database error (purge)
                traceError("DB purge");
                $error = 2;
            }
        }
        else { /// reCAPTCHA error
            traceError("reCAPTCHA create");
            $error = 2;
        }        
    }
    else {
        if (!empty($_GET["id"]) && !empty($_POST) && !empty($_POST["id"])) {

            /// Unexpected error
            traceError("Unexpected error");
            $error = 2;
        }
        else if (!empty($_GET["id"]) || (!empty($_POST) && !empty($_POST["id"]))) {

            if (empty($_GET["id"])) {
                $response = checkReCaptcha(getReCaptchaKey());
                if (empty($response) || !$response->success) {

                    /// reCAPTCHA error
                    traceError("reCAPTCHA check");
                    $error = 2;
                }
            }
            $expired = true;

            $uuid = (!empty($_GET["id"]))? $_GET["id"]:$_POST["id"];
            if ($error == 0 && strlen($uuid) == 36 && substr($uuid, 8, 1) == "-" && substr($uuid, 13, 1) == "-" && substr($uuid, 18, 1) == "-" && substr($uuid, 23, 1) == "-") {

                $query = "SELECT * FROM personne WHERE uuid = '" . addslashes($uuid) . "'";
                $result = mysqli_query($connect, $query);
                $fields = mysqli_fetch_assoc($result);
                if ($fields) {

                    $updated = false;
                    if ($fields["robot"] && empty($_GET["id"]) && ($fields["date"] + $fields["delay"]) > $date[0]) {

                        $query = "UPDATE personne SET robot = FALSE WHERE uuid = '" . addslashes($uuid) . "'";
                        $result = mysqli_query($connect, $query);
                        if (!empty($result)) {
                            $updated = true;
                        }
                        else { /// Database error (update failed)
                            traceError("update failed");
                            $error = 2;
                        }
                    }
                    if (!$fields["robot"] || $updated) {
                        $expired = false; /////////////////////////////////////////////////////////////////////////// Not a robot
?>
</head>
<body>
    <table>
        <tr>
            <td style="text-align:center"><h1>Je suis une personne !</h1></td>
        </tr>
        <tr>
            <td style="text-align:center"><img src="success.png"></td>
        </tr>
    </table>
    <table onclick="openSource()" style="cursor: pointer">
        <tr>
            <td style="text-align:center">OpenSource by&nbsp;&nbsp;<img src="openSource.png"></td>
        </tr>
    </table>
<?php
                    }
                    else if (($fields["date"] + $fields["delay"]) > $date[0]) {
                        $expired = false;
                        $delay = $fields["date"] + $fields["delay"] - $date[0]; /////////////////////////////// Check in progress
?>
    <script src="https://www.google.com/recaptcha/enterprise.js" async defer></script>
    <script>
        var delay = <?php echo $delay; ?> * 1000;

        setInterval(() => {
            delay = displayExpirationTime(delay);
        }, 1000);
        delay = displayExpirationTime(delay);
    </script>
</head>
<body style="margin-top: 62px">
	<h1 id="expire">&nbsp;</h1>
    <table>
        <tr>
            <td style="text-align:center">
                <form id="checkForm" action="index.php" method="post">
                    <div class="g-recaptcha" style="margin: 0 auto; display: table" data-callback="sendForm" data-sitekey="<?php echo getReCaptchaPublicKey(); ?>"></div><input name="id" type="hidden" value="<?php echo $uuid; ?>">
                </form>
            </td>
        </tr>
        <tr>
            <td style="text-align:center"><button id="refresh">Rafraichir</button></td>
        </tr>
    </table>
    <table onclick="openSource()" style="cursor: pointer">
        <tr>
            <td style="text-align:center">OpenSource by&nbsp;&nbsp;<img src="openSource.png"></td>
        </tr>
    </table>
    <script>
    document.getElementById("refresh").addEventListener("click", function() {
        window.location.href = window.location.pathname + "?id=<?php echo $uuid; ?>&cache=" + Date.now();
    });

    function sendForm(token) {
        document.getElementById("checkForm").submit();
    }
    </script>
<?php
                    }
                }
                else { /// Database error (query UUID)
                    traceError("query UUID");
                    $error = 2;
                }
            }
            if ($expired && $error == 0) { //////////////////////////////////////////////////////////////////// Expired / Invalid
?>
</head>
<body>
    <table>
        <tr>
            <td style="text-align:center"><h1>Expiré ou invalide !</h1></td>
        </tr>
        <tr>
            <td style="text-align:center"><img src="expired.png"></td>
        </tr>
    </table>
    <table onclick="openSource()" style="cursor: pointer">
        <tr>
            <td style="text-align:center">OpenSource by&nbsp;&nbsp;<img src="openSource.png"></td>
        </tr>
    </table>
<?php
            }
        }
        else { ////////////////////////////////////////////////////////////////////////////////////////////////////////// Welcome
?>
    <script src="https://www.google.com/recaptcha/enterprise.js" async defer></script>
</head>
<body>
    <div id="intro" class="overlay"><p><b>Mode d'emploi</b><br><br>
Ce site vous permet de créer un lien personnalisé, afin de vérifier qu'un internaute avec qui vous échangez est bien une personne réelle et non un robot (une IA utilisant un compte). L'idée étant de proposer à cet internaute d'ouvrir le lien, pour qu'il s'identifie comme n'étant pas un robot grâce à l'outil de détection <a href="https://cloud.google.com/security/products/recaptcha" target="_blank">reCAPTCHA v2</a> de Google.<br><br>
L'utilisation du site se fait en 3 étapes.<br><br>
<b>1. Création d'un lien</b><br><br>
Créez votre lien, après avoir choisi un délai d'expiration allant de 1 minute à 1/2 heure. Ce délai est lié au contexte d'utilisation, car il vous est recommandé de faire cette demande dans un contexte particulier, un contexte favorable où l'internaute interagit avec vous en direct. Proposer de vérifier qu'il s'agit bien d'une personne réelle à ce moment-là, est idéal, car il lui sera difficile d'ignorer cette demande. Ce délai, ajouté de 30 secondes (le temps de formuler votre demande), est sensé suffire pour lui laisser le temps de s'identifier.<br><br>
Un robot étant toujours géré par une vraie personne, l'intérêt de ce délai restreint et contraignant, est justement d'éviter que cette personne ait le temps d'être informé de cette demande, et s'identifie elle-même comme n'étant pas un robot.<br><br>
<b>2. Partage du lien</b><br><br>
Copiez et partagez le lien avec l'internaute testé, mais uniquement à lui (par message privée par exemple). Si ce n'est pas le cas, il vous sera alors difficile de savoir si celui qui s'est identifié, est bien l'internaute que vous souhaitiez tester. En effet, le lien personnalise une demande à travers un identifiant unique, présent dans l'URL du lien, mais n'a aucun rapport avec l'internaute testé. Autrement dit, n'importe qui qui ouvrirait ce lien, pourrait s'identifier lui-même comme n'étant pas un robot.<br><br>
<b>3. Contrôle du lien</b><br><br>
Pour contrôler si l'internaute testé s'est identifié comme étant une vraie personne, il vous suffit d'ouvrir le lien et de voir ce qu'il en est :<br><br>
&#9632; Si un décompte du délai et le reCAPTCHA avec la coche "Je ne suis pas un robot" est présent, c'est qu'il ne sait toujours pas identifié. Un bouton "Rafraichir" permet d'actualiser, de mettre à jour ce contrôle.<br>
&#9632; Si la mention "Je suis une personne !" s'affiche, c'est que l'internaute s'est bien identifié comme étant une personne réelle. Pour des raisons techniques, cette mention restera affichée durant 24 heures suivant la création du lien.<br>
&#9632; Si la mention "Expiré ou invalide" s'affiche, c'est que le délai défini a expiré (ou que 24 heures se sont écoulées).<br><br>
En espérant que ce site vous soit utile.<br><br>
<u>Transparence:</u><br><br>
Afin de rassurer les internautes testés, et éviter tout refus d'utiliser le lien par crainte d'un virus, le site est totalement transparent, son code source étant disponible en OpenSource sous licence MIT, depuis le site Github : <a href="https://github.com/paul-laval/JeSuisUnePersonne" target="blank">https://github.com/paul-laval/JeSuisUnePersonne</a><br><br><button id="close" style="margin-left: 270px; margin-top: 25px;">Fermer</button></p></div>
        <form id="createForm" action="index.php" method="post">
        <table>
            <tr>
                <td>Délai d'expiration :</td>
                <td style="text-align:right">
                    <select name="delay" required>
                        <option value="1">1 min</option>
                        <option value="2">2 min</option>
                        <option value="5" selected="selected">5 min</option>
                        <option value="10">10 min</option>
                        <option value="15">15 min</option>
                        <option value="30">1/2 heure</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><div class="g-recaptcha" data-sitekey="<?php echo getReCaptchaPublicKey(); ?>" data-action="create" data-callback="onReCaptchaValidated"></div></td>
                <td style="vertical-align:bottom; text-align:right; padding-bottom:9px"><button type="submit">Créer le lien</button></td>
            </tr>
        </table>
    </form>
    <table onclick="openSource()" style="cursor: pointer">
        <tr>
            <td style="text-align:center">OpenSource by&nbsp;&nbsp;<img src="openSource.png"></td>
        </tr>
    </table>
    <div id="info" class="about"><u>Mode d'emploi</u></div>
    <div id="snackbar">Êtes vous un robot ?</div>
    <script>
        var reCaptchaValidated = false;

        document.getElementById("createForm").addEventListener("submit", function(e) {
            e.preventDefault();
            if (!reCaptchaValidated) {

                var snackBar = document.getElementById("snackbar");
                snackBar.className = "show";
                setTimeout(function() { snackBar.className = snackBar.className.replace("show", ""); }, 2000);
                return;
            }
            this.submit();
        });

        document.getElementById("info").addEventListener("click", function() {
            document.getElementById("intro").style.display = "flex";
        });
        document.getElementById("close").addEventListener('click', function(e) {
            document.getElementById("intro").style.display = "none";
        });

        function onReCaptchaValidated(token) {
            reCaptchaValidated = Boolean(token);
        }
    </script>
<?php
        }
    }
    mysqli_close($connect);
}
if ($error != 0) { ///////////////////////////////////////////////////////////////////////////////////// Database/reCAPTCHA error
?>
</head>
<body>
    <table>
        <tr>
            <td style="text-align:center"><h1><?php echo ($error == 1)? "Erreur en DB !":"Erreur de CAPTCHA !" ?></h1></td>
        </tr>
        <tr>
            <td style="text-align:center"><img src="expired.png"></td>
        </tr>
    </table>
    <table onclick="openSource()" style="cursor: pointer">
        <tr>
            <td style="text-align:center">OpenSource by&nbsp;&nbsp;<img src="openSource.png"></td>
        </tr>
    </table>
<?php
}
?>
</body>
</html>