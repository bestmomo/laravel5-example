## Laravel 5 example ##

For Laravel 5.3 improved version look at [this repository](https://github.com/bestmomo/laravel5-3-example).

**Laravel 5 example** is a tutorial application for Laravel 5.2 (in french [there](http://laravel.sillo.org/laravel-5/)).

### Installation ###

* `git clone https://github.com/bestmomo/laravel5-example.git projectname`
* `cd projectname`
* `composer install`
* `php artisan key:generate`
* Create a database and inform *.env*
* `php artisan migrate --seed` to create and populate tables
* Inform *config/mail.php* for email sends
* `php artisan vendor:publish` to publish filemanager
* `php artisan serve` to start the app on http://localhost:8000/

Another cool way to install it is to upload [this package](http://laravel.sillo.org/tuto/installable.zip), unpack it in your server folder, and just launch it and follow the installation windows. It has been created with my [laravel installer package](https://github.com/bestmomo/laravel-installer). Anyway you'll have to set the email configuration.

### Nitrous Quickstart ###

Create a free development environment for this Laravel 5 example project in the cloud on [Nitrous.io](https://www.nitrous.io) by clicking the button below.

<a href="https://www.nitrous.io/quickstart">
  <img src="https://nitrous-image-icons.s3.amazonaws.com/quickstart.png" alt="Nitrous Quickstart" width=142 height=34>
</a>

In the IDE, start Laravel 5 Example via `Run > Start Laravel 5 Example` and access your site via `Preview > 8000`.

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
* Authentication (registration, login, logout, password reset, mail confirmation, throttle)
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
* Localisation

### Packages included ###

* laravelcollective/html
* bestmomo/filemanager

### Tricks ###

To test application the database is seeding with users :

* Administrator : email = admin@la.fr, password = admin
* Redactor : email = redac@la.fr, password = redac
* User : email = walker@la.fr, password = walker
* User : email = slacker@la.fr, password = slacker
