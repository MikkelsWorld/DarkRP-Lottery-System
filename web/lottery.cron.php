<?php

/*
Configuration:
Change this to the path, of your settings.php file.
Its not a good idea to keep this cronjob file public, as it can be ran from any browser.
So put it outside public_html.
*/
$settings = "settings.php";
require_once($settings);

/*
Generate numbers
*/
function randomGen($min, $max, $quantity) {
    $numbers = range($min, $max);
    shuffle($numbers);
    return array_slice($numbers, 0, $quantity);
}

/*
Finds the winners of each pot.
Args: Array of random numbers, the pot attempting to win, the database config, and the minimum match required to win.
*/
function findWinners($array, $pot, $database, $minMatch) {
    $array = explode(',', $array);
    $sql = "SELECT * FROM lottery_rows";
    $exe = $database->query($sql);
    $rows = $exe->fetch_all();

    //Create an empty winners array.
    $winners = array();

    foreach ($rows as $row) {
        $matches = 0;
        $numbers = explode(',', $row[3]);
        foreach ($numbers as $key => $value) {
            if (in_array($value, $array)) {
                $matches++;
            }
        }

        if ($matches >= $minMatch) {
            $winners[] = $row;
        }
    }

    if (count($winners)) {
        //Devide the pot.
        $potsize = "SELECT * FROM lottery_pot WHERE `sysname`='$pot'";
        $potsize = $database->query($potsize);
        $potsize = $potsize->fetch_all();
        $potsize = $potsize[0][3];

        $potsize = $potsize / count($winners);

        foreach ($winners as $winner) {
            //Insert into pending payouts table.
            $sql = "INSERT INTO lottery_pending (steamid, payout) VALUES ('$winner[2]', '$potsize') ";
            $exe = $database->query($sql);

            //Insert into winners table.
            $sql = "INSERT INTO lottery_winners (steamid, name, payout) VALUES ('$winner[2]', '$winner[1]', '$potsize') ";
            $exe = $database->query($sql);

            //Delete winning entry to avoid multiple counts.
            $sql = "DELETE FROM lottery_rows WHERE id='$winner[0]'";
            $exe = $database->query($sql);
        }

        //reset the pot, if we have a winner.
        $sql = "UPDATE lottery_pot SET `payout`='0' WHERE `sysname`='$pot'";
        $exe = $database->query($sql);
    }
}

/*
This function takes the randomly generated numbers, makes it into a human readable format x,x,x,x.
*/
function makeHumanReadable($array, $quantity) {
    $count = 1;
    $drawing = "";
    sort($array);
    foreach ($array as $number) {
        //Print out the number
        $print = $number;

        //Add comma behind all but last.
        if ($count !== $quantity) {
            $number .= ",";
        }

        //Increase count.
        $count++;

        //Add to string.
        $drawing .= $number;
    }

    return $drawing;
}

/*
This function will add the 2nd, and 3rd place pots to the jackpot.
Mainly so those pots wont, overtime grow to more than the jackpot.
*/
function combinePots($pot, $database, $resetValue) {
    $potsize = "SELECT * FROM lottery_pot WHERE `sysname`='$pot'";
    $potsize = $database->query($potsize);
    $potsize = $potsize->fetch_all();
    $potsize = $potsize[0][3];

    $jackpot = "SELECT * FROM lottery_pot WHERE `sysname`='jackpot'";
    $jackpot = $database->query($jackpot);
    $jackpot = $jackpot->fetch_all();
    $jackpot = $jackpot[0][3];

    $jackpot = $jackpot + $potsize;

    //Make sure we dont double the jackpot size, and that it doesn't go below the reset value.
    if ($pot !== "jackpot" or $potsize < $resetValue) {
        //Update new jackpot.
        $sql = "UPDATE lottery_pot SET `payout`='$jackpot' WHERE `sysname`='jackpot'";
        $exe = $database->query($sql);

        if ($potsize < $resetValue) {
            //Reset the pot, if its below reset value.
            $sql = "UPDATE lottery_pot SET `payout`='$resetValue' WHERE `sysname`='$pot'";
            $exe = $database->query($sql);
        }
    }
}

//Generate actual random numbers.
$numbers = randomGen($minNumber, $maxNumber, $quantity);

//Debugging numbers.
//$numbers = array(7, 4, 8, 9);

$readAble = makeHumanReadable($numbers, $quantity);

//Insert drawing into table
$sql = "INSERT INTO lottery_numbers (number) VALUES ('$readAble')";
$exe = $database->query($sql);

//Find the winners for the jackpot.
findWinners($readAble, "jackpot", $database, $minMatchJackpot);

//Find the winners for the jackpot.
findWinners($readAble, "2ndplace", $database, $minMatch2nd);

//Find the winners for the jackpot.
findWinners($readAble, "3rdplace", $database, $minMatch3rd);

//Now combine 2nd and 3rd place into the next jackpot to bump it a bit.
combinePots('jackpot', $database, $setJackpot);
combinePots('2ndplace', $database, $set2ndPlace);
combinePots('3rdplace', $database, $set3rdPlace);

//Delete losing rows.
$sql = "DELETE FROM lottery_rows";
$exe = $database->query($sql);