(function ($) {
    "use strict";
    var $window = $(window);
    var $body = $('body');
    var $document = $(document);
    function parse_query_string(a) {
        if (a === "")
            return {};
        var b = {};
        for (var i = 0; i < a.length; ++i)
        {
            var p = a[i].split('=');
            if (p.length !== 2)
                continue;
            b[p[0]] = decodeURIComponent(p[1].replace(/\+/g, " "));
        }
        return b;
    }
    $.QueryString = parse_query_string(window.location.search.substr(1).split('&'));
    if ('page' in $.QueryString && $.QueryString['page'] === 'azh-email-templates-settings') {
        $('.azm-email-template-upload').on('click', function (e) {
            e.preventDefault();

            var $input = $('#azm-email-template-upload');
            $input.off('change').on('change', function () {
                var file = $input.get(0).files[0];
                var xhr = new XMLHttpRequest();
                if (xhr.upload) {
                    xhr.upload.addEventListener("progress", function (e) {
                        $('.azm-progress .azm-status').width((e.loaded / e.total * 100) + '%');
                    }, false);
                    xhr.onreadystatechange = function (e) {
                        if (xhr.readyState === 4) {
                            if (xhr.status === 200) {
                                $('.azm-progress .azm-status').width('0%');
                                $input.off('change');
                                $input.val('');
                                if (xhr.response) {
                                    var template = JSON.parse(xhr.response);
                                    var $iframe = $('<iframe src="' + template.url + '"></iframe>').appendTo($body).on('load', function () {
                                        function process() {
                                            function cropped_screenshot(element, options) {
                                                options = options || {};
                                                // our cropping context
                                                var cropper = iframe_document.createElement('canvas').getContext('2d');
                                                // save the passed width and height
                                                var finalWidth = options.width || iframe_window.innerWidth;
                                                var finalHeight = options.height || iframe_window.innerHeight;
                                                // update the options value so we can pass it to h2c
                                                if (options.x) {
                                                    options.width = finalWidth + options.x;
                                                }
                                                if (options.y) {
                                                    options.height = finalHeight + options.y;
                                                }
                                                var userCallback = options.onrendered;
                                                // wrap the passed callback in our own
                                                options.onrendered = function (canvas) {
                                                    cropper.canvas.width = finalWidth;
                                                    cropper.canvas.height = finalHeight;
                                                    cropper.drawImage(canvas, -(+options.x || 0), -(+options.y || 0));
                                                    if (typeof userCallback === 'function') {
                                                        userCallback(cropper.canvas);
                                                    }
                                                };
                                                iframe_window.html2canvas(element, options);
                                            }
                                            if (loaded === 2) {
                                                (function ($) {
                                                    function upload_section(i, callback) {
                                                        window.jQuery('.azm-progress .azm-status').width(Math.round((i / sections.length) * 100) + '%');
                                                        if (i < sections.length) {
                                                            iframe_window.html2canvas(sections[i], {
                                                                width: 800
                                                            }).then(function (canvas) {
                                                                function toAbsoluteURL(url) {
                                                                    if (url.search(/^\/\//) != -1) {
                                                                        return iframe_window.location.protocol + url
                                                                    }
                                                                    if (url.search(/:\/\//) != -1) {
                                                                        return url
                                                                    }
                                                                    if (url.search(/^\//) != -1) {
                                                                        return iframe_window.location.origin + url
                                                                    }
                                                                    var base = iframe_window.location.href.match(/(.*\/)/)[0]
                                                                    return base + url;
                                                                }
                                                                $(sections[i]).find('img[src]').each(function () {
                                                                    $(this).attr('src', toAbsoluteURL($(this).attr('src')));
                                                                });
                                                                $(sections[i]).find('[background]').each(function () {
                                                                    $(this).attr('background', toAbsoluteURL($(this).attr('background')));
                                                                });
                                                                $(sections[i]).find('[style*="background-image"]').each(function () {
                                                                    var style = $(this).attr('style').replace(/background-image[: ]*url\([\'\" ]*([^\)\'\"]*)[\'\" ]*\) *;/, function (match, url) {
                                                                        return match.replace(url, encodeURI(toAbsoluteURL(decodeURI(url))));
                                                                    });
                                                                    $(this).attr('style', style);
                                                                });
                                                                $.post(azm.ajaxurl, {
                                                                    action: 'azm_upload_section',
                                                                    template: template.name,
                                                                    name: i,
                                                                    html: $('<div>').append($(sections[i]).clone()).html(),
                                                                    preview: canvas.toDataURL("image/jpeg", 0.95)
                                                                }, function (data) {
                                                                    upload_section(i + 1, function () {
                                                                        callback();
                                                                    });
                                                                });
                                                            });
                                                        } else {
                                                            window.jQuery('.azm-progress').fadeOut("slow");
                                                            callback();
                                                        }
                                                    }
                                                    cropped_screenshot(iframe_document.body, {
                                                        x: 0,
                                                        y: 0,
                                                        width: 800,
                                                        height: 500,
                                                        onrendered: function (canvas) {
                                                            var styles = $('style').toArray().map(function (element) {
                                                                return $(element).html();
                                                            }).join("\n");
                                                            var stylesheets = $('link[type="text/css"]').toArray().map(function (element) {
                                                                return $('<div>').append($(element).clone()).html();
                                                            }).join("\n");
                                                            var $head = $('head').clone();
                                                            $head.find('style').detach();
                                                            $head.find('link[type="text/css"]').detach();
                                                            $.post(azm.ajaxurl, {
                                                                action: 'azm_upload_section',
                                                                template: template.name,
                                                                name: 'index',
                                                                styles: styles,
                                                                stylesheets: stylesheets,
                                                                preview: canvas.toDataURL("image/jpeg", 0.95)
                                                            }, function (data) {
                                                            });
                                                        },
                                                        useCORS: true
                                                    });
                                                    var sections = $('body > div, body > table').toArray();
                                                    upload_section(0, function () {
                                                        alert(azm.i18n.done);
                                                    });
                                                })(iframe_window.jQuery);
                                            }
                                        }
                                        var iframe_body = $iframe.contents().find('body').get(0);
                                        var iframe_window = $iframe.get(0).contentWindow;
                                        var iframe_document = $iframe.get(0).contentDocument || $iframe.contentWindow.document;
                                        var loaded = 0;
                                        var jquery = iframe_document.createElement('script');
                                        jquery.type = 'text/javascript';
                                        jquery.src = azm.jquery;
                                        iframe_document.body.appendChild(jquery);
                                        jquery.onload = function () {
                                            loaded++;
                                            process();
                                        };
                                        var html2canvas = iframe_document.createElement('script');
                                        html2canvas.type = 'text/javascript';
                                        html2canvas.src = azm.html2canvas;
                                        iframe_document.body.appendChild(html2canvas);
                                        html2canvas.onload = function () {
                                            loaded++;
                                            process();
                                        };
                                    });
                                    $iframe.css({
                                        'width': '800px',
                                        'height': '500px',
                                        'position': 'absolute',
                                        'visibility': 'hidden'
                                    });
                                }
                            }
                        }
                    };
                    xhr.open("POST", azm.ajaxurl + '?action=azm_upload_template&format=stampready', true);
                    xhr.setRequestHeader("X-FILENAME", file.name);
                    xhr.send(file);
                    $('.azm-progress .azm-status').width('0%');
                    $('.azm-progress').fadeIn("slow");
                }
            });
            $input.trigger('click');
        });
    }
    $(function () {
        function get_forms_fields() {
            var forms_fields = {};
            for (var form_title in azm.forms) {
                for (var name in azm.forms[form_title]) {
                    if (name !== 'form_title') {
                        forms_fields[name] = azm.forms[form_title][name].label;
                    }
                }
            }
            for (var form_title in azm.import_forms) {
                for (var name in azm.import_forms[form_title]) {
                    forms_fields[name] = azm.import_forms[form_title][name];
                }
            }
            return forms_fields;
        }
        function get_forms() {
            var forms = {};
            for (var form_title in azm.forms) {
                forms[form_title] = azm.forms[form_title];
            }
            for (var form_title in azm.import_forms) {
                forms[form_title] = azm.import_forms[form_title];
            }
            return forms;
        }
        function open_modal(options, values, callback) {
            var $modal = $('<div class="azm-modal"></div>');
            $('<div class="azm-modal-title">' + options['title'] + '</div>').appendTo($modal);
            $('<div class="azm-modal-desc">' + options['desc'] + '</div>').appendTo($modal);
            var $controls = $('<div class="azm-modal-controls"></div>').appendTo($modal);
            $controls.css('column-count', '2');
            if ('columns' in options) {
                $controls.css('column-count', options.columns);
            }
            if ('fields' in options) {
                for (var name in options['fields']) {
                    (function (name) {
                        var field = options['fields'][name];
                        var $control = $('<div class="azm-modal-control"></div>').appendTo($controls);
                        $('<div class="azm-modal-label">' + field['label'] + '</div>').appendTo($control);
                        if ('options' in field) {
                            var $select = $('<select ' + (('multiple' in field && field['label']) ? 'multiple' : '') + '></select>').appendTo($control).on('change', function () {
                                if (('multiple' in field && field['label'])) {
                                    values[name] = $(this).find('option:selected').map(function () {
                                        return $(this).attr('value');
                                    }).toArray();
                                } else {
                                    values[name] = $(this).find('option:selected').attr('value');
                                }
                            });
                            for (var value in field['options']) {
                                if (value === values[name] || ('multiple' in field && values[name].indexOf(value) >= 0)) {
                                    $('<option value="' + value + '" selected>' + field['options'][value] + '</option>').appendTo($select);
                                } else {
                                    $('<option value="' + value + '">' + field['options'][value] + '</option>').appendTo($select);
                                }
                            }
                            $select.trigger('change');
                        } else {
                            $('<input type="text" value="' + values[name] + '">').appendTo($control).on('change', function () {
                                values[name] = $(this).val();
                            });
                        }
                    })(name);
                }
            }
            var $actions = $('<div class="azm-modal-actions"></div>').appendTo($modal);
            $('<div class="azm-modal-ok">' + azm.i18n.ok + '</div>').appendTo($actions).on('click', function () {
                $.simplemodal.close();
                setTimeout(function () {
                    callback(values);
                }, 0);
                return false;
            });
            $('<div class="azm-modal-cancel">' + azm.i18n.cancel + '</div>').appendTo($actions).on('click', function () {
                $.simplemodal.close();
                return false;
            });
            $modal.simplemodal({
                position: [100, 'center'],
                autoResize: true,
                overlayClose: true,
                opacity: 0,
                overlayCss: {
                    "background-color": "black"
                },
                closeClass: "azm-close",
                onClose: function () {
                    setTimeout(function () {
                        $.simplemodal.close();
                    }, 0);
                }
            });
        }
        function open_simple_modal(options, value, callback) {
            var $modal = $('<div class="azm-modal"></div>');
            $('<div class="azm-modal-title">' + options['title'] + '</div>').appendTo($modal);
            $('<div class="azm-modal-desc">' + options['desc'] + '</div>').appendTo($modal);
            $('<div class="azm-modal-label">' + options['label'] + '</div>').appendTo($modal);
            if ('options' in options) {
                var $select = $('<select class="azm-modal-control"></select>').appendTo($modal).on('change', function () {
                    value = $(this).find('option:selected').attr('value');
                });
                for (var v in options['options']) {
                    if (v === value) {
                        $('<option value="' + v + '" selected>' + options['options'][v] + '</option>').appendTo($select);
                    } else {
                        $('<option value="' + v + '">' + options['options'][v] + '</option>').appendTo($select);
                    }
                }
                $select.trigger('change');
            } else {
                $('<input type="text" value="' + value + '" class="azm-modal-control">').appendTo($modal).on('change', function () {
                    value = $(this).val();
                });
            }
            var $actions = $('<div class="azm-modal-actions"></div>').appendTo($modal);
            $('<div class="azm-modal-ok">' + azm.i18n.ok + '</div>').appendTo($actions).on('click', function () {
                $.simplemodal.close();
                setTimeout(function () {
                    callback(value);
                }, 0);
                return false;
            });
            $('<div class="azm-modal-cancel">' + azm.i18n.cancel + '</div>').appendTo($actions).on('click', function () {
                $.simplemodal.close();
                return false;
            });
            $modal.simplemodal({
                position: ['200px', 'center'],
                autoResize: true,
                overlayClose: true,
                opacity: 0,
                overlayCss: {
                    "background-color": "black"
                },
                closeClass: "azm-close",
                onClose: function () {
                    setTimeout(function () {
                        $.simplemodal.close();
                    }, 0);
                }
            });
        }
        function open_mapping_modal(examples, total, fields, callback) {
            var $modal = $('<div class="azm-modal"></div>');
            var mapping = {};
            var options = {
                id_fields: {},
                existing_leads: 'skip'
            };
            for (var name in examples[0]) {
                mapping[name] = name;
            }
            $('<div class="azm-modal-title">' + azm.i18n.select_columns + '</div>').appendTo($modal);
            $('<div class="azm-modal-desc">' + azm.i18n.define_which_column_represents_which_field + '</div>').appendTo($modal);
            var $table = $('<table></table>');
            var $head = $('<thead></thead>').appendTo($table);
            $head = $('<tr></tr>').appendTo($head);
            for (var name in examples[0]) {
                (function (name) {
                    var $select = $('<select></select>').on('change', function () {
                        if ($(this).val() === '') {
                            $input.show();
                            $checkbox.hide();
                        } else {
                            $input.hide();
                            $checkbox.show();
                        }
                        mapping[name] = $(this).val();
                    });
                    $('<option value="">' + azm.i18n.select_available_field + '</option>').appendTo($select);
                    for (var field in fields) {
                        $('<option value="' + field + '">' + fields[field] + '</option>').appendTo($select);
                    }
                    var $input = $('<input type="text" placeholder="' + azm.i18n.or_define_new_field_name + '" value="">').on('change', function () {
                        mapping[name] = $(this).val();
                    });
                    var $checkbox = $('<div class="azm-id-field"></div>');
                    $('<input id="azm-id-field-' + name + '" type="checkbox" value="' + name + '">').on('change', function () {
                        if ($(this).prop('checked')) {
                            options.id_fields[name] = true;
                            $existing_leads.show();
                            $.simplemodal.update($('.azm-modal').outerHeight());
                        } else {
                            options.id_fields[name] = false;
                            var exists = false;
                            for (var n in options.id_fields) {
                                if (options.id_fields[n]) {
                                    exists = true;
                                    break;
                                }
                            }
                            if (!exists) {
                                $existing_leads.hide();
                                $.simplemodal.update($('.azm-modal').outerHeight());
                            }
                        }
                    }).appendTo($checkbox);
                    $checkbox.append('<div class="azm-checkbox"><label for="azm-id-field-' + name + '"></label></div><span class="azm-label">' + azm.i18n.use_as_id + '</span>');
                    var $tr = $('<th><div>' + name + '</div></th>').appendTo($head);
                    $tr.find('div').after($select);
                    $select.after($input);
                    $input.after($checkbox);
                    $checkbox.hide();
                })(name);
            }
            var $body = $('<tbody></tbody>').appendTo($table);
            $(examples).each(function () {
                var $tr = $('<tr></tr>').appendTo($body);
                for (var name in this) {
                    $('<td>' + this[name] + '</td>').appendTo($tr);
                }
            });
            var $tr = $('<tr></tr>').appendTo($body);
            for (var name in examples[0]) {
                $('<td>...</td>').appendTo($tr);
            }
            var $options = $('<div class="azm-import-options"></div>');
            var $existing_leads = $('<div class="azm-option"><div class="azm-option-title">' + azm.i18n.existing_leads + '</div></div>').appendTo($options);
            var $radio = $('<div class="azm-radio"></div>').appendTo($existing_leads);
            $('<input id="azm-skip" value="skip" checked name="azm-existing-leads" type="radio">').appendTo($radio).on('change', function () {
                if ($(this).prop('checked')) {
                    options.existing_leads = $(this).val();
                }
            });
            $('<label for="azm-skip">' + azm.i18n.skip + '</label>').appendTo($radio);
            $('<input id="azm-overwrite" value="overwrite" name="azm-existing-leads" type="radio">').appendTo($radio).on('change', function () {
                if ($(this).prop('checked')) {
                    options.existing_leads = $(this).val();
                }
            });
            $('<label for="azm-overwrite">' + azm.i18n.overwrite + '</label>').appendTo($radio);
            $('<input id="azm-merge" value="merge" name="azm-existing-leads" type="radio">').appendTo($radio).on('change', function () {
                if ($(this).prop('checked')) {
                    options.existing_leads = $(this).val();
                }
            });
            $('<label for="azm-merge">' + azm.i18n.merge + '</label>').appendTo($radio);
            $existing_leads.hide();
            var $mapping = $('<div class="azm-mapping"></div>').appendTo($modal);
            $table.appendTo($mapping);
            $options.appendTo($modal);
            var $actions = $('<div class="azm-modal-actions"></div>').appendTo($modal);
            $('<div class="azm-modal-ok">' + azm.i18n.import + ' ' + total + ' ' + azm.i18n.rows + '</div>').appendTo($actions).on('click', function () {
                $.simplemodal.close();
                setTimeout(function () {
                    callback(options, mapping);
                }, 0);
                return false;
            });
            $('<div class="azm-modal-cancel">' + azm.i18n.cancel + '</div>').appendTo($actions).on('click', function () {
                $.simplemodal.close();
                return false;
            });
            $modal.simplemodal({
                position: [100, 'center'],
                autoResize: true,
                overlayClose: true,
                opacity: 0,
                overlayCss: {
                    "background-color": "black"
                },
                closeClass: "azm-close",
                onClose: function () {
                    setTimeout(function () {
                        $.simplemodal.close();
                    }, 0);
                }
            });
        }
        function refresh_tokens() {
            var form_title = $form_title.val();
            var tokens = [];
            $email_field.children('[data-form-title="' + form_title + '"]').each(function () {
                tokens.push('<input type="text" value="{' + $(this).attr('value') + '}"/>');
            });
            $('.azm-tokens').html(tokens.join(' '));
            $('.azm-tokens input').each(function () {
                $(this).attr('size', $(this).val().length);
            });
        }
        var $email_field = $('[name="_email_field"]');
        var $form_title = $('[name="_form_title"]').on('change', function (event) {
            var form_title = $(this).val();
            $email_field.children().show().not('[data-form-title="' + form_title + '"]').hide();
            if ($email_field.children('[data-form-title="' + form_title + '"]').filter('[value="email"]').length) {
                $email_field.val('email');
            } else {
                $email_field.val($email_field.children('[data-form-title="' + form_title + '"]').first().attr('value'));
            }
            refresh_tokens();
        });
        if (!$form_title.children('[selected]').length) {
            $form_title.trigger('change');
        } else {
            refresh_tokens();
        }
        $('[data-rule] .azm-pause').on('click', function () {
            var $status = $(this).closest('[data-rule]');
            var id = $status.data('rule');
            $.post(azm.ajaxurl, {
                action: 'azr_pause_rule',
                rule: id
            }, function (status) {
                $status.attr('data-status', status);
            });
        });
        $('[data-rule] .azm-run').on('click', function () {
            var $status = $(this).closest('[data-rule]');
            var id = $status.data('rule');
            $.post(azm.ajaxurl, {
                action: 'azr_run_rule',
                rule: id
            }, function (status) {
                $status.attr('data-status', status);
            });
        });
        $('.azm-leads-import').on('click', function (e) {
            e.preventDefault();
            var options = {};
            $.each(Object.keys(azm.forms), function (i, value) {
                options[value] = value;
            });
            open_simple_modal({
                'title': azm.i18n.source_of_leads,
                'desc': '',
                'options': options,
                'label': azm.i18n.form_name
            }, '', function (form_title) {
                if (form_title) {
                    var $input = $('#azm-leads-import');
                    $input.off('change').on('change', function () {
                        var file = $input.get(0).files[0];
                        var xhr = new XMLHttpRequest();
                        if (xhr.upload) {
                            xhr.upload.addEventListener("progress", function (e) {
                                $('.azm-progress .azm-status').width((e.loaded / e.total * 100) + '%');
                            }, false);
                            xhr.onreadystatechange = function (e) {
                                if (xhr.readyState === 4) {
                                    if (xhr.status === 200) {
                                        $('.azm-progress .azm-status').width('100%');
                                        $('.azm-progress').fadeOut("slow");
                                        $input.off('change');
                                        $input.val('');
                                        var data = JSON.parse(xhr.response);
                                        if (data && 'examples' in data && 'total' in data && data.examples.length > 0) {
                                            open_mapping_modal(data.examples, data.total, get_forms_fields(), function (options, mapping) {
                                                function processing(position) {
                                                    if (current < data.total) {
                                                        $('.azm-progress .azm-status').width((current / data.total * 100) + '%');
                                                        $.post(azm.ajaxurl, {
                                                            action: 'azm_leads_import',
                                                            file_path: data.file_path,
                                                            form_title: form_title,
                                                            options: options,
                                                            mapping: mapping,
                                                            position: position
                                                        }, function (response) {
                                                            response = JSON.parse(response);
                                                            current += response.imported;
                                                            processing(response.position);
                                                        });
                                                    } else {
                                                        $('.azm-progress .azm-status').width('100%');
                                                        $('.azm-progress').fadeOut("slow");
                                                    }
                                                }
                                                $('.azm-progress .azm-operation').text(azm.i18n.import_progress);
                                                $('.azm-progress').fadeIn("slow");
                                                var current = 0;
                                                if (!azm.import_forms) {
                                                    azm.import_forms = {};
                                                }
                                                azm.import_forms[form_title] = {};
                                                for (var name in mapping) {
                                                    azm.import_forms[form_title][mapping[name]] = mapping[name];
                                                }
                                                $.post(azm.ajaxurl, {
                                                    action: 'azm_update_user',
                                                    user: {
                                                        ID: azm.user_id
                                                    },
                                                    meta: {
                                                        import_forms: azm.import_forms
                                                    }
                                                });
                                                processing(0);
                                            });
                                        }
                                    }
                                }
                            };
                            xhr.open("POST", azm.ajaxurl + '?action=azm_leads_import', true);
                            xhr.setRequestHeader("X-FILENAME", file.name);
                            xhr.send(file);
                            $('.azm-progress .azm-status').width('0%');
                            $('.azm-progress .azm-operation').text(azm.i18n.upload_progress);
                            $('.azm-progress').fadeIn("slow");
                        }
                    });
                    $('#azm-leads-import').trigger('click');
                }
            });
        });
        $('.azm-leads-delete').on('click', function (e) {
            e.preventDefault();
            var options = {};
            $.each(Object.keys(azm.forms), function (i, value) {
                options[value] = value;
            });
            open_simple_modal({
                'title': azm.i18n.source_of_leads,
                'desc': '',
                'options': options,
                'label': azm.i18n.form_name
            }, '', function (form_title) {
                if (form_title) {
                    $('.azm-progress .azm-status').width('0%');
                    $('.azm-progress .azm-operation').text(azm.i18n.leads_deleting_progress);
                    $('.azm-progress').fadeIn("slow");
                    function processing(number) {
                        $.post(azm.ajaxurl, {
                            action: 'azm_leads_delete',
                            nonce: azm.nonce,
                            form: form_title,
                            number: number
                        }, function (left) {
                            left = parseInt(left, 10);
                            if (!total) {
                                total = left;
                            }
                            $('.azm-progress .azm-status').width(((total - left) / total * 100) + '%');
                            if (left > 0) {
                                processing(10);
                            } else {
                                $('.azm-progress .azm-status').width('100%');
                                $('.azm-progress').fadeOut("slow");
                            }
                        });
                    }
                    var total = false;
                    processing(0);
                }
            });
        });
        $('.azm-leads-export').on('click', function (e) {
            e.preventDefault();
            var $button = $(this);
            var forms_fields = get_forms_fields();
            forms_fields['id'] = azm.i18n.lead_id;
            forms_fields['timestamp'] = azm.i18n.lead_timestamp;
            forms_fields['post_date'] = azm.i18n.lead_post_date;
            forms_fields['page'] = azm.i18n.page_of_lead;
            forms_fields['form_title'] = azm.i18n.form_of_lead;
            var forms = {};
            Object.keys(get_forms()).forEach(function (value, index) {
                forms[value] = Object.keys(get_forms())[index];
            });
            open_modal({
                title: azm.i18n.leads_export,
                desc: '',
                fields: {
                    form: {
                        label: azm.i18n.form_name,
                        options: forms,
                        multiple: true
                    },
                    fields: {
                        label: azm.i18n.fields,
                        options: forms_fields,
                        multiple: true
                    }
                }
            }, {form: [], fields: Object.keys(forms_fields)}, function (settings) {
                window.location = $button.attr('href') + '?form=' + settings.form.join(',') + '&fields=' + settings.fields.join(',');
            });
        });
        if ($('body.post-type-azr_rule').length) {
            $window.on('azr-refresh', function () {
                $('.azr-action > .azr-type > select').each(function () {
                    var $select = $(this);
                    var $type = $select.closest('.azr-type');

                    $type.find('.azm-email-sending-test').remove();
                    if (['send_text_email', 'send_html_email'].indexOf($select.val()) >= 0) {
                        $('<div class="azm-email-sending-test button button-primary button-large">' + azm.i18n.send_test_email_to + azm.admin_email + '</div>').appendTo($type).on('click', function () {
                            $.post(azm.ajaxurl, {
                                action: 'azm_email_sending_test',
                                parameters: JSON.stringify($type.closest('.azr-action').data('obj'))
                            }, function (result) {
                                if (result) {
                                    alert(result);
                                } else {
                                    alert(azm.i18n.something_went_wrong);
                                }
                            });
                            return false;
                        });
                    }

                    $type.find('.azm-sms-sending-test').remove();
                    if (['send_sms'].indexOf($select.val()) >= 0) {
                        $('<div class="azm-sms-sending-test button button-primary button-large">' + azm.i18n.send_test_sms + '</div>').appendTo($type).on('click', function () {
                            var phone = prompt(azm.i18n.enter_receiver_phone_number);
                            if (phone) {
                                $.post(azm.ajaxurl, {
                                    action: 'azm_sms_sending_test',
                                    parameters: JSON.stringify($type.closest('.azr-action').data('obj')),
                                    phone: phone
                                }, function (result) {
                                    if (result) {
                                        alert(result);
                                    } else {
                                        alert(azm.i18n.something_went_wrong);
                                    }
                                });
                            }
                            return false;
                        });
                    }
                });
                if ($('.azr-rule').is('.azr-valid')) {
                    $('#minor-publishing-actions, #major-publishing-actions').show();
                } else {
                    $('#minor-publishing-actions, #major-publishing-actions').hide();
                    $('.azm-email-sending-test').remove();
                }
            });
        }
        if ($('body.azexo-builder_page_azh-bouncing-settings').length) {
            $('.bounce-test').on('click', function () {
                var $button = $(this);
                $('.bounce-test-status').remove();
                $.post(azm.ajaxurl, {
                    action: 'azm_bounce_test',
                    send: true
                }, function (status) {
                    function check() {
                        $button.text($button.text() + '.');
                        pass++;
                        $.post(azm.ajaxurl, {
                            action: 'azm_bounce_test',
                            check: true
                        }, function (status) {
                            if (isNaN(parseInt(status, 10))) {
                                $button.after('<div class="bounce-test-status wp-ui-text-notification">' + status + '</div>');
                            } else {
                                if (parseInt(status, 10) > 0) {
                                    $button.after('<div class="bounce-test-status">' + azm.i18n.successful + '</div>');
                                } else {
                                    if (pass < 20) {
                                        check();
                                    } else {
                                        $button.after('<div class="bounce-test-status">' + azm.i18n.failed + '</div>');
                                    }
                                }
                            }
                        });
                    }
                    var pass = 0;
                    if (isNaN(parseInt(status, 10))) {
                        $button.after('<div class="bounce-test-status wp-ui-text-notification">' + status + '</div>');
                    } else {
                        check();
                    }
                });
                return false;
            });
        }
    });
})(window.jQuery);


