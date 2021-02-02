
	var option;
	$(document).ready(function() {
		
		option = $('#selectDate').val();
		showChart();
			
		$('#selectDate').change(function() {
			
			if ($(this).val() == 4) {
					
				$('#myModal').modal({backdrop: 'static'},"show");
					
				$('#btn-show-balance').click(function() {
					var startDate = $('#startDate').val();
					var endDate = $('#endDate').val();
					
					$.post("/profile/checkDates", {
						startDate: startDate, 
						endDate: endDate
						}, function(data) {
							
						if (data == 'true') {
							$('#myModal').modal("hide");
							$('#modalForm').submit();	
						} else {
							$('#errorDate').text('Invalid Date!');
						}
					});
				});
			} else {
				$('#balanceForm').submit();	
			}
		});
	});
		
	$('.btn-close').click(function() {
		$('#errorDate').text('');
		$('#startDate').val('');
		$('#endDate').val('');
			
		$('#selectDate').val(option);
	});
		
	function createChart(chartLabels, chartValues) {
		new Chart($('#chart'), {
			type: 'pie',
			data: {
				labels: chartLabels,
				datasets: [{
					data: chartValues,
					backgroundColor: [
						'rgba(255, 99, 132, 0.5)',
						'rgba(54, 162, 235, 0.5)',
						'rgba(255, 206, 86, 0.5)',
						'rgba(75, 192, 192, 0.5)',
						'rgba(153, 102, 255, 0.5)',
						'rgba(255, 159, 64, 0.5)'
					],
					borderColor: [
						'rgba(255, 99, 132, 1)',
						'rgba(54, 162, 235, 1)',
						'rgba(255, 206, 86, 1)',
						'rgba(75, 192, 192, 1)',
						'rgba(153, 102, 255, 1)',
						'rgba(255, 159, 64, 1)'
					],
					borderWidth: 1
				}]
			},
			options: {
				title: {
					display: true,
					text: 'Expenses',
					fontSize: 20
				}
			}
		});	
	}
		
	function showChart() {
		$.post('/profile/getExpenses', function(data) {
			var chartData = JSON.parse(data);
			var chartLabels = [];
			var chartValues = [];
				
			for (i in chartData) {
				chartLabels.push(chartData[i].name);
				chartValues.push(chartData[i].amount);
			}
			$('#chart').remove();
			$('#chart-parent').append('<canvas id="chart" width="200" height="80"></canvas>');
			if (chartData.length <= 0) {
				chartLabels.push('No expenses');
				chartValues.push('100');
			}
			createChart(chartLabels, chartValues);
		});
	}