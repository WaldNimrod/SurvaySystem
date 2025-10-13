//This JS Responsible to create the mashov after getting the final data from the sever

var ParamTextName = 'ParamText';
var Data; //Containes the data returned from the server For creating the mashov
var RectSize; // the size of 1 squere of Slider (represent 1 standard deviation unit)

$(document).ready(function () {
    RectSize = $($('.SliderContainer')[0]).width() / 6;

    //Taking the data from the server side (converting the String to a Javascript object that containes all the data from the server)
    try {
        Data = JSON.parse($('#TextData').text());
    } catch (e) {
        console && console.error && console.error('Failed parsing result JSON', e);
        return;
    }

    //Creating the Personal Details of the responder
    $PDTable = $('#PersonalD_Table');
    $.each(Data.PD, function (key, value) {
        //the key holds the DB parameter name of the ResponderExtra Record
        //and value holds the Title of the parameter and the value seperated by ';'
        $PDTable.append(
            $('<tr>').append(
                $('<td>').text(value.split(';')[0])
            ).append(
                $('<td>').text(value.split(';')[1])
            )
        );
    });

    //filling all the texts with an ID of PD_XXX
    $("[id^='PD']").each(function () {
        var IDval = Data.PD && Data.PD[this.id];
        if (IDval) {
            $(this).text((IDval).split(';')[1]);
        }
    });

    //Configuring the sliders positions:
    //on each Div with have an ID that is one the Dims that was calculated on the sever
    //we will calculate the position from the value of the dim and move the arrow
    $.each(Data.Dims, function (key, value) {

        //  $('#debug_txt_res').html($('#debug_txt_res').html() + key + ': ' + value.res + '<br>');

        $DimDiv = $('#' + key);

        var moveArrow = 18;

        //Arrows Border
        if (value.res > 3) {
            value.res = 3;
        }
        if (value.res < -3) {
            value.res = -3;
        }

        moveArrow += value.res * -1 * RectSize;

        $DimDiv.find('.Slider_Arrow').find('img')
            .css('position', 'relative')
            .css('left', moveArrow + 'px');

        if (value.threshold) {
            var $dimThreshold = $('<div style="position:absolute; z-index:3; top:25px;">');
            var tleft = 320;
            tleft += value.threshold * -1 * RectSize;
            $dimThreshold.css('left', tleft + 'px');
            $dimThreshold.append($('<img src="data:image/png;base64,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" width="54" height="75">'));
            $DimDiv.append($dimThreshold);
        }
    });

    $.each($("div[id*='ParamText_dim']"), function () {
        var PTextID = this.id;
        var delimiters = PTextID.split('-');
        var dimID = delimiters.splice(0, 1)[0];
        dimID = dimID.substring(ParamTextName.length + 1);
        var DimRes = Data.Dims && Data.Dims[dimID] && Data.Dims[dimID].res;

        var $PTexts = $(this).find('div');

        for (var dindx = 0; dindx < $PTexts.length; dindx++) {
            //First
            if (dindx == 0) {
                if (DimRes <= delimiters[dindx]) {
                    $($PTexts[dindx]).hide();
                }
            }
            //Last
            else if (dindx + 1 == $PTexts.length) {
                if (DimRes >= delimiters[dindx - 1]) {
                    $($PTexts[dindx]).hide();
                }
            }
            //Between
            else {
                if (DimRes >= delimiters[dindx - 1] || DimRes <= delimiters[dindx]) {
                    $($PTexts[dindx]).hide();
                }
            }
        }
    });

    if (Data.Social_desirability_ReRun.toLowerCase() == 'false' || Data.Social_desirability_ReRun.toLowerCase() == 'yes') {
        if (Data.Social_desirability_ReRun.toLowerCase() != 'yes') {
            $('#Social_desirability_ReRun').hide();
        }
        $('[id=Group1]').hide();
        if (parseFloat(Data.Dims.dim_sumTotal.res) <= parseFloat(Data.Dims.dim_sumTotal.threshold)) {
            $('[id=Group3]').hide();
        }
        else {
            $('[id=Group2]').hide();
        }
    }
    else {
        if (Data.Social_desirability_ReRun.toLowerCase() == 'double') {
            $('[id=Group2]').hide();
            $('[id=Group3]').hide();
        }
    }
});