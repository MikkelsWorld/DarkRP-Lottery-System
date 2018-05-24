1. Add the contents of the game folder, to your gameservers addons folder.

2. Open the file located in darkrp_lottery/lua/autorun/sv_lottery.lua

3. Enter in your mysql details, host, username, password etc. Set your desired values to increase the difficulty of winning the pot. The bigger spectrum and more numbers will increase the difficulty.

4. Upload the web folder to your web site, its best to put the lottery.cron.php file outside the public_html folder. As it can be run from anywhere. Edit settings.php to match the servers config for mysql and numbers.

5. Rename the sample-settings.php to settings.php and fill out the same mysql settings as for the server.

6. Navigate to the setup.php script, this will generate the tables.

7. Now setup a cron on cPanel to run every 24 hours, as an example and it will handle the rest..