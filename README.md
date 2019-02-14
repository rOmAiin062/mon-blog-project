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


#### Architecture du projet

- 7 controllers : 
	- IndexController (Accueil du blog)
	- NewController (Pour créer un nouvel article)
	- ArticleController (Pour consulter en détail un article)
	- EditController (Pour modifier un article)
	- DeleteController (Pour supprimer un article)
	- SecurityController (Pour gérer la connexion / création de compte utilisateur)


- 5 classes de tests : 
	- IndexControllerTest
	- NewControllerTest
	- ArticleControllerTest
	- EditControllerTest
	- DeleteControllerTest

