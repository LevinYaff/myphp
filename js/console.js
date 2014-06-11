/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Used in or for console
 *
 * @package phpMyAdmin-Console
 */

/**
 * Executed on page load
 */
$(function () {

    if($('#pma_console').length == 0) {
        return;
    }

    PMA_console.initialize();
});


/**
 * Console object
 */
var PMA_console = {
    $consoleContent: null,
    $consoleToolbar: null,
    $requestForm: null,
    isInitialized: false,
    /**
     * Used for console initialize
     *
     * @return void
     */
    initialize: function() {
        // Cookie var checks and init
        if(! $.cookie('pma_console_height')) {$.cookie('pma_console_height', 92);}
        if(! $.cookie('pma_console_mode')) {$.cookie('pma_console_mode', 'info');}

        // Vars init
        PMA_console.$consoleToolbar= $('#pma_console .toolbar');
        PMA_console.$consoleContent= $('#pma_console .content');

        // Generate a from for post
        PMA_console.$requestForm = $('<form method="post" action="import.php">'
            + '<input type="hidden" name="is_js_confirmed" value="0">'
            + '<input type="hidden" name="pos" value="0">'
            + '<input type="hidden" name="message_to_show"'
            + 'value="Your SQL query has been executed successfully.">'
            + '<textarea name="sql_query"></textarea>'
            + '<input type="hidden" name="token" value="'
            + PMA_commonParams.get('token') + '">'
            + '</form>');
        PMA_console.$requestForm.bind('submit', AJAX.requestHandler);

        // Reinit of Resizer and Input is OK
        PMA_consoleResizer.initialize();
        PMA_consoleInput.initialize();

        // Change console mode from cookie
        switch($.cookie('pma_console_mode')) {
            case 'collapse':
                PMA_console.collapse();
                break;
            default:
                $.cookie('pma_console_mode', 'info');
            case 'info':
                PMA_console.info();
                break;
            case 'show':
                PMA_console.show();
                PMA_console.scrollBottom();
                break;
        }

        // Event binds that shouldn't run again
        if(PMA_console.isInitialized === false) {
            $('#pma_console .switch_button').click(PMA_console.toggle);
            $(document).keydown(function(event) {
                // 27 keycode is ESC
                if(event.keyCode == 27)
                    PMA_console.toggle();
            });

            $('#pma_console .toolbar').children().mousedown(function() {
                event.preventDefault();
                event.stopImmediatePropagation();
            });

            $('#console_clear').click(function() {
                PMA_consoleMsg.clear();
            });

            PMA_console.isInitialized = true;
        }
    },
    execute: function(queryString) {
        if(typeof(queryString) != 'string'){
            return;
        }
        PMA_consoleMsg.add(queryString, 'query');
        PMA_console.$requestForm.children('textarea').val(queryString);
        PMA_console.$requestForm.trigger('submit');
    },
    /**
     * Change console to collapse mode
     *
     * @return void
     */
    collapse: function() {
        $.cookie('pma_console_mode', 'collapse');
        var pmaConsoleHeight = $.cookie('pma_console_height');

        if(pmaConsoleHeight < 32)
            $.cookie('pma_console_height', 92);
        $('#pma_console .toolbar').addClass('collapsed');
        PMA_console.$consoleContent.height(pmaConsoleHeight);
        PMA_console.$consoleContent.stop();
        PMA_console.$consoleContent.animate({'margin-bottom': -1 * PMA_console.$consoleContent.outerHeight() + 'px'},
            'fast', 'easeOutQuart', function() {
                PMA_console.$consoleContent.css({display:'none'});
                $(window).trigger('resize');
            });
    },
    /**
     * Show console
     *
     * @return void
     */
    show: function() {
        $.cookie('pma_console_mode', 'show');

        var pmaConsoleHeight = $.cookie('pma_console_height');

        if(pmaConsoleHeight < 32) {
            $.cookie('pma_console_height', 32);
            PMA_console.collapse();
            return;
        }
        PMA_console.$consoleContent.css({display:'block'});
        if($('#pma_console .toolbar').hasClass('collapsed'))
            $('#pma_console .toolbar').removeClass('collapsed');
        PMA_console.$consoleContent.height(pmaConsoleHeight);
        PMA_console.$consoleContent.stop();
        PMA_console.$consoleContent.animate({'margin-bottom': 0},
            'fast', 'easeOutQuart', function() {
                $(window).trigger('resize');
            });
        PMA_consoleInput.cm.focus();
    },
    /**
     * Change console to SQL information mode
     * this mode shows current SQL query
     * This mode is the default mode
     *
     * @return void
     */
    info: function() {
        // Under construction
        PMA_console.collapse();
    },
    /**
     * Toggle console mode between collsapse/show
     * Used for toggle buttons and shortcuts
     *
     * @return void
     */
    toggle: function() {
        switch($.cookie('pma_console_mode')) {
            case 'collapse':
            case 'info':
                PMA_console.show();
                break;
            case 'show':
                PMA_console.collapse();
                break;
            default:
                PMA_consoleInitialize();
        }
    },
    scrollBottom: function() {
        PMA_console.$consoleContent.scrollTop(PMA_console.$consoleContent.prop("scrollHeight"));
    }
};

/**
 * Resizer object
 * Careful: this object UI logics highly related with functions under PMA_console
 * Resizing min-height is 32, if small than it, console will collapse
 */
var PMA_consoleResizer = {
    'posY': 0,
    'height': 0,
    'resultHeight': 0,
    'mousedown': function(event) {
        if($.cookie('pma_console_mode') !== 'show')
            return;
        PMA_consoleResizer.posY = event.pageY;
        PMA_consoleResizer.height = PMA_console.$consoleContent.height();
        $(document).mousemove(PMA_consoleResizer.mousemove);
        $(document).mouseup(PMA_consoleResizer.mouseup);
        // Disable text selection while resizing
        $(document).bind('selectstart', function(){ return false; });
    },
    'mousemove': function(event) {
        PMA_consoleResizer.resultHeight = PMA_consoleResizer.height + (PMA_consoleResizer.posY -event.pageY);
        // Content min-height is 32, if adjusting height small than it we'll move it out of the page
        if(PMA_consoleResizer.resultHeight <= 32) {
            PMA_console.$consoleContent.height(32);
            PMA_console.$consoleContent.css('margin-bottom', PMA_consoleResizer.resultHeight - 32);
        }
        else {
            // Logic below makes viewable area always at bottom when adjusting height and content already at bottom
            if(PMA_console.$consoleContent.scrollTop() + PMA_console.$consoleContent.innerHeight() + 16
                >= PMA_console.$consoleContent.prop('scrollHeight')) {
                PMA_console.$consoleContent.height(PMA_consoleResizer.resultHeight);
                PMA_console.scrollBottom();
            } else {
                PMA_console.$consoleContent.height(PMA_consoleResizer.resultHeight);
            }
        }
    },
    'mouseup': function() {
        $.cookie('pma_console_height', PMA_consoleResizer.resultHeight);
        PMA_console.show();
        $(document).unbind('mousemove');
        $(document).unbind('mouseup');
        $(document).unbind('selectstart');
    },
    'initialize': function() {
        PMA_console.$consoleToolbar.unbind('mousedown');
        PMA_console.$consoleToolbar.mousedown(PMA_consoleResizer.mousedown);
    }
};


/**
 * Console input object
 */
var PMA_consoleInput = {
    cm: null,
    initialize: function() {
        // This object can't be reinitialize
        if(PMA_consoleInput.cm !== null)
            return;
        PMA_consoleInput.cm = CodeMirror($('#query_input')[0], {
            theme: 'pma',
            mode: 'text/x-sql',
            lineWrapping: true
        });
        $('#pma_console .CodeMirror.cm-s-pma').keydown(PMA_consoleInput.keydown);
    },
    keydown: function(event) {
        if(event.ctrlKey && event.keyCode == 13) {
            if(PMA_consoleInput.cm.getValue().length == 0){
                return;
            }
            PMA_console.execute(PMA_consoleInput.cm.getValue());
            PMA_consoleInput.clear();
            PMA_consoleInput.cm.clearHistory();
        }
    },
    clear: function() {
        PMA_consoleInput.cm.setValue('');
    }
};


/**
 * Console messages, and message items object
 */
var PMA_consoleMsg = {
    clear: function() {
        $('#pma_console .message_container').empty();
    },
    add: function(msgString, msgType) {
        if(typeof(msgString) !== 'string') {
            return;
        }
        var msgId = Math.round(Math.random()*(999999999999-100000000000)+100000000000);
        var $newMessage = $('<div class="message" msgid="' + msgId + '"></div>');
        switch(msgType) {
            case 'query':
                $newMessage.append('<span class="query collapsed"></span>');
                CodeMirror.runMode(msgString,
                    'text/x-sql', $newMessage.children('.query')[0]);
                break;
            default:
            case 'normal':
                $newMessage.append('<span>' + msgString + '</span>');
        }
        $('#pma_console .message_container')
            .append($newMessage);
    }
};