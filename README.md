## Mon-blog-project

Ce projet a été développé dans le cadre de l'UE "Application WEB" du Master e-Services (Université de Lille).
Ce projet permet de mettre en place un blog simpliste : des auteurs, des articles.

#### Fonctionnalités implémentées
- Authentification (création d'un compte, connexion) 
- Liste des articles disponibles sur le blog
- Consultation en détail d'un article
- Depuis la page détail d'un article, si l'auteur de l'article est authentifié : 
	- Possibilité de modifier l'article
	- Possibilité de supprimer l'article
- Consultation du profil (si l'utilisateur connecté)
- Depuis la page profil :
	- Voir l'ensemble des articles de l'utilisateur
	- Changer son mot de passe (contraintes de taille sur le mdp >6 char)

#### Configuration de la base de données     

- Pour le bon fonctionnement du projet, il est nécessaire de mettre en place une base de données (MySQL)
    - Configurer la connexion à la BDD (dans le fichier .env)
      "DATABASE_URL=mysql://DBUsername:DBPassword@DBUrl:3306/DBName"
      
    - Charger le fichier sql fourni avec le projet à télécharger [ici](https://github.com/rOmAiin062/mon-blog-project/blob/master/mon-blog-project.sql)
    - La DB contient : table article, table user
    
    
#### Configuration de la base de données pour les tests
- Pour l'exécution des tests, il faut utiliser une base dédiées aux tests 
    - Configurer le fichier 'phpunit.xml.dist' à la racine du projet :
            
            <php>
                <ini name="error_reporting" value="-1" />
                <env name="APP_ENV" value="test" />
                <env name="SHELL_VERBOSITY" value="-1" />
        	    <env name="DATABASE_URL" value="mysql://DBUsername:DBPassword@DBUrl:3306/DBNameTEST" />
            </php>
    (doit être similaire à .env sauf nom de la DB différent)
    
    - Charger le fichier sql fourni avec le projet à télécharger [ici](https://github.com/rOmAiin062/mon-blog-project/blob/master/mon_blog_test.sql)
    - La DB test contient : table article, table user



#### Architecture du projet

- 7 controllers : 
	- IndexController (Accueil du blog)
	- NewController (Pour créer un nouvel article)
	- ArticleController (Pour consulter en détail un article / les articles d'un utilisateur)
	- EditController (Pour modifier un article)
	- DeleteController (Pour supprimer un article)
	- SecurityController (Pour gérer la connexion / création de compte utilisateur / changement de mot de passe)
	- UserController (Pour consulter la page de l'utilisateur)


- 6 classes de tests : 
	- IndexControllerTest
	- NewControllerTest
	- ArticleControllerTest
	- EditControllerTest
	- DeleteControllerTest
	- UserControllerTest
	


