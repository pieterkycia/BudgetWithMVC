
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
							
							$('#errorDate').css('visibility', 'visible');
						}
					});
				});
			} else {
				$('#balanceForm').submit();	
			}
		});
	});
		
	$('.btn-close').click(function() {
		$('#errorDate').css('visibility', 'hidden');
		$('#startDate').val('');
		$('#endDate').val('');
			
		$('#selectDate').val(option);
	});
	
	var chartBgColor = [
		'rgba(255, 99, 132, 0.5)', 'rgba(54, 162, 235, 0.5)', 'rgba(255, 206, 86, 0.5)', 'rgba(75, 192, 192, 0.5)', 'rgba(153, 102, 255, 0.5)', 'rgba(255, 159, 64, 0.5)'
	];
	
	var chartBorderColor = [
		'rgba(255, 99, 132, 0.5)', 'rgba(54, 162, 235, 0.5)', 'rgba(255, 206, 86, 0.5)', 'rgba(75, 192, 192, 0.5)', 'rgba(153, 102, 255, 0.5)', 'rgba(255, 159, 64, 0.5)'
	];
	
	var incomesChart = new Chart($('#incomes-chart'), {
		type: 'doughnut',
		data: {
			datasets: [{
				backgroundColor: chartBgColor,
				borderColor: chartBorderColor,
				borderWidth: 1
			}]
		},
		options: {
			title: {
				display: true,
				text: 'Incomes',
				fontSize: 20
			},
			legend: {
				labels: {
					boxWidth: 12
				}	
			},			
			maintainAspectRatio: false
		}
	});	
		
	var expensesChart = new Chart($('#expenses-chart'), {
		type: 'doughnut',
		data: {
			datasets: [{
				backgroundColor: chartBgColor,
				borderColor: chartBorderColor,
				borderWidth: 1
			}]
		},
		options: {
			title: {
				display: true,
				text: 'Expenses',
				fontSize: 20
			},
			legend: {
				labels: {
					boxWidth: 12
				}	
			},			
			maintainAspectRatio: false
		}
	});	

	function showChart() {
		getIncomes();
		getExpenses();
	}
	
	function updateChart(chart, label, data, canvasId) {
		
		chart.data.labels = label;
		chart.data.datasets[0].data = data;
		chart.update();
		
		var height = chart.legend.height + 200;
		$('#' + canvasId).css('height', height);
	}
	
	function getExpenses() {
		$.post('/profile/getExpenses', function(data) {
			var chartData = JSON.parse(data);
			var chartLabels = [];
			var chartValues = [];
				
			for (i in chartData) {
				chartLabels.push(chartData[i].name);
				chartValues.push(chartData[i].amount);
			}
			
			if (chartData.length <= 0) {
				chartLabels.push('No data');
				chartValues.push('100');
			}
			updateChart(expensesChart, chartLabels, chartValues, 'expenses-parent-chart');
		});
	}
	
	function getIncomes() {
		$.post('/profile/getIncomes', function(data) {
			var chartData = JSON.parse(data);
			var chartLabels = [];
			var chartValues = [];
				
			for (i in chartData) {
				chartLabels.push(chartData[i].name);
				chartValues.push(chartData[i].amount);
			}
			
			if (chartData.length <= 0) {
				chartLabels.push('No data');
				chartValues.push('100');
			}
			updateChart(incomesChart, chartLabels, chartValues, 'incomes-parent-chart');
		});
	}