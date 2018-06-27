/* vim: set expandtab sw=4 ts=4 sts=4: */

/**
 * Module import
 */
import { PMA_Messages as messages } from './variables/export_variables';

/**
 * @package PhpMyAdmin
 *
 * Server Status Advisor
 */

/**
 * Unbind all event handlers before tearing down a page
 */
function teardownServerStatusAdvisor () {
    $('a[href="#openAdvisorInstructions"]').off('click');
    $('#statustabs_advisor').html('');
    $('#advisorDialog').remove();
    $('#instructionsDialog').remove();
}

/**
 * Binding event handlers on page load
 */
function onloadServerStatusAdvisor () {
    // if no advisor is loaded
    if ($('#advisorData').length === 0) {
        return;
    }

    /** ** Server config advisor ****/
    var $dialog = $('<div />').attr('id', 'advisorDialog');
    var $instructionsDialog = $('<div />')
        .attr('id', 'instructionsDialog')
        .html($('#advisorInstructionsDialog').html());

    $('a[href="#openAdvisorInstructions"]').click(function () {
        var dlgBtns = {};
        dlgBtns[messages.strClose] = function () {
            $(this).dialog('close');
        };
        $instructionsDialog.dialog({
            title: messages.strAdvisorSystem,
            width: 700,
            buttons: dlgBtns
        });
    });

    var $cnt = $('#statustabs_advisor');
    var $tbody;
    var $tr;
    var even = true;

    var data = JSON.parse($('#advisorData').text());
    $cnt.html('');

    if (data.parse.errors.length > 0) {
        $cnt.append('<b>Rules file not well formed, following errors were found:</b><br />- ');
        $cnt.append(data.parse.errors.join('<br/>- '));
        $cnt.append('<p></p>');
    }

    if (data.run.errors.length > 0) {
        $cnt.append('<b>Errors occurred while executing rule expressions:</b><br />- ');
        $cnt.append(data.run.errors.join('<br/>- '));
        $cnt.append('<p></p>');
    }

    if (data.run.fired.length > 0) {
        $cnt.append('<p><b>' + messages.strPerformanceIssues + '</b></p>');
        $cnt.append('<table class="data" id="rulesFired" border="0"><thead><tr>' +
                    '<th>' + messages.strIssuse + '</th><th>' + messages.strRecommendation +
                    '</th></tr></thead><tbody></tbody></table>');
        $tbody = $cnt.find('table#rulesFired');

        var rc_stripped;

        $.each(data.run.fired, function (key, value) {
            // recommendation may contain links, don't show those in overview table (clicking on them redirects the user)
            rc_stripped = $.trim($('<div>').html(value.recommendation).text());
            $tbody.append($tr = $('<tr class="linkElem noclick"><td>' +
                                    value.issue + '</td><td>' + rc_stripped + ' </td></tr>'));
            even = !even;
            $tr.data('rule', value);

            $tr.click(function () {
                var rule = $(this).data('rule');
                $dialog
                    .dialog({ title: messages.strRuleDetails })
                    .html(
                        '<p><b>' + messages.strIssuse + ':</b><br />' + rule.issue + '</p>' +
                    '<p><b>' + messages.strRecommendation + ':</b><br />' + rule.recommendation + '</p>' +
                    '<p><b>' + messages.strJustification + ':</b><br />' + rule.justification + '</p>' +
                    '<p><b>' + messages.strFormula + ':</b><br />' + rule.formula + '</p>' +
                    '<p><b>' + messages.strTest + ':</b><br />' + rule.test + '</p>'
                    );

                var dlgBtns = {};
                dlgBtns[messages.strClose] = function () {
                    $(this).dialog('close');
                };

                $dialog.dialog({ width: 600, buttons: dlgBtns });
            });
        });
    }
}

/**
 * Module export
 */
export {
    teardownServerStatusAdvisor,
    onloadServerStatusAdvisor
};
