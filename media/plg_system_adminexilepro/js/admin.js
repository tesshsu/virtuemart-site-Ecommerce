(function ($) {
    $(document).ready(function () {
        var AdminExilePro = function (options) {
            var root = this;

            var vars = {
                invalidChars: {
                    32: 'SPACE', // Space
                    34: 'QUOTE', // "
                    35: 'POUND', // #
                    36: 'DOLLAR', // $
                    37: 'PERCENT', // %
                    38: 'AMPERSAND', // &
                    43: 'PLUS', // +
                    44: 'COMMA', // ,
                    46: 'PERIOD', // .
                    47: 'FORWARDSLASH', // /
                    58: 'COLON', // :
                    59: 'SEMICOLON', // ;
                    60: 'LESSTHAN', // <
                    61: 'EQUALS', // =
                    62: 'GREATERTHAN', // >
                    63: 'QUESTION', // ?
                    64: 'AT', // @
                    91: 'LEFTBRACKET', // [
                    92: 'BACKSLASH', // \
                    93: 'RIGHTBRACKET', // ]
                    94: 'CARAT', // ^
                    96: 'GRAVE', // `
                    123: 'LEFTCURLY', // {
                    125: 'RIGHTCURLY', // }
                    124: 'PIPE', // |
                    126: 'TILDE'        // ~
                },
            }

            this.construct = function (options) {
                $.extend(vars, options);
                Joomla.JText.load();

                $.each(['#jform_params_key', '#jform_params_keyvalue'], function (i, item) {
                    $(item).keyup(function () {
                        testInput(item, $(item).val());
                    });
                });
                $('#jform_params_twofactor0').click(function() { displayURL(); });
                $('#jform_params_twofactor1').click(function() { displayURL(); });
                displayURL();
                
                /* pro feature start */
                getIPStats();
                /* pro features end */
            };

            var testInput = function (type, str) {
                var self = this;
                if (type === '#jform_params_key' && (/^[0-9]+$/.test(str))) {
                    $(type).val('');
                    alert(Joomla.JText._('PLG_SYS_ADMINEXILEPRO_MESSAGE_NOTNUMERIC'));
                    return;
                }
                if (!(/^[\040-\177]*$/.test(str))) {
                    while (!(/^[\040-\177]*$/.test(str)))
                        for (i = 0; i <= (str.length - 1); i++)
                            if (!(/^[\040-\177]*$/.test(str.charAt(i))))
                                $(type).val(str.replace(str.charAt(i), ''));
                    alert(Joomla.JText._('PLG_SYS_ADMINEXILEPRO_MESSAGE_INVALIDASCII'));
                    return;
                }
                for (i = 0; i <= (str.length - 1); i++) {
                    if (vars.invalidChars.hasOwnProperty(str.charCodeAt(i))) {
                        $(type).val(str.replace(str.charAt(i), ''));
                        alert(Joomla.JText._('PLG_SYS_ADMINEXILEPRO_MESSAGE_INVALIDCHAR') + "\n\n" + validCharsMesssage());
                        return;
                    }
                }
                displayURL();
            }

            var validCharsMesssage = function () {
                var str = [], name, char;
                $.each(vars.invalidChars, function (key,value) {
                    char = String.fromCharCode(key);
                    name = Joomla.JText._('PLG_SYS_ADMINEXILEPRO_CHAR_' + value)
                    str.push(char + '\t\t:\t\t' + name);
                });
                str = str.join('\n');
                return str;
            }

            var displayURL = function () {
                var adminurl = vars.uri.replace(/\/$/,'')+'/administrator';
                if ($('#jform_params_twofactor0')[0].checked) {
                    adminurl+=("?"+$('#jform_params_key').val());
                } else {
                    var data = {};
                    data[$('#jform_params_key').val()] = $('#jform_params_keyvalue').val();
                    adminurl+=('?'+$.param(data));
                }
                var target = $('#jform_params_url-lbl').parent('span').next('span')[0];
                $(target).empty();
                $(target).append($('<a href="'+adminurl+'">'+adminurl+'</a>'));
            }
            
            /* pro feature start */        
            var getIPStats = function() {          
                $.getJSON({
                    url: vars.uri+'/administrator?option=com_ajax&format=json&plugin=adminexilepro&method=stats',
                    cache:false,
                    success: function (data) {
                        var network, address, cidr, searchattr, resultarray, 
                                inputmatch, rowmatch, groupmatch, matchlabel, 
                                matchparent, target, table, start, end, 
                                failabove, multiply, penalty, type;
                        if(data.data[0] !== false) {
                            $.each(data.data[0].firewall,function(i,r){
                                network = i.split('/');
                                address = network[0];
                                cidr = network[1];
                                type = ((r.type==="1")?'whitelist':'blacklist');
                                searchattr = 'input[id^="jform_params__'+type+'__'+type+'"][id$="__address"][value="'+address+'"]';
                                resultarray = $(searchattr);
                                if(resultarray.length) {
                                    inputmatch = resultarray[0];
                                    rowmatch = $(inputmatch).closest('tr')[0];
                                    target = $(rowmatch).find('span.spacer')[0];
                                    $(target).empty().html(r.count+' ');
                                }
                            });
                            
                            if(Number(vars.enablebruteforce)===1) {
                                target = $('#jform_params_bfblocked-lbl').closest('span.spacer').find('span.after')[0];
                                $(target).empty();
                                table = $('<table class="table table-striped table-bordered"></table>').appendTo(target);
                                $('<tr><th>'+Joomla.JText._('PLG_SYS_ADMINEXILEPRO_BFTABLE_ADDRESS')+'</th><th>'+Joomla.JText._('PLG_SYS_ADMINEXILEPRO_BFTABLE_FAILS')+'</th><th>'+Joomla.JText._('PLG_SYS_ADMINEXILEPRO_BFTABLE_PENALTYSTART')+'</th><th>'+Joomla.JText._('PLG_SYS_ADMINEXILEPRO_BFTABLE_PENALTYEND')+'</th><th></th>'+IPSecurityEnabled('<th></th>')+'</tr>').appendTo(table);
                                $.each(data.data[0].bruteforce,function(i,r){
                                    if(r.fail >= Number(vars.bfthreshold)) {
                                        start = new Date(1000*r.ts);
                                        end = new Date(1000*r.expire);
                                        $('<tr id="bruteforce'+r.address.replace(/[\.:]/g,'')+'"><td>'+r.address+'</td><td>'+r.fail+'</td><td>'+start.toString()+'</td><td>'+end.toString()+'</td><td><a href="#" class="btn btn-small" data-bruteforce="'+r.address+'">'+Joomla.JText._('PLG_SYS_ADMINEXILEPRO_BFTABLE_DELETE')+'</a></td>'+IPSecurityEnabled('<td><a href="#" class="btn btn-small" data-blacklist="'+r.address+'">'+Joomla.JText._('PLG_SYS_ADMINEXILEPRO_BFTABLE_BLACKLIST')+'</a></td>')+'</tr>').appendTo(table);
                                        $('a[data-bruteforce="'+r.address+'"]').click(function(e){
                                            e.preventDefault();
                                            e.stopPropagation();
                                            root.deleteBF($(this).data('bruteforce'));
                                        })
                                        $('a[data-blacklist="'+r.address+'"]').click(function(e){
                                            e.preventDefault();
                                            e.stopPropagation();
                                            root.blacklistBF($(this).data('blacklist'));
                                        })
                                    }
                                });
                            }
                        }
                    },
                    complete: function () {
                        // Schedule the next request when the current one's complete
                        setTimeout(getIPStats, 5000);
                    }
                });
            };
            
            var IPSecurityEnabled = function(ret) {
                if(Number(vars.enableip)===1) {
                    return ret;
                }
                return '';
            };
            
            this.deleteBF = function(address) {
                $.getJSON({
                    url: vars.uri+'/administrator?option=com_ajax&format=json&plugin=adminexilepro&method=delete&address='+encodeURIComponent(address),
                    success: function (data) {
                        if(data.data[0] === true) {
                            $('#bruteforce'+address.replace(/[\.:]/g,'')).remove();
                        }
                    }
                });
            }
            
            this.blacklistBF = function(address) {
                if(!confirm(Joomla.JText._('PLG_SYS_ADMINEXILEPRO_BFTABLE_CONFIRM_BLACKLIST'))) return;
                $.getJSON({
                    url: vars.uri+'/administrator?option=com_ajax&format=json&plugin=adminexilepro&method=delete&address='+encodeURIComponent(address),
                    success: function (data) {
                        if(data.data[0] === true) {
                            var blacklist = $('input[name="jform[params][blacklist]"]')[0];
                            var divctrl = $(blacklist).parents('div.controls')[0];
                            var addbtn = $(divctrl).find('a.group-add')[0];
                            $(addbtn).click();
                            var input = $(divctrl).find('input.validate-ipaddress').last();
                            input.val(address);
                            $('#bruteforce'+address.replace(/[\.:]/g,'')).remove();
                            $('#toolbar-apply').find('button').click();
                        }
                    }
                });                
            }
            /* pro feature end */
            
            
            this.construct(options);
        }
        
        var validIPv46 = function(ip) {
            var regex;
            regex = new RegExp('(^\s*((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)(/(3[012]|[12]?[0-9]))?)\s*$)|(^\s*((([0-9A-Fa-f]{1,4}:){7}([0-9A-Fa-f]{1,4}|:))|(([0-9A-Fa-f]{1,4}:){6}(:[0-9A-Fa-f]{1,4}|((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){5}(((:[0-9A-Fa-f]{1,4}){1,2})|:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){4}(((:[0-9A-Fa-f]{1,4}){1,3})|((:[0-9A-Fa-f]{1,4})?:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){3}(((:[0-9A-Fa-f]{1,4}){1,4})|((:[0-9A-Fa-f]{1,4}){0,2}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){2}(((:[0-9A-Fa-f]{1,4}){1,5})|((:[0-9A-Fa-f]{1,4}){0,3}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){1}(((:[0-9A-Fa-f]{1,4}){1,6})|((:[0-9A-Fa-f]{1,4}){0,4}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(:(((:[0-9A-Fa-f]{1,4}){1,7})|((:[0-9A-Fa-f]{1,4}){0,5}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:)))(%.+)?\s*$)','i');
            return regex.test(ip);
        }
        
        window.plg_sys_adminexilepro = new AdminExilePro(window.plg_sys_adminexilepro_config);
        
        /* pro feature start */
        document.formvalidator.setHandler('ipaddress', function(ip) {
            var regex;
            regex = new RegExp('(^\s*((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)(/(3[012]|[12]?[0-9]))?)\s*$)|(^\s*((([0-9A-Fa-f]{1,4}:){7}([0-9A-Fa-f]{1,4}|:))|(([0-9A-Fa-f]{1,4}:){6}(:[0-9A-Fa-f]{1,4}|((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){5}(((:[0-9A-Fa-f]{1,4}){1,2})|:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){4}(((:[0-9A-Fa-f]{1,4}){1,3})|((:[0-9A-Fa-f]{1,4})?:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){3}(((:[0-9A-Fa-f]{1,4}){1,4})|((:[0-9A-Fa-f]{1,4}){0,2}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){2}(((:[0-9A-Fa-f]{1,4}){1,5})|((:[0-9A-Fa-f]{1,4}){0,3}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){1}(((:[0-9A-Fa-f]{1,4}){1,6})|((:[0-9A-Fa-f]{1,4}){0,4}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(:(((:[0-9A-Fa-f]{1,4}){1,7})|((:[0-9A-Fa-f]{1,4}){0,5}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:)))(%.+)?\s*$)','i');
            return regex.test(ip);
        });
        /* pro feature end */
    });
})(jQuery)
