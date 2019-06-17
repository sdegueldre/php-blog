# Php-blog

This is simple blogging platform that we made as a learning project at [BeCode](https://www.becode.org/). This is our first website where we wrote both the front-end and the back-end.
The blog alllows you to sign up, log in, and comment on articles.
There are 3 levels of user permissions : Regular user, Author and Admin.
Authors and admins can create and edit articles, only admins can delete them.
Admins have access to a dashboard to create and edit categories for articles, and manage user permissions.


## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes.

### Prerequisites

You need to clone this repository

```
git clone git@github.com:sdegueldre/php-blog.git
```

### Run it locally

In the newly created folder, run

```
npm run dev
```
To run in in production, we made a deploy script

```
./deploy.sh <ssh endpoint>
```

The script was written to deploy it in apache userdirs, so it's probably not very robust, it expects the ssh authentication to be handled by public key, password authentication will probably break it.
It will create a public_html folder at the ssh root and copy all files in there.
You will need to manually set the correct credentials for the database by opening the settings.php file located in the src subdirectory

```
ssh <ssh endpoint>
vi public_html/src/settings.php
```

## Built With

* [Slim](http://www.slimframework.com/) - A php micro-framework, used for routing
* [Twig](https://twig.symfony.com/) - Templating engine
* [PostgresSQL](https://www.postgresql.org/) - Database

## License

The project uses a Bootstrap template from https://startbootstrap.com/



## Authors

* **Julien Caramazza**
* **Simon Fraipont**
* **Michael Lambrechts**
* **Samuel Degueldre**
