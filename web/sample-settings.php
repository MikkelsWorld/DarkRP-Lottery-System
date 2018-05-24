<?php

/*
Connect to the database.
Host, Username, Password, Database.
*/
$MYSQL_HOST = ""; //This is your MySQL host. Usually localhost or an IP.
$MYSQL_USER = ""; //Username for your mysql database.
$MYSQL_PASS = ""; //Password for the user.
$MYSQL_DATA = ""; //Database.

/*
Quantity, the amount of numbers to generate in reach row.
Keep it above the 3.
*/
$quantity = 4;

/*
This is the smallest number people can pick from.
*/
$minNumber = 1;

/*
This is the largest number people can pick from.
*/
$maxNumber = 40;

/*
Minimum amount of matches for an entry into the jackpot.
The lower the number the greater chance of a hit.
Jackpot should in term be hitting all numbers correctly.
*/
$minMatchJackpot = $quantity;

/*
Amount of numbers to hit, to reach 2nd place.
*/
$minMatch2nd = 3;

/*
Amount of numbers to hit, to reach 3rd place.
*/
$minMatch3rd = 2;

/*
On win reset the pots accordingly.
*/
$setJackpot = 1000000;
$set2ndPlace = 150000;
$set3rdPlace = 50000;

/*
DO NOT EDIT BELOW THIS LINE
---------------------------------------------------------------
*/

$database = new Mysqli($MYSQL_HOST, $MYSQL_USER, $MYSQL_PASS, $MYSQL_DATA);