# shell.nix
{ pkgs,
  php,
  composer,
  mysql
  }:
let

in pkgs.mkShell {
  name = "lend-engine-app";
  packages = [
    php
    composer
    mysql

    pkgs.killall
  ];
  buildInputs = with pkgs; [
   ];
  shellHook = ''

  export SYMFONY_ENV=dev
  export LE_SERVER_NAME=dev
  export SYMFONY__POSTMARK_API_KEY=xxx
  export DEV_DB_USER=root
  export DEV_DB_PASS=root
  export CLOUDAMQP_URL=xxx

  export SYMFONY__AWS_KEY=xxx
  export SYMFONY__AWS_SECRET=xxx


  ####################################################################
  # Create a diretory for the generated artifacts
  ####################################################################
  mkdir -p .nix-shell
  export NIX_SHELL_DIR=$PWD/.nix-shell

  ####################################################################
  # Put the MySQL databases in the project diretory.
  ####################################################################
  export MYSQL_DATA=$NIX_SHELL_DIR/db

  trap \
      "
        ######################################################
        # Stop MySQL
        ######################################################

        killall mysqld

        ######################################################
        # Delete `.nix-shell` directory
        # ----------------------------------
        # The first  step is going  back to the  project root,
        # otherwise `.nix-shell`  won't get deleted.  At least
        # it didn't for me when exiting in a subdirectory.
        ######################################################

        cd $PWD
        rm -rf $NIX_SHELL_DIR
      " \
      EXIT

  if ! test -d $MYSQL_DATA
    then

      ######################################################
      # Init PostgreSQL
      ######################################################
      mysqld --initialize-insecure --datadir=$MYSQL_DATA
    fi
  
  mysqld_safe --datadir=$MYSQL_DATA --socket=$MYSQL_DATA/mysql.sock 2>&1 > $NIX_SHELL_DIR/mysql.log &
  sleep 2

  mysql -S $MYSQL_DATA/mysql.sock -u root -e 'CREATE DATABASE _core CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;'
  mysql -S $MYSQL_DATA/mysql.sock -u root -e 'CREATE DATABASE unit_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;'

  mysql -S $MYSQL_DATA/mysql.sock -u root < ./tenant_setup.sql

  mysql -S $MYSQL_DATA/mysql.sock -u root -e "UPDATE _core.account SET status = 'DEPLOYING' where stub = 'unit_test';"
  mysql -S $MYSQL_DATA/mysql.sock -u root -e "SET password = password('root');"
  

  cat << EOF
    # Run Migrations
    php bin/console doctrine:migrations:migrate --no-interaction

    # Start a development server on localhost:8000
    php bin/console server:run

    # Provision a new tenant to test stuff
    firefox http://unit_test.localhost:8000/deploy
  EOF
  unset shellHook
  ''; 
} 
