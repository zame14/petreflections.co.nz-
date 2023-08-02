jQuery(function($) {
    setTimeout(function () {
        $(".banner-content-wrapper").addClass('ani-show');
        $(".inside-banner-wrapper .page-title").addClass('ani-show');
    }, 500);
    $(".search_tag").keyup(function () {
        var val = $(this).val().trim();
        val = val.replace(/\s+/g, '');
        if(val.length >= 3 || val.length ==0) {
            var searchTerm = $(".search_tag").val();
            var listItem = $('.results tbody').children('tr');
            var searchSplit = searchTerm.replace(/ /g, "'):containsi('");

            $.extend($.expr[':'], {'containsi': function(elem, i, match, array){
                return (elem.textContent || elem.innerText || '').toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
            }
            });

            $(".results tbody tr").not(":containsi('" + searchSplit + "')").each(function(e){
                $(this).attr('visible','false');
            });

            $(".results tbody tr:containsi('" + searchSplit + "')").each(function(e){
                $(this).attr('visible','true');
            });

            var jobCount = $('.results tbody tr[visible="true"]').length;
            $('.counter').text(jobCount + ' item');

            if(jobCount == '0') {$('.no-result').show();}
            else {$('.no-result').hide();}
        }
    });
    $(".search_owner").keyup(function () {
        var val = $(this).val().trim();
        val = val.replace(/\s+/g, '');
        if(val.length >= 3 || val.length ==0) {
            var searchTerm = $(".search_owner").val();
            var listItem = $('.results tbody').children('tr');
            var searchSplit = searchTerm.replace(/ /g, "'):containsi('");

            $.extend($.expr[':'], {'containsi': function(elem, i, match, array){
                return (elem.textContent || elem.innerText || '').toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
            }
            });

            $(".results tbody tr").not(":containsi('" + searchSplit + "')").each(function(e){
                $(this).attr('visible','false');
            });

            $(".results tbody tr:containsi('" + searchSplit + "')").each(function(e){
                $(this).attr('visible','true');
            });

            var jobCount = $('.results tbody tr[visible="true"]').length;
            $('.counter').text(jobCount + ' item');

            if(jobCount == '0') {$('.no-result').show();}
            else {$('.no-result').hide();}
        }
    });
    $(".status").focus(function() {
        prev_val = $(this).val();
    }).change(function() {
       var pet_id = $(this).data("id");
       var status = $(this).val();
       $(this).blur();
        var success = confirm('Are you sure you want to change the status?');
        if(success) {
            // do ajax to update status and email customer
            $.ajax({
                url: ajaxurl + "?action=ajax&call=updateStatus&pet_id=" + pet_id + "&status="+status,
                cache: false,
                success: function (response) {
                    if (response == 2) {
                        // display alert that owner has been notified
                        alert("Owner has been notified via email");
                    }
                }
            });
        } else {
            $(this).val(prev_val);
            return false;
        }
    });
    $(".table-btn").click(function() {
       $(this).toggleClass('fa-minus');
    });
    $(".datepicker1").datepicker({
        dateFormat: "dd/mm/yy"
    });
    $(".datepicker2").datepicker({
        dateFormat: "dd/mm/yy"
    });
    $(".date-picker-wrapper a").click(function() {
        var date1 = $(".datepicker1").val();
        var date2 = $(".datepicker2").val();
        if((date1 == "") || (date2 == "")) {
            alert("Please select both a start and end date.");
        } else {
            $("#pet-report").submit();
        }
    });
});
function hideNotification()
{
    var $ = jQuery;
    $(".my-account-wrapper .notification").hide();
}
function toggleTable(view)
{
    var $ = jQuery;
    if(view == 1) {
        var action = "showPetStatusTables";
        $.ajax({
            url: ajaxurl + "?action=ajax&call="+action,
            cache: false,
            success: function (response) {
                $(".table-responsive").html(response).fadeIn();
            }
        });
    } else {
        var action = "showPetTable";
        $.ajax({
            url: ajaxurl + "?action=ajax&call="+action,
            cache: false,
            success: function (response) {
                window.location.href = response;
            }
        });
    }
}
function showTable(i)
{
    var $ = jQuery;
    $(".row-collapse-"+i).toggle();
}
function runReport()
{
    var $ = jQuery;
    var date1 = $(".datepicker1").val();
    var date2 = $(".datepicker2").val();
    $.ajax({
        url: ajaxurl + "?action=ajax&call=updateReport&date1="+ date1 + "&date2="+ date2,
        cache: false,
        success: function (response) {
            $(".reports-wrapper").html(response).hide().fadeIn();
        }
    });
}