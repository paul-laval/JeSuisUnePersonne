# JeSuisUnePersonne

Le site de détection de robots personnalisé

## Mode d'emploi

Ce site vous permet de créer un lien personnalisé, afin de vérifier qu'un internaute avec qui vous échangez est bien une personne réelle et non un robot (une IA utilisant un compte). L'idée étant de proposer à cet internaute d'ouvrir le lien, pour qu'il s'identifie comme n'étant pas un robot grâce à l'outil de détection [reCAPTCHA v2](https://cloud.google.com/security/products/recaptcha) de Google.

L'utilisation du site se fait en 3 étapes.

### 1. Création d'un lien

Créez votre lien, après avoir choisi un délai d'expiration allant de 1 minute à 1/2 heure. Ce délai est lié au contexte d'utilisation, car il vous est recommandé de faire cette demande dans un contexte particulier, un contexte favorable où l'internaute interagit avec vous en direct. Proposer de vérifier qu'il s'agit bien d'une personne réelle à ce moment-là, est idéal, car il lui sera difficile d'ignorer cette demande. Ce délai, ajouté de 30 secondes (le temps de formuler votre demande), est sensé suffire pour lui laisser le temps de s'identifier.

Un robot étant toujours géré par une vraie personne, l'intérêt de ce délai restreint et contraignant, est justement d'éviter que cette personne ait le temps d'être informé de cette demande, et s'identifie elle-même comme n'étant pas un robot.

### 2. Partage du lien

Copiez et partagez le lien avec l'internaute testé, mais uniquement à lui (par message privée par exemple). Si ce n'est pas le cas, il vous sera alors difficile de savoir si celui qui s'est identifié, est bien l'internaute que vous souhaitiez tester. En effet, le lien personnalise une demande à travers un identifiant unique, présent dans l'URL du lien, mais n'a aucun rapport avec l'internaute testé. Autrement dit, n'importe qui qui ouvrirait ce lien, pourrait s'identifier lui-même comme n'étant pas un robot.

### 3. Contrôle du lien

Pour contrôler si l'internaute testé s'est identifié comme étant une vraie personne, il vous suffit d'ouvrir le lien et de voir ce qu'il en est :

* Si le décompte du délai et le reCAPTCHA avec la coche "Je ne suis pas un robot" sont présents, c'est qu'il ne sait toujours pas identifié. Un bouton "Rafraichir" permet d'actualiser, de mettre à jour, ce contrôle.
* Si la mention "Je suis une personne !" s'affiche, c'est que l'internaute s'est bien identifié comme étant une personne réelle. Pour des raisons techniques, cette mention restera affichée durant 3 jours suivant la création du lien.
* Si la mention "Expiré ou invalide" s'affiche, c'est que le délai défini a expiré (ou que 3 jours se sont écoulées).
