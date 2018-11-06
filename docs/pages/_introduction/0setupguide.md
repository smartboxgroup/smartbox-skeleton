---
name: Installation and Setup Guide
title: Installation and Setup Guide
permalink: introduction/installation
---

# Installation and Setup Guide for the Smartbox Integration Framework bundle

## Configure

To get the framework up and running we will make the following assumptions regarding software that we will be using:
* **Queuing system**: RabbitMQ
* **Database**: MySql
* **Process Control**: Supervisor
* **WebServer**: Apache + PHP 7.0



### Composer Dependencies
To simplify our setup we will install all the related Smartbox bundles
Add the following to composer.json and run `composer install`

```json
"smartbox/core-bundle": "^1.0.0",
"smartbox/integration-framework-bundle": "^1.20.4",
"smartbox/camel-config-bundle": "^1.0.0",
"smartbox/besimple-soap": "^1.1.1"
```

To update to the latest smartbox bundle versions please run 

``` composer update smartbox/integration-framework-bundle smartbox/core-bundle smartbox/camel-config-bundle smartbox/besimple-soap```



## Sample Setup for System


### Webserver Setup

Install all the necessary dependencies on Ubuntu: Apache, MySql and PHP Stack.

```bash
sudo apt-get install -y mysql-server mysql-client
sudo apt-get install -y apache2
sudo apt-get install -y php7.0 libapache2-mod-php7.0 php7.0-cli php7.0-common php7.0-mbstring php7.0-gd php7.0-intl php7.0-xml php7.0-mysql php7.0-mcrypt php7.0-zip php7.0-dev
sudo apt-get install -y php7.0-curl php7.0-xml php7.0-soap php-apcu php-apcu-bc

```



### RabbitMQ

Install and configure RabbitMQ. This will:
* Setup RabbitMQ on Ubuntu
* Add an administrator called "mel" with the password "mel".
* Allow remote access to the management console.
* And finally it will add rabbitmq.local to /etc/hosts


```bash 
sudo apt-get install rabbitmq-server
rabbitmq-plugins enable rabbitmq_management
rabbitmq-plugins enable rabbitmq_stomp
rabbitmqctl add_user mel mel
rabbitmqctl set_user_tags mel administrator
rabbitmqctl set_permissions -p / mel "." "." ".*"
sudo touch /etc/rabbitmq/rabbitmq.config
echo "[{rabbit, [{loopback_users, []}]}]." | sudo tee /etc/rabbitmq/rabbitmq.config
echo -e "\n127.0.0.1	rabbitmq.local" | sudo tee -a /etc/hosts
```

### Adding Workers with Supervisor

Install Supervisor on Ubuntu:

```bash
sudo apt-get install supervisor
```

#### Create a worker configuration:

To create a consumer that will be controlled by Supervisor, we can add a .conf file to the supervisor configurations directory. For example:
 
    /etc/supervisor/conf.d/SuperHardWorker.conf
 
which would contain contents as follows:

```bash
[program:consumer_db_queue]
process_name = %(program_name)s_%(process_num)02d
autostart = true
autorestart = true
numprocs=10
directory=/var/www/skeleton/
command = php app/console smartesb:consumer:start queue://main/persist --killAfter=200
stdout_logfile = /home/mel/stdout.log
stderr_logfile = /home/mel/stderr.log
startretries = 10
user = mel
 ```
Note that the value `numprocs=10` means that supervisor will attempt to always run 10 instances/processes of the program.
