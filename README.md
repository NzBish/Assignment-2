# Simple MVC application example

This is an example of a a simple application designed using
Model-View-Controller pattern.  The app requires a number of services, defined in `docker-compose.yml` and you can run it using docker-compose.

## docker-compose

Web applications generally require multiple components or *services* (e.g. PHP interpreter, web server, DBMS).

Each *service* can be placed in its own Docker container. Containers required to run an app are called a stack.

Instead of managing all containers individually, we use `docker-compose` - a tool that allows us to easily launch and manage stacks of containers.

Docker-compose launches all the required *services* to run our web application. Services are defined by a configuration file `docker-compose.yml`. Have a look at this file. You will see 4 services defined: **db** (MySQL database), **web** (Apache with PHP interpreter), **composer** (a dependency manager for PHP) and **myadmin** (PhpMyAdmin db administration tool). You will notice that **db**, **composer**  and **myadmin** services are launched from standard Docker images that will be downloaded from DockerHub; however, **web** service uses a custom image built from `Dockerfile.extensions` dockerfile. I will explain the reason for this in class. Review `docker-compose.yml` file in conjunction with the [docs](https://docs.docker.com/compose/compose-file/) to understand how the stack is defined. You will see that most options for each service correspond to the options of `docker run` command.

`docker-compose` will look for `docker-compose.yml` file in the current directory, so make sure you execute the command from the right directory, when you bring the stack up and also down.

To bring the stack up, open terminal and navigate to this directory.

Execute: `docker-compose up -d`

This will start containers for all the services defined in `docker-compose.yml` file and create a custom network to interconnect them. `-d` flag indicates that containers should be launched in the background, so you should get back to the terminal prompt straight away. However, you can see the output that services running in those containers produce by executing `docker-compose logs` - this will list all the outputs from the moment they launched to now and exit back to the terminal. You can add `-f` flag to follow the logs as they are produced. To terminate this command, just press ctrl-c, but it is good to keep these logs being printed in a separate terminal window to observe what the web server is serving or if there are any errors. It will take a while for MySQL to initialise and for **composer** to download all the project dependencies, so either wait a few minutes before opening the browser or watch the logs until these processes are finished.

You can also check that the containers are running by executing `docker ps`.

Now you can navigate your browser to `http://localhost:8000` (It will take a few seconds for all services to get initialised properly so don't do it straight away) and you should see the web app running. If you want to watch only web server's logs, you can specify the service: `docker-compose logs -f web` (again ctrl-c to exit)..

You can also open `http://localhost:8080` to access PhpMyAdmin tool and explore the database. Alternatively, you can launch the command line mysql client on the **db** service: `docker-compose exec db mysql -proot`.


Once you're finished with the application, you can 'bring the stack down' with `docker-compose down`. This will stop and remove the containers. Run `docker ps` again and you will see - the containers have now disappeared. Be aware that the database is being written to a file inside the db service, so it will not persist `docker-compose down` command. However, the composer.lock file and vendor directories that **composer** creates are written to the mapped volume and, hence, your HDD and will persist along with all your code. You can safely delete these files, as they will be recreated when you run `docker-compose up -d` again or you can leave them be to speed up orchestration of the application stack (composer won't need to download any dependencies that already exist in vendor directory).

## PHP-MSQL application

This is a simple application that creates and deletes accounts.

Note that if the database does not exist, i.e. when you firs launch the stack, the application will create the required tables and populate them with sample data.

## Extras

### Composer

Composer is a dependency manager for PHP projects. Dependencies (additional PHP packages that this app requires to run) are defined in `composer.json` file. Composer will download and install those dependencies (and the dependencies of dependencies) into `vendor` directory. The key dependency for this project is `dannyvankooten/php-router` package, which is used to define routes and then  route the incoming requests to appropriate controllers - check out the code in `index.php` and `router.php`.

Two additional packages are installed: 

* `php\_codesniffer` - can be used to automatically check your source code against PSR-1 and PSR-12 recommendations. To run it, first bring the application stack up and then execute `docker-compose exec web vendor/bin/phpcs src --standard=PSR1,PSR12`.
* `phpDocumentor` - can be used to automatically generate HTML docs from the PHPDoc blocks in your code. You can run it with `docker-compose exec web vendor/bin/phpdoc -d src -t docs`. This will generate documentation in `docs` folder, which you can access in browser at `http://localhost:8000/docs/`.
