// gestion du datepicker
// masque le champ date de visite pour éviter la modification par ce biais
$('#commande_premiere_page_dateVisite').hide();
// configure le datepicker
$('#datepicker').datepicker({
    language: "fr",
    startDate: "today",
    daysOfWeekDisabled: "0,2",
    todayHighlight: true,
    datesDisabled: []
});
// gestion de l'évènement selection d'une date dans le datepicker
$('#datepicker').on('changeDate', function() {
    // change la valeur du champ date de visite
    $('#commande_premiere_page_dateVisite').val(
        $('#datepicker').datepicker('getFormattedDate')
    );
    // change la valeur de l'afficheur supplémentaires
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
// gere l'affichage des boutons radio pour le type de billets
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
