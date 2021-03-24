# TEST DEV ALTERNANCE

### Cloner le projet en SSH ou HTTPS    
git clone git@github.com:madbrain67/test-dev-alternance.git  
git clone https://github.com/madbrain67/test-dev-alternance.git

### Installer les dépendances/vendor    
copier et renommer le fichier db.php.dist en db.php
Configurer le fichier db.php avec vos identifiants SQL  

### Base de données  
importer le fichier argonautes.sql

### Installer les dépendances/vendor    
composer install  
  
### lancer le projet
php -S localhost:8000 -t public 