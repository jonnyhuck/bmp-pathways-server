# Balfast Pathways Server
Server Scripts for the [Belfast Mobility Project]() GPS tracking App.

## Setup
The scripts are very simple and should work easily after the below setup:

You will require the [PDO-pgsql](http://php.net/manual/en/ref.pdo-pgsql.connection.php) extension for [PHP](http://php.net/). This is trivially installed with:

* Ubuntu: `sudo apt install php-pgsql`
* Mac (Homebrew): `brew install php56-pdo-pgsql`

Also, you will need to add a file called `connection.php` to the same directory as your scripts. This should contain the connection string for your repository in this form (replace the sections in `[]` with your own information):

```php
<?php
$connstr = 'pgsql:dbname=[DBNAME];host=localhost;user=[USRNAME];password=[PASSWORD]';
```

## Database Setup

The scripts are intended to work with a [Postgresql](https://www.postgresql.org/) database, using the following structure:

#### Table "public.gps_log"
Column | Type | Modifiers
---|---|---
 `id_log`   | `integer` | `not null default nextval('gps_log_id_log_seq'::regclass)`
 `id_user`  | `integer`                     | 
 `lng`      | `double precision`            | 
 `lat`      | `double precision`            | 
 `accuracy` | `double precision`            | 
 `log_time` | `timestamp without time zone` | 

Indexes |
---|
`"pk_idu" PRIMARY KEY, btree (id_log)` |

#### Table "public.users"

Column | Type | Modifiers
---|---|---
`id_user` | `integer` | `not null default nextval('users_id_user_seq'::regclass)`
`time_registered` | `timestamp with time zone` | `default now()`
`dummy` | `bit(1)` | 

Indexes |
---|
`"users_pkey" PRIMARY KEY, btree (id_user)` |

For convenience, we used the [PostGIS](http://postgis.net/) extensions to allow us to analyse the data easily.