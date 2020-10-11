P7 - API Bilemo

Environnement utilisé pour le développement de l'API :

- Symfony 5.1
- Composer 1.8.5
- Mamp 5.7
- PHP 7.3.8
- MySQL 5.7.26

Dépendances utilisées pour le projet :

- FOS/Rest-bundle
- jms/serializer-bundle
- lexik/jwt-authentication-bundle
- nelmio/api-doc-bundle
- willdurand/hateoas-bundle

Logiciel utilisé pour tester les appels aux endpoints : 

- Postman

Installation :

1/ Clonez ou téléchargez le repository GitHub :

git clone https://github.com/BFH59/api-bilemo.git

2/ Configurez vos variables d'environnement telles que la connexion à la base de données / Passphrase JWT et chemin des clés PEM (voir étape 6) dans le fichier .env.local qui devra être crée à la racine du projet en réalisant une copie du fichier .env.

3/ Installez les dépendances du projet avec Composer :

composer install

4/ Créez la base de données, tapez la commande ci-dessous en vous plaçant dans le répertoire du projet :

php bin/console doctrine:database:create

5/ Créez les tables de la BDD grace aux scripts de migrations :

 php bin/console doctrine:migrations:migrate
 
6/ Créez votre passphrase et vos clés privée/publique JWT (documentation:https://github.com/lexik/LexikJWTAuthenticationBundle/blob/master/Resources/doc/index.md#configuration) :

mkdir -p config/jwt

openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096

openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout
 
 
7/ (Optionnel) Créez le jeu de données test grace aux fixtures:

php bin/console doctrine:fixtures:load

8/ Félicitations ! Vous pouvez dorénavant utiliser l'API avec postman !

===================================================================================================

Documentation de l'API accessible via l'adresse : http://127.0.0.1:8888/api/doc (changer le domaine en fonction de votre hébergement distant/local)

URL à contacter pour obtenir un token et effectuer des appels à l'API :

Requete POST sur l'url : http://localhost:8888/api/login_check avec le contenu suivant (pour le client Orange) :

Body:

{
	"username": "orange@orange.fr",
	"password": "password"
}


