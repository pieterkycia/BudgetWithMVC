
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
					if (checkDate(startDate) && checkDate(endDate) && (startDate <= endDate)) {
						$('#myModal').modal("hide");
						$('#modalForm').submit();	
					} else {
						$('#errorDate').css('visibility', 'visible');
					}
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
	
	var chartBgColors = [
		'rgba(255, 99, 132, 0.5)', 'rgba(54, 162, 235, 0.5)', 'rgba(255, 206, 86, 0.5)', 'rgba(75, 192, 192, 0.5)', 'rgba(153, 102, 255, 0.5)', 'rgba(255, 159, 64, 0.5)'
	];
	
	var chartBorderColors = [
		'rgba(255, 99, 132, 0.5)', 'rgba(54, 162, 235, 0.5)', 'rgba(255, 206, 86, 0.5)', 'rgba(75, 192, 192, 0.5)', 'rgba(153, 102, 255, 0.5)', 'rgba(255, 159, 64, 0.5)'
	];
	
	var incomesChart = new Chart($('#incomes-chart'), {
		type: 'doughnut',
		data: {
			datasets: [{
				backgroundColor: chartBgColors,
				borderColor: chartBorderColors,
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
				backgroundColor: chartBgColors,
				borderColor: chartBorderColors,
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
		getDataForChart('Expenses', expensesChart, 'expenses-parent-chart');
		getDataForChart('Incomes', incomesChart, 'incomes-parent-chart');
	}
	
	function updateChart(chart, labels, values, chartParentNode) {
		chart.data.labels = labels;
		chart.data.datasets[0].data = values;
		chart.update();
		
		var height = chart.legend.height + 200;
		$('#' + chartParentNode).css('height', height);
	}
	
	function getDataForChart(dataType, chart, chartParentNode) {
		$.post('/profile/get' + dataType, function(data) {
			var chartData = JSON.parse(data);
			var labels = [];
			var values = [];
				
			for (i in chartData) {
				labels.push(chartData[i].name);
				values.push(chartData[i].amount);
			}
			
			if (chartData.length <= 0) {
				labels.push('No data');
				values.push('100');
			}
			updateChart(chart, labels, values, chartParentNode);
		});
	}
	
	function checkDate(date) {
		if (checkFormat(date) && checkValue(date)) {
			return true;
		} else {
			return false;
		}
	}
	
	function checkFormat(date)
	{
		if (date.match(/^[1-9]{1}[0-9]{3}-[0-9]{1,2}-[0-9]{1,2}$/)) {
			return true;
		} else {
			return false;
		}
	}

	function checkValue(date)
	{
		var fullDate = date.split('-');
		var year = fullDate[0];
		var month = fullDate[1] - 1;
		var day = fullDate[2];
		
		var setDate = new Date(year, month, day);
		if (setDate.getDate() != day || setDate.getMonth() != month || setDate.getFullYear() != year) {
			return false;
		} else {
			return true;
		}
	}