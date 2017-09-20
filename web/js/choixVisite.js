$('#commande_premiere_page_dateVisite').hide();

$('#datepicker').datepicker({
    language: "fr",
    startDate: "today",
    daysOfWeekDisabled: "0,2",
    todayHighlight: true,
    datesDisabled: []
});
$('#datepicker').on('changeDate', function() {
    $('#commande_premiere_page_dateVisite').val(
        $('#datepicker').datepicker('getFormattedDate')
    );
    $('#date-selected').val(
        $('#datepicker').datepicker()
    );
    var dateSelected = $('#datepicker').datepicker('getFormattedDate');
    dateSelected = dateSelected.split("-");
    var dateAffichee = "";
    for (var i = dateSelected.length-1; i>=0; i--) {
        dateAffichee = dateAffichee+dateSelected[i]+"/";
    }
    dateAffichee = dateAffichee.slice(0,10);
    $('#date-selected').text(dateAffichee);
});

$('input[type="radio"]').hide();
$('.radio label').addClass('btn');
$('.radio label').css('width', '150px');
$('.radio label').css('margin', '5px');
$('.radio label').css('background-color', 'grey');
$('.radio label').css('color', 'white');
$('#commande_premiere_page_type_0').parent().css('background-color', 'rgb(201,45,0)');
$('#commande_premiere_page_type_0').click(function () {
    $(this).parent().css('background-color', 'rgb(201,45,0)');
    $('#commande_premiere_page_type_1').parent().css('background-color', 'grey');
})
$('#commande_premiere_page_type_1').click(function () {
    $(this).parent().css('background-color', 'rgb(201,45,0)')
    $('#commande_premiere_page_type_0').parent().css('background-color', 'grey');
})
