## Instructions

1. Clone the repository:

`git clone https://github.com/il-yes/tkt.git`

2. Switch to the repository folder:

`cd tkt`

3. Build and start the containers:

`docker-compose up -d --build`

If you want to destroy the containers in order to rebuild them right after:

`docker-compose down --remove-orphans --volumes`

4. Connect to the application's container:

`docker exec -ti tkt_php-fpm_1 bash`

5. Switch to the application folder:

`cd application`

6. Install the dependencies:

`composer install` or `composer update -W`

7. Create the database:

`bin/console doc:sch:cre`

8. Apply the migrations and the fixtures:

`bin/initialize`

9. Launch the built-in Symfony server:

`symfony serve -d`

9. On your web browser, go to :

`http://localhost:5000`