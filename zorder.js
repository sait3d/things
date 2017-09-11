$(document).ready(function(){

    $(document).on('click', '#saveorder', function(event) {
        // шапка заказа
        var id_order = 0;
        var id_customer = 0;
        var priceorder = 0;
        // табличная часть
        var price = 0;
        var numline=0;
        var pt = {};
        var i = 1;
        // параметры шапки
        id_order = parseInt($('#id_order').html());
        id_customer = parseInt($('#id_customer').find('option:selected').val());
        // в массив аякс
        pt[0] = {};
        pt[0]["id_order"] = id_order;
        pt[0]["id_customer"] = id_customer;
        // выбираем цены по строкам заказа
        $('.price').each(function () {
            // строка заказа
            numline = $(this).attr('data-id');
            price = parseInt($(this).val());
            // итоговая сумма заказа
            priceorder += price;
            // для передачи в аякс
            pt[i] = {};
            pt[i]["numline"] = numline;
            pt[i]["price"] = price;

            i++;
        });

        $.ajax({
            url: '/administrator/z/ajax_zsaveorder.php',
            async: true,
            cache: false,
            data: {pt: pt},
            type: 'POST',
            success: function(data) {
                alert(data);
            }
        });

        event.stopPropagation();
    });


    $(document).on('change', '#id_customer', function(event) {
        // отборы
        var id_customer = 0;
        id_customer = parseInt($('#id_customer').find('option:selected').val());

        $.ajax({
            url: '/administrator/z/ajax_reportprofit.php',
            async: true,
            cache: false,
            data: 'id_customer='+id_customer,
            type: 'POST',
            success: function(data) {
                $('#wrapperresult').html(data);
            }
        });

        event.stopPropagation();
    });
			
     $(document).on('change', '.price', function(event) {
        // пересчет итого
        var priceorder = 0;

        $('.price').each(function () {
            // строка заказа
            price = parseInt($(this).val());
            // итоговая сумма заказа
            priceorder += price;
        });
		$('#orderprice').html('<b>'+priceorder+'</b>');
		
        event.stopPropagation();
    });
	

});
