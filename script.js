$.ajax({
	type: "GET",
	url: "index.php",
	data: "requsest=getAllData",
	dataType: 'json',
	success: function(msg){
	//	console.log(msg[0]);
		writeContent(msg);
	}
});

function writeContent(data)
{
	for(var indx in data)
	{
		console.log(data[indx]);
		var item = data[indx];
		var li = "";
		for(var per in item.period)
		{			
			if(item.period[per] != undefined)
			{
				li += "<li>"+item.period[per]+"<li>";
			}
		}
		var str = "<tr><td></td><td>"+indx+"</td><td>"+item.allCash+"</td><td>"+item.allPayment+"</td><td><ul id='period'>"+li+"</ul></td><td>"+item.balance+"</td><td>"+item.payDay+"</td></tr>";
		
		$("#content").append(str);
	}	
}
