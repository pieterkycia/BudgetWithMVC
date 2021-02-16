
	$('#amount').keyup(function () {
		limit();
	});
	
	$('#date, input[name = "category"]').change(function () {
		limit();
	});

	function limit() {
		var inputs = $('#expense-form').serializeArray();

		if (!(inputs[3].name == 'category')) {
			$('#limit').text('nie wybrano żadnej kategorii');
			return;
		} else {
			getExpense(inputs);
		}
	}
	
	function getExpense(inputData){
		var dataAsText = inputData[3].value;
		var value = inputData[0].value;
		var dataAsValue = dataAsText.split('|');
		var date = inputData[1].value;
		var id = dataAsValue[0];
		var name = dataAsValue[1];
		var limit = dataAsValue[2];
		var moneySpent = 0;
		
		$.post('/Profile/getExpenseByIdAndDate', {
			id: id, date: date
		}, function (data) {
			data = JSON.parse(data);
			if (data || (limit == 0)) {
				moneySpent = Math.abs(data.amount);
				showInfo(limit, moneySpent, name, value);
			} else {
				showInfo(limit, moneySpent, name, value);
				$('#alert-limit').html('<div class="alert alert-danger"> <b> Niepoprawna data </b> </div>');
			}
		});
	}
	
	function showInfo(limit, moneySpent, name, value) {
		if (limit == 0) {
			$('#alert-limit').html('');
			$('#limit').html('Nie ustalono limitu dla kategorii <b>' + name + '</b>');
			$('#alert-limit').html('<div class="alert alert-info"> Możesz wydawać ile chcesz! </div>');
			return;
		} else {
			$('#limit').html('Ustalony limit dla kategorii <b>' + name + '</b> to <b>' + limit + ' zł</b>');
			limitAlert(limit, moneySpent, value);
		}
	}
	
	function limitAlert(limit, moneySpent, value) {
		value = checkValue(value);
		if (value != 'false') {
		
			var allSpent = value + moneySpent;
			if (allSpent > limit) {
				$('#alert-limit').html('<div class="alert alert-danger"> W tym miesiącu wydano <b>' + moneySpent.toFixed(2) + ' zł </b> <br/> Wpisana kwota to <b>' + value + ' zł </b> <br/> Przekraczasz limit o <b>' + (allSpent - limit).toFixed(2) + ' zł </b> </div>');
			} else {
				$('#alert-limit').html('<div class="alert alert-success"> W tym miesiącu wydano <b>' + moneySpent.toFixed(2) + ' zł </b> <br/> Wpisana kwota to <b>' + value + ' zł </b> <br/> Możesz jeszcze wydać <b>' + (limit - allSpent).toFixed(2) + ' zł </b> </div>');
			}
		} else {
			$('#alert-limit').html('<div class="alert alert-danger"> <b> Niepoprawna kwota </b> </div>');
		}
	}
	
	function checkValue(value) {
		value = value.replace(/\,/g, '.');
		if (value == '') {
			value = Math.abs(value);
			value = parseFloat(value);
			return value;
		}
		if (value.match(/^[0-9]+\.+$/)) {
			value = value.replace(/\./g, '');
		}
		if (value.match(/^[0-9]+(\.[0-9]{1,2}){0,1}$/) ) {
			value = parseFloat(value);
			return value;
		} else {
			return 'false';
		}
	}