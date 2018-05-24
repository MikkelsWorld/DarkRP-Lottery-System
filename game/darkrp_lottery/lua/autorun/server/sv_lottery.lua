print("Booting DarkRP Lottery System.")
require("tmysql4")

--[[
MySQL settings.
]]
local MYSQL_HOST = "" -- MySQL host, this is usually an IP.
local MYSQL_USER = "" -- MySQL username.
local MYSQL_PASS = "" -- MySQL password for the user.
local MYSQL_DATA = "" -- MySQL database.
local MYSQL_PORT = 3306

--[[
The settings below MUST match the settings set in your cronjob.
]]
local rowPrice = 100 --The price for buying a row, and enter the lottery. The money will be added to the pot.
local quantity = 4 --This is the amount of numbers to generate, for a single row.
local minNumber = 1 --The smallest number to include during generation.
local maxNumber = 40 --The largest number to include during generation.
local advertTimer = 5 --Amount in minutes to display the add in chat.

//Setup networking strings
util.AddNetworkString("darkrp_lottery_drawgui")
util.AddNetworkString("darkrp_lottery_buy_rows")

local db, err = tmysql.initialize(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DATA, MYSQL_PORT)

if (err) then
    print("Error connecting to MySQL:")
    ErrorNoHalt(err)
else
	//Generate the numbers.
	function genLotteryNumbers()
		numbers = {} 

		while(table.Count(numbers) < quantity) do 
			numbers[math.random(1, maxNumber)] = true 
		end 

		numbers = (","):Implode(table.GetKeys(numbers))

		return numbers
	end

	//Gather the users current lottery tickets, runs when called.
	function getLotteryRows(ply, txt)
		if string.sub(string.lower(txt), 1, 8) == "!lottery" then
			local lotteryRows = {}

			local qs = string.format("SELECT number FROM `lottery_rows` WHERE steamid='%s'", ply:SteamID64())
			db:Query(qs, function(results)
				for k,v in pairs(results[1].data) do
					table.insert(lotteryRows, 0, v['number'])
				end

				net.Start("darkrp_lottery_drawgui")
				net.WriteString(rowPrice)
				net.WriteTable(lotteryRows)
				net.Send(ply)
				return false
			end)
		end
	end
	hook.Add("PlayerSay","getLotteryRows", getLotteryRows)

	//Buy rows
	function buyLotteryRows(len, ply)
		amount = math.floor(db:Escape(net.ReadString()))
		name = db:Escape(ply:Name())
		SteamID = db:Escape(ply:SteamID64())

		total = rowPrice * amount

		if (amount < 1) then
			ply:SendLua("GAMEMODE:AddNotify(\"You must buy more than one row.\", NOTIFY_ERROR, 5)")
			return false
		end

		if (ply:getDarkRPVar("money") >= total) then
			//Renerate the rows, loop using amount.
			for i=0,amount do
				numbers = genLotteryNumbers()
				//Generate the random numbers.
				local qs = string.format("INSERT INTO `lottery_rows` (name, steamid, number) VALUES ('%s', '%s', '%s')", name, SteamID, numbers)
				db:Query(qs, function()
				end)
			end

			//Update pots
			local addToJackPot = math.ceil(total * 0.7)
			local qs = string.format("UPDATE `lottery_pot` SET `payout` = `payout` + '%s' WHERE `id` = 1", addToJackPot)
				db:Query(qs, function()
			end)

			local addTo2ndPlace = math.ceil(total * 0.2)
			local qs = string.format("UPDATE `lottery_pot` SET `payout` = `payout` + '%s' WHERE `id` = 2", addTo2ndPlace)
				db:Query(qs, function()
			end)

			local addTo3rdPlace = math.ceil(total * 0.1)
			local qs = string.format("UPDATE `lottery_pot` SET `payout` = `payout` + '%s' WHERE `id` = 3", addTo3rdPlace)
				db:Query(qs, function()
			end)

			//Take his money!
			ply:addMoney(-total)

			ply:SendLua("GAMEMODE:AddNotify(\"Your purchase was registered, and rows generated.\", NOTIFY_GENERIC, 5)")

		else
			ply:SendLua("GAMEMODE:AddNotify(\"You cannot afford to buy that many rows.\", NOTIFY_ERROR, 5)")
		end
	end
	net.Receive("darkrp_lottery_buy_rows", buyLotteryRows)

	//Post chat advert.
	function advert(ply)
		timeA = advertTimer * 60
		timer.Create( "LotteryAdvert", timeA, 0, function() 
			for k, ply in pairs( player.GetAll() ) do
				ply:ChatPrint( "This server has a lottery system, type !lottery in chat to enter!" )
			end
		end )

		timeJ = advertTimer * 61
		timer.Create( "LotteryJackpotAdvert", timeJ, 0, function() 
			local qs = string.format("SELECT payout FROM `lottery_pot` WHERE id='1'")
			db:Query(qs, function(results)
				for k, v in pairs(results[1].data) do
					for key, ply in pairs( player.GetAll() ) do
						payout = v['payout']
						ply:ChatPrint("Jackpot is currently $" .. payout .. " type !lottery to buy rows!")
					end
				end
			end)
		end )

		//Check every 5 minute, if we have players who won on the server.
		timer.Create( "LotteryWinner", 300, 0, function() 
			print("Checking for lottery winners")
			local qs = string.format("SELECT * FROM `lottery_pending`")
			db:Query(qs, function(results)
				for k, v in pairs(results[1].data) do
					local ply = player.GetBySteamID64(v['steamid'])

					//If we found a ply, then his probably a winner.
					if (ply) then
						print("Lottery winner found! Adding money.")
						ply:addMoney(v['payout'])

						ply:SendLua("GAMEMODE:AddNotify(\"You won the lottery!\", NOTIFY_GENERIC, 5)")

						//Remove pending.
						local qs = string.format("DELETE FROM `lottery_pending` WHERE id='%s'", v['id'])
						db:Query(qs, function()
						end)
					end
				end
			end)
		end)
	end
	hook.Add( "Initialize", "Lottery Advert", advert )
end