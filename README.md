Documentation Partiel Docker 

# RECUPERER LE PROJET

git clone https://github.com/Teshanii/partiel_docker.git
cd partiel_docker

# DEMARER L'APPLICATION

docker compose up -d
-> Cette commande va construire les images et démarrer les 3 conteneurs.

Accéder à l'application : http://localhost:8080

# ARCHITECTURE
L'application est composée de 3 services qui communiquent entre eux via un réseau Docker bridge nommé net_biblio.

# DATABASE

On utilise l'image officielle MariaDB 10.5
Initialise automatiquement la base de données avec le fichier init.sql au premier démarrage
Les données sont dans un volume Docker nommé data_db, ce qui signifie que si on supprime et recrée le conteneur, les données ne sont pas perdues
Le mot de passe est lu depuis /run/secrets/db_password et non en clair dans le docker-compose.yml

# BACK

On utilise l'image PHP 8.0 avec Apache construite depuis le Dockerfile dans le dossier back/
Il reçoit les requêtes HTTP et interroge la base de données
Se connecte à la base via le nom de service database qui est résolu automatiquement par le DNS interne de Docker
Lit le secret directement en PHP avec file_get_contents('/run/secrets/db_password'), ce qui évite d'avoir besoin d'un script intermédiaire contrairement à Node.js
Ne démarre qu'une fois que la base de données est prête grâce au healthcheck
Exposé sur le port 3000 de la machine hôte

# FRONT

On utilise l'image Nginx construite depuis le Dockerfile dans le dossier front/
Il donne  une page HTML statique qui appelle le back sur http://localhost:3000
Exposé sur le port 8080 de la machine hôte


# RESEAU

Un seul réseau bridge net_biblio est créé. Tous les conteneurs sont connectés à ce réseau. Cela signifie que le back peut appeler la base de données en utilisant simplement le nom database sans connaître son adresse IP. Le front en revanche fait ses appels depuis le navigateur du client, qui lui ne fait pas partie du réseau Docker, donc il utilise localhost:3000.

# VOLUME

Un volume nommé data_db est monté sur /var/lib/mysql dans le conteneur de la base de données. Cela permet de conserver les données même si le conteneur est supprimé.
Pour vérifier la persistance :
# Supprimer le conteneur de la base de données
docker compose down

# Relancer
docker compose up -d
-> Les données sont toujours là

# Tester la communication entre les conteneurs

Vérifier que le back répond :
curl http://localhost:3000/index.php
On doit recevoir un JSON avec la liste des livres.

Se connecter dans le conteneur back et pinger la base :
docker exec -it back_php bash
ping database

Vérifier les logs si quelque chose ne fonctionne pas :
docker logs db_maria
docker logs back_php
docker logs front_html

# SECRETS

Les mots de passe ne sont jamais écrits en clair dans le code ou dans les fichiers de configuration. Ils sont stockés dans des fichiers locaux et montés dans les conteneurs via le mécanisme de secrets Docker dans /run/secrets/. MariaDB supporte nativement la lecture de secrets avec les variables MARIADB_PASSWORD_FILE et MARIADB_ROOT_PASSWORD_FILE. PHP les lit directement avec file_get_contents().

# IMAGES DOCKER HUB

Les images sont disponibles sur Docker Hub :
https://hub.docker.com/r/teshani23/partiel_docker-back
https://hub.docker.com/r/teshani23/partiel_docker-front

# Construction et démarrage
docker compose up -d --build

docker compose up : démarre tous les services du docker-compose.yml
-d : en arrière-plan (detached mode), le terminal reste libre
--build : force la reconstruction des images Docker avant de démarrer


# Arrêter l'application
docker compose down
-> Arrête et supprime tous les conteneurs
-> Les volumes (données de la DB) sont conservés

docker compose down -v
-> Même chose mais supprime aussi les volumes (les données de la DB sont perdues)


# Vérifier que tout tourne
docker ps
->Liste tous les conteneurs actifs
->Permet de vérifier que db_maria, back_php et front_html sont bien Up


# Voir les logs
docker compose logs
->Affiche les logs de tous les services

docker compose logs back
->Affiche les logs uniquement du service back


# Pousser les images sur Docker Hub
docker build -t teshani23/biblio-back:latest ./back
docker push teshani23e/biblio-back:latest

docker build -t teshani23/biblio-front:latest ./front
docker push teshani23/biblio-front:latest

->docker build : construit l'image
-> -t : donne un nom/tag à l'image
-> docker push : envoie l'image sur Docker Hub
