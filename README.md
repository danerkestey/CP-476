# CP-476
Simple CRUD app for our Internet Computing class that uses the LAMP stack.

# Guide for Setting up and Running the Application using WSL2 with Ubuntu

This guide assumes you have already installed Windows Subsystem for Linux (WSL2) with Ubuntu, Apache, PHP, and MySQL.

## 1. Start MySQL Server

To start your MySQL service, open your WSL2 terminal and type the following command:

```bash
sudo service mysql start
```

To verify that the MySQL service has started correctly, you can check the status:

```bash
sudo service mysql status
```

The system should report that the MySQL service is running.

## 2. Start Apache Server

In the same terminal, you can start the Apache service by typing:

```bash
sudo service apache2 start
```

You can confirm that the service is running correctly:

```bash
sudo service apache2 status
```

The system should report that the Apache service is running.

## 3. Create and Populate the Database

With the MySQL service running, you can now create and populate your database tables. This guide assumes that you have already created `createTables.php` and `populateTables.php` scripts.

Navigate to the directory containing your PHP scripts in the terminal and run the following commands:

```bash
php php/utils/createTables.php <root // or whatever is yours> <password // or whatever is yours>

php php/utils/populateTables.php  <root // or whatever is yours> <password // or whatever is yours>
```

Please ensure that the paths to your PHP scripts are correct if they are not located in the current directory.

## 4. Access the Web Application

Your PHP files should be located in Apache's default document root, which is typically `/var/www/html`. 

You can then open a web browser and type the following URL to access your login page:

```plaintext
http://localhost/login.php
```

You will now be able to log into the application by using your MySQL credentials. In this scenario, the username would be `root` and the password `password`.

## Notes

- Ensure that the project is located in your /var/www/html directory. For example, in this demo, the path of the project is: `/var/www/html/CP-476` If you know the files are there, but still cannot `cd` into the directory, you may need to change the permissions of the directory using:

```bash
sudo chown -R your_username:your_username /var/www/html

cd /var/www/html
```

- Remember to replace `password` with the actual password you set for your MySQL `root` user.
- If the Apache or MySQL services are not starting, you might need to troubleshoot the permissions or check the configuration files for Apache and MySQL.
- If you have installed MySQL locally and are not using a password for the `root` user, you may need to modify the PHP files to remove or change the password fields.
- Always make sure your WSL2 instance is updated using `sudo apt update && sudo apt upgrade`.
- If you are unable to access your PHP pages via a browser, ensure that your firewall settings are not blocking the connection and that the Apache service is running.