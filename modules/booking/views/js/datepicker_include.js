var selected_date = JSON.parse(selected_dates);
function unavailable(date)
{
    var currentMonth = date.getMonth() + 1;
    var currentDate = date.getDate();
    if (currentMonth < 10)
    {
        currentMonth = '0' + currentMonth;
    }
    if (currentDate < 10)
    {
        currentDate = '0' + currentDate;
    }
    dmy = date.getFullYear() + "-" + currentMonth + "-" + currentDate;
    if ($.inArray(dmy, selected_date) == -1) 
        return [false, "", "Unavailable date"];
        
    else
        return [true, ""];
}

$(function()
{
    $(".attribute_date").datepicker({
        defaultDate: new Date(),
        dateFormat: 'yy-mm-dd',
        beforeShowDay: unavailable
    });

});

