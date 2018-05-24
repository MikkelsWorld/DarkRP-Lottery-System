<?php
include("settings.php");

/*
Create the lottery numbers table.
*/
$query = "CREATE TABLE lottery_numbers (
			id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
			number varchar(50) NOT NULL,
			drawn TIMESTAMP )";

$query = $database->query($query);

/*
Purchased rows of lottery numbers.
*/
$query = "CREATE TABLE lottery_rows (
			id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
			name varchar(255),
			steamid varchar(255) NOT NULL,
			number varchar(50) NOT NULL)";

$query = $database->query($query);

/*
Pending payouts table.
*/
$query = "CREATE TABLE lottery_pending (
			id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
			steamid varchar(255) NOT NULL,
			payout integer(11))";

$query = $database->query($query);

/*
Past lottery winners.
*/
$query = "CREATE TABLE lottery_winners (
			id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
			steamid varchar(255),
			name varchar(255),
			payout integer(11))";

$query = $database->query($query);

/*
Create pot table
*/
$query = "CREATE TABLE lottery_pot (
			id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
			name varchar(255),
			sysname varchar(255),
			payout integer(11))";

$query = $database->query($query);

$query = "INSERT INTO lottery_pot (name, sysname, payout) VALUES ('Jackpot', 'jackpot', '1000000')";
$query = $database->query($query);

$query = "INSERT INTO lottery_pot (name, sysname, payout) VALUES ('2nd Place', '2ndplace', '150000')";
$query = $database->query($query);

$query = "INSERT INTO lottery_pot (name, sysname, payout) VALUES ('3rd Place', '3rdplace', '50000')";
$query = $database->query($query);

echo "The tables has been added to your database, please delete this script. Do not run it again.";