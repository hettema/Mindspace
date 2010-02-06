/**
 * Copyright 2009, 2010 hette.ma.
 * 
 * This file is part of Mindspace.
 * Mindspace is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.Mindspace is distributed
 * in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public
 * License for more details.You should have received a copy of the GNU General Public License
 * along with Mindspace. If not, see <http://www.gnu.org/licenses/>.
 * 
 *  credits
 * ----------
 * Idea by: Garrett French |    http://ontolo.com    |     garrett <dot> french [at] ontolo (dot) com
 * Code by: Eldhose C G | http://ceegees.in  | eldhose (at) ceegees [dot] in
 * Initiated by: Dennis Hettema    |    http://hette.ma    |     hettema (at) gmail [dot] com
 */
function paddZero(val) {
	if (val < 10) {
		return "0"+val;
	}
	return val;
}
function getTimeString(ts) {
	 var d = new Date(ts);
	 return ""+ paddZero(d.getUTCDate()) + "/" + paddZero(d.getUTCMonth() + 1)+"/" + paddZero(d.getUTCFullYear()) + " "+
	 paddZero(d.getUTCHours()) +":"+ paddZero(d.getUTCMinutes())+":" + paddZero(d.getUTCSeconds());
}
function changeGraph() {

	dataSetProcessed = createDataSet(gDataSet,parseInt($("#serp-category").val()));
	plotAccordingToChoices();
    var dt1 = new Date(gLowerTimeVal);
    var dt2 = new Date(gUpperTimeVal);
    var msg = "Time Frame <br/> Min : " + getTimeString(gLowerTimeVal)
    	+" <br/> Max : "+ getTimeString(gUpperTimeVal);
               
   $('#timeVals').html(msg);
	
}

function checkSelection() {

	if ($("#category-list").val() == "" ){
		showDialog("Please select a Domain Category");
		return false;
	}
	if ($("#group-list").val() == "" ) {
		showDialog("Please select a Keyword Group");
		return false;
	}
	return true;
	
}
function createDataSet(ds,pos) {
	
	var data =[];
	for (var idx=0; idx < ds.domainInfo.length; idx++) {
		var info = ds.domainInfo[idx];
		var obj = {};
		obj.label = info.domainName;
		obj.data = [];
		for (var idx2=0; idx2 < info.occurances.length; idx2++) {
			if (ds.timestamps[idx2] < gLowerTimeVal) {
				continue;
			}
			if (ds.timestamps[idx2] > gUpperTimeVal) {
				continue;
			}
			obj.data.push([ds.timestamps[idx2],info.occurances[idx2][pos]]);
		}
		data.push(obj);
	}
	return data;
}
// insert checkboxes 

function plotAccordingToChoices() {
    var data = [];
    choiceContainer.find("input:checked").each(function () {
    	var key = $(this).attr("name");
        key = parseInt(key);
        if (dataSetProcessed[key])
              data.push(dataSetProcessed[key]);
    });
	var width = window.outerWidth - 500;
  	var height = 450;
    if (data.length > 0)  {
        $("#placeholder").attr("style","width:"+width+"px;height:"+height+"px");
        $.plot($("#placeholder"), data, {
	        yaxis: { min: 0 },
	        xaxis: { tickDecimals: 0 , mode:'time'},
	        lines: { show: true },
	        points: { show: true },
	        grid: { hoverable: true, clickable: true }    
    	});
            
	}
}

function initChart(dataSet) {
        
    var html = "";
    
   	choiceContainer.children().remove();
    var i = 0;
    $.each(dataSet, function(key, val) {
        val.color = i;
        ++i;
    });

    var info = dataSet.domainInfo;
    
    for(var idx = 0; idx < dataSet.length; idx++) {
       	html += '<br/><input type="checkbox" name="' +idx
             + '" checked="checked" id="id' + idx + '">' +
            '<label style="color:white;margin-left:5px" for="id' + idx + '">' + dataSet[idx].label + '</label>';
    }
    choiceContainer.html(html);
    choiceContainer.find("input").click(plotAccordingToChoices);
    $("#placeholder").bind("plothover", function (event, pos, item) {
 	   
        if (item) {
            if (previousPoint != item.datapoint) {
                previousPoint = item.datapoint;
                
                $("#tooltip").remove();
                var x = item.datapoint[0],
                y = item.datapoint[1];
                var eng = "";
                for (var idx = 0; idx < gDataSet.timestamps.length; idx++) {
                	if (gDataSet.timestamps[idx] == x ) {
                		eng = gDataSet.engines[idx];
                		break
                	}
                }
                var msg = getTimeString(x) + "-" +item.series.label + ", count:" + y +",engine:"+eng;
                showTooltip(item.pageX, item.pageY,msg);
            }
        }
        else {
            $("#tooltip").remove();
            previousPoint = null;            
        }
	   
	});
    
}

function showTooltip(x, y, contents) {
    $('<div id="tooltip">' + contents + '</div>').css( {
        position: 'absolute',
        display: 'none',
        top: y + 5,
        left: x + 5,
        border: '1px solid #fdd',
        padding: '2px',
        'background-color': '#fee',
        opacity: 0.80
    }).appendTo("body").fadeIn(200);
}

