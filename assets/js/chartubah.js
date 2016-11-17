
window.onload = function () {

    $.getJSON("data_chart.php", function (result) {
	//bar chart
	var chart = new CanvasJS.Chart("chartContainer2",
	{
		animationEnabled: true,
		title:{
			text: "Chart with Labels on X Axis"
		},
		axisY: {
				title: "Total"
			},
		data: [
		{
			type: "column", //change type to bar, line, area, pie, etc
			dataPoints: result
		}
		]
		});

	chart.render();
	

});
}
