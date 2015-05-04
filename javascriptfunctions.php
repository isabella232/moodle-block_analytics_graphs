<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
?>

<script type="text/javascript">
    
function parseObjToString(obj) {
            var array = $.map(obj, function(value) {
                return [value];
            });
            return array;
        }

function sendEmail() {
        $( "form" ).submit(function( event ) {
                    // Stop form from submitting normally
                    event.preventDefault();
                    // Get some values from elements on the page:
                    var $form = $( this ),
                    otherval = $form.find( "input[name='other']" ).val(),
                    idsval = $form.find( "input[name='ids[]']" ).val(),
                    subjectval = $form.find( "input[name='subject']" ).val(),
                    textoval = $form.find( "textarea[name='texto']" ).val(),
                    url = $form.attr( "action" );

                    // Send the data using post
                    var posting = $.post( url, { other: otherval, ids: idsval,  
                                    subject: subjectval, texto: textoval } );
                    // Put the results in a div
                    posting.done(function( data ) {
                    //alert(data);
                    if(data){
                        $(".div_nomes").dialog("close");
                        alert("<?php echo get_string('sent_message', 'block_analytics_graphs');?>");
                    } else {
                        alert("<?php echo get_string('not_sent_message', 'block_analytics_graphs');?>");
                    }

                });
            });

            $(".div_nomes").dialog({
                modal: true,
                autoOpen: false,
                width: 'auto'
            });
}

function createEmailForm(titulo, alunos, courseid, other) {
        var nomes="";
                ids = [];
                email = [];
                $.each(alunos, function(ind, val){
            nomes += val.nome + ", ";
                        ids.push(val.userid);
                        email.push(val.email);
                });
                var string =
            "<h3>" + titulo + "</h3>" +  
            "<p style='font-size:small'>" + nomes + "</p>" +
            "<form action='email.php?id=" + courseid + "' method='post'>" +
                        "<input type='hidden' name='other' value='" + other + "'>" +
                        "<input type='hidden' name='ids[]' value='" + ids + "'>" +
                        "<center>" +
                        "<p style='font-size:small'><?php echo get_string('subject', 'block_analytics_graphs');?>: " +
            "<input type='text' name='subject' ></p>" +
                        "<textarea style='font-size:small' cols='100' rows='6' name='texto' ></textarea>" +
                        "<br>" +
                        "<input type='submit' " +
            "value='<?php echo get_string('send_email', 'block_analytics_graphs');?>' " +
            "style='font-size: small' ></center>" +
                        "</form>";
                return string;
}


function convert_series_to_group(group_id, groups, all_content, chart_id)
{
    
    $(chart_id).highcharts().series[0].setData([0]);
    $(chart_id).highcharts().series[1].setData([0]);

    //comeback to original series
    if(group_id == "-")
    {
        var nraccess_vet = [];
        var nrntaccess_vet = [];
        $.each(geral, function(index, value) {
            if (value.numberofaccesses > 0){
                nraccess_vet.push(value.numberofaccesses);
            }else{
                nraccess_vet.push([0]);
            }

            if(value.numberofnoaccess > 0){
                nrntaccess_vet.push(value.numberofnoaccess);
            }else{
                nrntaccess_vet.push([0]);
            }
        });

        $(chart_id).highcharts().series[0].setData(nraccess_vet);
        $(chart_id).highcharts().series[1].setData(nrntaccess_vet);
    }
    else
    {
        $.each(groups, function(index, group){
            if(index == group_id){
                var access = group.numberofaccesses;
                var noaccess = group.numberofnoaccess;
                $(chart_id).highcharts().series[0].setData(access);
                $(chart_id).highcharts().series[1].setData(noaccess);
            }
        });
    }
}


function series_update(course, graph_title, result, students, chart_id, group){
    $.post( "phpfunctions.php",
        { course: course, title: graph_title, call_function: "graph_submission_create_graph" , result: result, students: students},
        function( data ) {
            //reset series data
            $(chart_id).highcharts().series[0].setData([0]);
            $(chart_id).highcharts().series[1].setData([0]);
            $(chart_id).highcharts().series[2].setData([0]);
            $(chart_id).highcharts().series[3].setData([0]);
            $(chart_id).highcharts().series[4].setData([0]);
            //update series data
            $(chart_id).highcharts().series[0].setData(data.serie1);
            $(chart_id).highcharts().series[1].setData(data.serie2);
            $(chart_id).highcharts().series[2].setData(data.serie3);
            $(chart_id).highcharts().series[3].setData(data.serie4);
            $(chart_id).highcharts().series[4].setData(data.serie5);

            if(group != "-"){
                data.statistics = parseObjToString(data.statistics);
                $.each(data.statistics, function(index, value) {
                    var nome = value.assign;

                    var div = "";
                    if (typeof value.in_time_submissions != 'undefined')
                    {

                        var title = graph_title +
                            "</h3>" +
                            <?php echo json_encode(get_string('in_time_submission', 'block_analytics_graphs')); ?> +
                            " - " +  nome ;
                        div += "<div class='div_nomes' id='" + index + "-0-"+group+"'>" + 
                            createEmailForm(title, value.in_time_submissions, courseid, "assign.php") +
                            "</div>";
                    }
                    if (typeof value.latesubmissions != 'undefined')
                    {

                        var title = graph_title +
                            "</h3>" +
                            <?php echo json_encode(get_string('late_submission', 'block_analytics_graphs')); ?> +
                            " - " +  nome ;
                        div += "<div class='div_nomes' id='" + index + "-1-"+group+"'>" +
                            createEmailForm(title, value.latesubmissions, courseid, "assign.php") +
                            "</div>";
                    }
                    if (typeof value.no_submissions != 'undefined')
                    {

                        var title = graph_title +
                            "</h3>" +
                            <?php echo json_encode(get_string('no_submission', 'block_analytics_graphs')); ?> +
                            " - " +  nome ;
                        div += "<div class='div_nomes' id='" + index + "-2-"+group+"'>" +
                            createEmailForm(title, value.no_submissions, courseid, "assign.php") +
                            "</div>";
                    }
                    //dont repeat div write
                    if($(index+"-0-"+group).length===0 && $(index+"-1-"+group).length===0 && $(index+"-2-"+group).length===0)
                        $("body").append(div);
                });
                $(".div_nomes").dialog({
                    modal: true,
                    autoOpen: false,
                    width: 'auto'
                });
            }
    }, "json");
}

</script>