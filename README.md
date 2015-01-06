## Laravel 5 example ##

**Laravel 5 example** is a tutorial application. It's a work in progress because Laravel 5 is still alpha.

Fixed to commit 8e80fd5b180440c1b0feb82a1b71ac39785ca3ce before remove of annotations that must be taken over as 3rd package.

### Installation ###

* `git clone https://github.com/bestmomo/laravel5-example.git projectname`
* `cd projectname`
* `composer instal`
* `php artisan key:generate`
* Create a database and inform *.env*
* `php artisan migrate` to create tables
* `php artisan db:seed` to populate tables
* `php artisan route:scan` to create routes
* `php artisan event:scan` to create events
* Inform *config/mail.php* for email sends

### Include ###

* [HTML5 Boilerplate](http://html5boilerplate.com) for front architecture
* [Bootstrap](http://getbootstrap.com) for CSS and jQuery plugins
* [Font Awesome](http://fortawesome.github.io/Font-Awesome) for the nice icons
* [Highlight.js](https://highlightjs.org) for highlighting code
* [Startbootstrap](http://startbootstrap.com) for the free templates
* [CKEditor](http://ckeditor.com) the great editor
* [Filemanager](https://github.com/simogeo/Filemanager) the easy file manager

### Features ###

* Home page
* Custom Error Page 404
* Authentication (registration, login, logout, password reset)
* Users roles : administrator (all access), redactor (create and edit post, upload and use medias in personnal directory), and user (create comment in blog)
* Blog with comments
* Search in posts
* Tags on posts
* Contact us page
* Admin dashboard with new messages, users, posts and comments
* Users admin (roles filter, show, edit, delete, create)
* Messages admin
* Posts admin (list with dynamic order, show, edit, delete, create)
* Medias gestion

### Packages included ###

* illuminate/html

### Tricks ###

To test application the database is seeding with users :

* Administrator : email = admin@la.fr, password = admin
* Redactor : email = redac@la.fr, password = redac
* User : email = walker@la.fr, password = walker
* User : email = slacker@la.fr, password = slacker