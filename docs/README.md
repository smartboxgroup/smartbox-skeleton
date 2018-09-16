Installation and Setup Notes
========


## Apache, MySql and PHP

## RabbitMQ


## Supervisor

To create a consumer that will be controlled by supervisord, we can add a .conf file to the supervisor configurations directory. For example:

```/etc/supervisor/conf.d/SuperHardWorker.conf```

which would contain contents as follows:

```
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
Note that the value ```numprocs=10``` means that supervisor will attempt to always run 10 instances/processes of the program.

