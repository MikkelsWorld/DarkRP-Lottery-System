
//Draw the gui for managing the lottery.
function DrawGUI()
	//Read netstrings.
	local rowPrice = net.ReadString()
	local lotteryRows = net.ReadTable()

	local frame = vgui.Create("DFrame")
	frame:SetPos(ScrW()/2-200, ScrH()/2-150)
	frame:SetSize(400,300)
	frame:SetTitle("DarkRP lottery")
	frame:MakePopup()
	frame:ShowCloseButton(true)
	function frame:Paint(w, h)
		draw.RoundedBox(4, 0, 0, w, h, Color(10, 10, 10, 230))
		draw.RoundedBoxEx(4, 0, 0, w, 25, Color(150, 20, 20, 255), true, true, false, false)
	end

	local rowAmount = vgui.Create( "DNumSlider", frame )
	rowAmount:SetPos( 10, 220 )			// Set the position
	rowAmount:SetSize( 300, 100 )		// Set the size
	rowAmount:SetText( "Price per row " .. rowPrice .. "$" )	// Set the text above the slider
	rowAmount:SetMin( 1 )				// Set the minimum number you can slide to
	rowAmount:SetMax( 100 )				// Set the maximum number you can slide to
	rowAmount:SetDecimals( 0 )			// Decimal places - zero for whole number
	rowAmount:SetConVar( 1 ) // Changes the ConVar when you slide

	buy_button = vgui.Create("DButton", frame)
	buy_button:SetSize(100,30)
	buy_button:SetPos(290,260)
	buy_button:SetText("Buy Rows")
	buy_button.DoClick = function()
		net.Start("darkrp_lottery_buy_rows")
		net.WriteString(rowAmount:GetValue())
		net.SendToServer()
		frame:Close()
	end
	
	local rows = vgui.Create( "DListView", frame )
	rows:SetMultiSelect( false )
	rows:SetPos(10, 30)
	rows:SetSize(380,200)
	rows:AddColumn( "Current Purchased Rows" )

	for k, v in pairs(lotteryRows) do
		rows:AddLine(tostring(v))
	end
end
net.Receive("darkrp_lottery_drawgui", DrawGUI)