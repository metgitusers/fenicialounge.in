(function ($) {
    "use strict";
    var $window = $(window);
    var $body = $('body');
    var $document = $(document);
    $(function () {
        function grouped_select2_scroll($group_header) {
            var top = 0;
            $group_header.closest('.select2-results__option').prevAll().each(function () {
                top = top + $(this).outerHeight();
            });
            $('#' + $('.azexo-grouped-select2 .select2-results__options').attr('id')).scrollTop(top);
        }
        function grouped_select2($select) {
            $select.select2({
                dropdownAutoWidth: 'true',
                minimumResultsForSearch: Infinity,
                dropdownCssClass: 'azexo-grouped-select2',
                width: 'calc(100% - 70px)'
            });
            $select.on('select2:open', function () {
                setTimeout(function () {
                    $('.azexo-grouped-select2 .select2-results .select2-results__option--highlighted[aria-selected]').each(function () {
                        var $options = $(this).closest('.select2-results__options');
                        var $header = $options.prev('.select2-results__group');
                        $header.parent('.select2-results__option[role="group"]').addClass('azexo-group-expanded');
                        $options.show();
                        grouped_select2_scroll($header);
                    });
                });
            });
        }
        $body = $('body');
        $body.on({
            click: function () {
                var $header = $(this);
                if ($header.is('.select2-results__group')) {
                    var $group = $header.closest('.select2-results__option[role="group"]');
                    var $options = $group.find('.select2-results__options');
                    if ($options.is(':hidden')) {
                        var $root = $group.parent();
                        $group.addClass();
                        $root.find('.select2-results__option[role="group"] .select2-results__options').not($options).each(function () {
                            $(this).parent('.select2-results__option[role="group"]').removeClass('azexo-group-expanded');
                            $(this).hide();
                        });
                        $options.show();
                    } else {
                        $group.removeClass('azexo-group-expanded');
                        $options.hide();
                    }
                }
            }
        }, '.azexo-grouped-select2 .select2-results > .select2-results__options > .select2-results__option > .select2-results__group');
        $('textarea.azr-rule').each(function () {
            function create_parameter(parameter, settings, obj) {
                var $parameter = $('<div data-parameter="' + parameter + '"></div>');
                $('<div class="azr-label">' + settings.label + '</div>').appendTo($parameter);
                switch (settings.type) {
                    case 'autocomplete':
                        if (settings.options || settings.dependent_options && Object.keys(settings.dependent_options).length) {
                            var $select = $('<select ' + (settings.required ? 'required' : '') + '></select>').appendTo($parameter).on('change', function () {
                                obj[parameter] = $(this).val();
                                if (settings.helpers && obj[parameter] in settings.helpers) {
                                    $helpers.empty();
                                    $helpers.append(settings.helpers[obj[parameter]]);
                                    $helpers.find('.azr-tokens input').each(function () {
                                        $(this).attr('size', $(this).val().length);
                                    });
                                }
                            });
                            var $helpers = $('<div class="azr-helpers"></div>').appendTo($parameter);
                            if (settings.options.length) {
                                for (var value in settings.options) {
                                    $('<option value="' + value + '">' + settings.options[value] + '</option>').appendTo($select);
                                }
                            } else {
                                if (parameter in obj && obj[parameter]) {
                                    $('<option value="' + obj[parameter] + '">' + obj[parameter] + '</option>').appendTo($select);
                                } else {
                                    if(settings.default) {
                                        $('<option value="' + settings.default + '">' + settings.default + '</option>').appendTo($select);
                                    }                                    
                                }
                            }
                            $select.select2({
                                dropdownAutoWidth: 'true',
                                tags: 'true',
                                placeholder: {
                                    id: '-1',
                                    text: azr.i18n.select_an_option
                                },
                                allowClear: true,
                                createTag: function (params) {
                                    var term = $.trim(params.term);
                                    if (term === '') {
                                        return null;
                                    }
                                    return {
                                        id: term,
                                        text: term,
                                        value: true
                                    };
                                }
                            });
                            for (var master in settings.dependent_options) {
                                for (var value in settings.dependent_options[master]) {
                                    $('<option data-master="' + master + '" value="' + value + '">' + settings.dependent_options[master][value] + '</option>').appendTo($select);
                                }
                            }
                            if (parameter in obj) {
                                $select.val(obj[parameter]).trigger('change');
                            } else {
                                $select.val(settings.default).trigger('change');
                            }
                        } else {
                            if (settings.no_options) {
                                $('<div class="azr-warning">' + settings.no_options + '</div>').appendTo($parameter);
                            }
                        }
                        break;
                    case 'dropdown':
                        if (settings.options && Object.keys(settings.options).length || settings.dependent_options && Object.keys(settings.dependent_options).length) {
                            var $select = $('<select ' + (settings.required ? 'required' : '') + '></select>').appendTo($parameter).on('change', function () {
                                obj[parameter] = $(this).val();
                                if (settings.helpers && obj[parameter] in settings.helpers) {
                                    $helpers.empty();
                                    $helpers.append(settings.helpers[obj[parameter]]);
                                    $helpers.find('.azr-tokens input').each(function () {
                                        $(this).attr('size', $(this).val().length);
                                    });
                                }
                            });
                            var $helpers = $('<div class="azr-helpers"></div>').appendTo($parameter);
                            for (var value in settings.options) {
                                $('<option value="' + value + '">' + settings.options[value] + '</option>').appendTo($select);
                            }
                            $select.select2({dropdownAutoWidth: 'true'});
                            for (var master in settings.dependent_options) {
                                for (var value in settings.dependent_options[master]) {
                                    $('<option data-master="' + master + '" value="' + value + '">' + settings.dependent_options[master][value] + '</option>').appendTo($select);
                                }
                            }
                            if (parameter in obj) {
                                $select.val(obj[parameter]).trigger('change');
                            } else {
                                $select.val(settings.default).trigger('change');
                            }
                        } else {
                            if (settings.no_options) {
                                $('<div class="azr-warning">' + settings.no_options + '</div>').appendTo($parameter);
                            }
                        }
                        break;
                    case 'multiselect':
                        if (settings.options && Object.keys(settings.options).length) {
                            var $select = $('<select ' + (settings.required ? 'required' : '') + ' multiple></select>').appendTo($parameter).on('change', function () {
                                obj[parameter] = $(this).val();
                            });
                            for (var value in settings.options) {
                                $('<option value="' + value + '">' + settings.options[value] + '</option>').appendTo($select);
                            }
                            $select.select2({dropdownAutoWidth: 'true'});
                            if (parameter in obj) {
                                $select.val(obj[parameter]).trigger('change');
                            } else {
                                $select.val(settings.default).trigger('change');
                            }
                        } else {
                            if (settings.no_options) {
                                $('<div class="azr-warning">' + settings.no_options + '</div>').appendTo($parameter);
                            }
                        }
                        break;
                    case 'checkbox':
                        $('<label><input type="checkbox" ' + (obj[parameter] ? 'checked' : '') + '></label>').appendTo($parameter).on('change', function () {
                            obj[parameter] = $(this).find('input').prop('checked');
                        });
                        break;
                    case 'text':
                    case 'number':
                    case 'date':
                    case 'email':
                        var $input = $('<input type="' + settings.type + '" ' + (settings.required ? 'required' : '') + ' ' + (settings.step ? 'step="' + settings.step + '"' : '') + '/>').appendTo($parameter).on('change', function () {
                            obj[parameter] = $(this).val();
                        });
                        if (parameter in obj) {
                            $input.val(obj[parameter]).trigger('change');
                        } else {
                            $input.val(settings.default).trigger('change');
                        }
                        break;
                    case 'days_with_units':
                        var $input = $('<input type="number" ' + (settings.required ? 'required' : '') + ' />').appendTo($parameter).on('change', function () {
                            switch ($select.val()) {
                                case 'minutes':
                                    obj[parameter] = $(this).val() / 24 / 60;
                                    break;
                                case 'hours':
                                    obj[parameter] = $(this).val() / 24;
                                    break;
                                case 'days':
                                    obj[parameter] = $(this).val();
                                    break;
                                case 'weeks':
                                    obj[parameter] = $(this).val() * 7;
                                    break;
                            }
                        });
                        var $select = $('<select></select>').appendTo($parameter).on('change', function () {
                            $input.val('1').trigger('change');
                        });
                        $('<option value="minutes">' + azr.i18n.minutes + '</option>').appendTo($select);
                        $('<option value="hours">' + azr.i18n.hours + '</option>').appendTo($select);
                        $('<option value="days" selected>' + azr.i18n.days + '</option>').appendTo($select);
                        $('<option value="weeks">' + azr.i18n.weeks + '</option>').appendTo($select);
                        if (parameter in obj) {
                            $input.val(obj[parameter]).trigger('change');
                        } else {
                            $input.val(settings.default).trigger('change');
                        }
                        break;
                    case 'ajax_dropdown':
                        var $select = $('<select ' + (settings.required ? 'required' : '') + '></select>').appendTo($parameter).on('change', function () {
                            obj[parameter] = $(this).val();
                        });
                        $select.select2({
                            ajax: {
                                url: settings.url,
                                dataType: 'json'
                            },
                            dropdownAutoWidth: 'true'
                        });
                        if (parameter in obj) {
                            $.post(settings.url, {
                                'values': obj[parameter]
                            }, function (data) {
                                for (var value in data) {
                                    $('<option value="' + value + '">' + data[value] + '</option>').appendTo($select);
                                }
                                $select.val(obj[parameter]).trigger('change');
                            }, 'json');
                        } else if (settings.default) {
                            $.post(settings.url, {
                                'values': settings.default
                            }, function (data) {
                                for (var value in data) {
                                    $('<option value="' + value + '">' + data[value] + '</option>').appendTo($select);
                                }
                                $select.val(settings.default).trigger('change');
                            }, 'json');
                        }
                        break;
                    case 'ajax_multiselect':
                        var $select = $('<select ' + (settings.required ? 'required' : '') + ' multiple></select>').appendTo($parameter).on('change', function () {
                            obj[parameter] = $(this).val();
                        });
                        $select.select2({
                            ajax: {
                                url: settings.url,
                                dataType: 'json'
                            },
                            dropdownAutoWidth: 'true'
                        });
                        if (parameter in obj) {
                            $.post(settings.url, {
                                'values': obj[parameter]
                            }, function (data) {
                                for (var value in data) {
                                    $('<option value="' + value + '">' + data[value] + '</option>').appendTo($select);
                                }
                                $select.val(obj[parameter]).trigger('change');
                            }, 'json');
                        } else if (settings.default) {
                            $.post(settings.url, {
                                'values': settings.default
                            }, function (data) {
                                for (var value in data) {
                                    $('<option value="' + value + '">' + data[value] + '</option>').appendTo($select);
                                }
                                $select.val(settings.default).trigger('change');
                            }, 'json');
                        }
                        break;
                    case 'richtext':
                    case 'textarea':
                        var $textarea = $('<textarea ' + (settings.required ? 'required' : '') + '></textarea>').appendTo($parameter).on('change', function () {
                            obj[parameter] = btoa(unescape(encodeURIComponent($(this).val())));
                        });
                        if (parameter in obj) {
                            if (obj[parameter]) {
                                try {
                                    $textarea.val(decodeURIComponent(escape(atob(obj[parameter])))).trigger('change');
                                } catch (e) {

                                }
                            } else {
                                $textarea.val('').trigger('change');
                            }
                        } else {
                            $textarea.val(settings.default).trigger('change');
                        }
                        if (settings.type == 'richtext') {
                            rich_text_editor($textarea);
                        }
                        break;
                    case 'group':
                        function add_row(row) {
                            var $row = $('<tr></tr>').appendTo($group_list);
                            var index = 0;
                            for (var i = 0; i < obj[parameter].length; i++) {
                                if (obj[parameter][i] == row) {
                                    index = i;
                                }
                            }
                            for (var name in settings.fields) {
                                var $parameter = create_parameter(name, settings.fields[name], row).appendTo($('<td></td>').appendTo($row));
                                $parameter.on('change', function () {
                                    refresh_dependencies($row, settings.fields, obj[parameter][index]);
                                });
                                refresh_dependencies($row, settings.fields, obj[parameter][index]);
                            }
                            $('<a href="#" class="azr-remove"><span class="dashicons dashicons-no-alt"></span></a>').appendTo($('<td></td>').appendTo($row)).on('click', function () {
                                obj[parameter].splice(index, 1);
                                $row.remove();
                                $group_table.trigger('change');
                                return false;
                            });
                        }
                        var $group_table = $('<table></table>').appendTo($parameter);
                        var $group_list = $('<tbody></tbody>').appendTo($group_table);
                        $('<a href="#">' + settings.add_label + '</a>').appendTo($parameter).on('click', function () {
                            var new_row = {};
                            obj[parameter].push(new_row);
                            add_row(new_row);
                            return false;
                        });
                        if (parameter in obj) {
                            $(obj[parameter]).each(function () {
                                add_row(this);
                            });
                        } else {
                            obj[parameter] = [];
                        }
                        break;
                }
                return $parameter;
            }
            function refresh_dependencies($parameters, parameters, obj) {
                for (var parameter in parameters) {
                    var $parameter = $parameters.find('[data-parameter="' + parameter + '"]');
                    if (parameters[parameter].dependencies) {
                        for (var master_parameter in parameters[parameter].dependencies) {
                            if (parameters[parameter].dependencies[master_parameter].indexOf(obj[master_parameter]) >= 0) {
                                $parameter.show();
                                break;
                            } else {
                                $parameter.hide();
                            }
                        }
                    }
                    if (parameters[parameter].dependency && parameters[parameter].dependency in parameters) {
                        $parameter.find('[data-master]').attr('hidden', 'hidden').attr('disabled', 'disabled');
                        $parameter.find('[data-master="' + obj[parameters[parameter].dependency] + '"]').removeAttr('hidden').removeAttr('disabled');
                        $parameter.find('select[data-select2-id]').select2("destroy").select2({dropdownAutoWidth: 'true'});
                    }
                    if (parameters[parameter].event_dependency) {
                        if (parameters[parameter].event_dependency.indexOf($event_select.val()) >= 0) {
                            $parameter.show();
                        } else {
                            $parameter.hide();
                        }
                    }
                }
                refresh_rule();
            }
            function create_action(action, obj) {
                var $action = $('<div class="azr-action"></div>').data('obj', obj);
                var $action_type = $('<div class="azr-type"></div>').appendTo($action);
                var $action_parameters = $('<div class="azr-parameters"></div>').appendTo($action);
                $('<div class="azr-remove">' + azr.i18n.remove + '</div>').appendTo($action).on('click', function () {
                    var i = rule.actions.indexOf(obj);
                    delete rule.actions[i];
                    rule.actions = rule.actions.filter(function (obj) {
                        return obj ? true : false;
                    });
                    $action.remove();
                    refresh_rule();
                });
                var $action_select = $('<select><option value="" selected></option></select>').appendTo($action_type).on('change', function (event) {
                    $action_parameters.empty();
                    var action = $action_select.val();
                    if (action) {
                        obj.type = action;
                        if (obj.blocked) {
                            $action.addClass('azr-blocked');
                        } else {
                            $action.removeClass('azr-blocked');
                        }
                        $action_type.find('.azr-description').remove();
                        if (azr.settings.actions[action].description) {
                            $('<div class="azr-description">' + azr.settings.actions[action].description + '</div>').appendTo($action_type);
                        }
                        $action_type.find('.azr-helpers').remove();
                        if (azr.settings.actions[action].helpers) {
                            $('<div class="azr-helpers">' + azr.settings.actions[action].helpers + '</div>').appendTo($action_type).find('.azr-tokens input').each(function () {
                                $(this).attr('size', $(this).val().length);
                            });


                        }

                        var event_dependency = [];
                        if (azr.settings.actions[action].event_dependency) {
                            $(azr.settings.actions[action].event_dependency).each(function () {
                                if ($events_list.find('.azr-event > .azr-type > select').val() == this) {
                                    event_dependency = [];
                                    return false;
                                } else {
                                    if (azr.settings.events[this].group) {
                                        event_dependency.push(azr.settings.events[this].group + ' > ' + azr.settings.events[this].name);
                                    } else {
                                        event_dependency.push(azr.settings.events[this].name);
                                    }
                                }
                            });
                        }
                        var condition_dependency = [];
                        if (azr.settings.actions[action].condition_dependency) {
                            $(azr.settings.actions[action].condition_dependency).each(function () {
                                var conditions = [];
                                $conditions_list.find('.azr-condition > .azr-type > select').each(function () {
                                    conditions.push($(this).val());
                                });
                                if (conditions.indexOf(this) >= 0) {
                                    condition_dependency = [];
                                    return false;
                                } else {
                                    if (azr.settings.conditions[this].group) {
                                        condition_dependency.push(azr.settings.conditions[this].group + ' > ' + azr.settings.conditions[this].name);
                                    } else {
                                        condition_dependency.push(azr.settings.conditions[this].name);
                                    }
                                }
                            });
                        }


                        if (event_dependency.length || condition_dependency.length) {
                            if (event_dependency.length) {
                                $('<div class="azr-warning">' + azr.i18n.required_event + ' ' + event_dependency.join(', ') + '</div>').appendTo($action_parameters);
                            }
                            if (condition_dependency.length) {
                                $('<div class="azr-warning">' + azr.i18n.required_condition + ' ' + condition_dependency.join(', ') + '</div>').appendTo($action_parameters);
                            }
                        } else {
                            for (var parameter in azr.settings.actions[action].parameters) {
                                var settings = azr.settings.actions[action].parameters[parameter];
                                var $parameter = create_parameter(parameter, settings, obj);
                                $action_parameters.append($parameter);
                                $parameter.on('change', function () {
                                    refresh_dependencies($action_parameters, azr.settings.actions[action].parameters, obj);
                                });
                            }
                            refresh_dependencies($action_parameters, azr.settings.actions[action].parameters, obj);


                            if (azr.settings.actions[action].event_dependency) {
                                $(azr.settings.actions[action].event_dependency).each(function () {
                                    if ($events_list.find('.azr-event > .azr-type > select').val() == this) {
                                        event_dependency = [];
                                        return false;
                                    } else {
                                        if (azr.settings.events[this].group) {
                                            event_dependency.push(azr.settings.events[this].group + ' > ' + azr.settings.events[this].name);
                                        } else {
                                            event_dependency.push(azr.settings.events[this].name);
                                        }
                                    }
                                });
                            }
                            var event_dependency = [];
                            if (azr.settings.actions[action].event_dependency) {
                                $(azr.settings.actions[action].event_dependency).each(function () {
                                    if (azr.settings.events[this].group) {
                                        event_dependency.push(azr.settings.events[this].group + ' > ' + azr.settings.events[this].name);
                                    } else {
                                        event_dependency.push(azr.settings.events[this].name);
                                    }
                                });
                            }
                            var condition_dependency = [];
                            if (azr.settings.actions[action].condition_dependency) {
                                $(azr.settings.actions[action].condition_dependency).each(function () {
                                    if (azr.settings.conditions[this].group) {
                                        condition_dependency.push(azr.settings.conditions[this].group + ' > ' + azr.settings.conditions[this].name);
                                    } else {
                                        condition_dependency.push(azr.settings.conditions[this].name);
                                    }
                                });
                            }
                            if (event_dependency.length) {
                                $('<div class="azr-description">' + azr.i18n.required_event + ' ' + event_dependency.join(', ') + '</div>').appendTo($action_parameters);
                            }
                            if (condition_dependency.length) {
                                $('<div class="azr-description">' + azr.i18n.required_condition + ' ' + condition_dependency.join(', ') + '</div>').appendTo($action_parameters);
                            }
                        }

                        if ($events && $events.is(':hidden')) {
                            $events.show();
                            if (azr.settings.actions[action].event_dependency) {
                                $events_list.find('.azr-event > .azr-type > select').val(azr.settings.actions[action].event_dependency[0]).trigger('change');
                            }
                        }
                        if ($conditions && $conditions.is(':hidden')) {
                            $conditions.show();
                            if (azr.settings.actions[action].condition_dependency) {
                                if (!$conditions_list.children().length) {
                                    $conditions.find('.azr-add-condition').trigger('click');
                                    $conditions_list.find('.azr-condition > .azr-type > select').val(azr.settings.actions[action].condition_dependency[0]).trigger('change');
                                }
                            }
                        }
                    }
                });

                var groups = {};
                var groups_items = {};
                for (var a in azr.settings.actions) {
                    if (azr.settings.actions[a].group) {
                        if (!groups[azr.settings.actions[a].group]) {
                            groups[azr.settings.actions[a].group] = [];
                        }
                        groups[azr.settings.actions[a].group].push(a);
                        groups_items[a] = azr.settings.actions[a].group;
                    }
                }
                for (var a in azr.settings.actions) {
                    if (!groups_items[a]) {
                        if ($actions_list.find('.azr-action.azr-blocked > .azr-type > select').val() == a) {
                            continue;
                        }
                        $('<option value="' + a + '">' + azr.settings.actions[a].name + '</option>').appendTo($action_select);
                    }
                }
                for (var group in groups) {
                    var $group = $('<optgroup label="' + group + '"></optgroup>').appendTo($action_select);
                    $(groups[group]).each(function () {
                        if ($actions_list.find('.azr-action.azr-blocked > .azr-type > select').val() == a) {
                            return;
                        }
                        $('<option value="' + this + '">' + azr.settings.actions[this].name + '</option>').appendTo($group);
                    });
                }

                grouped_select2($action_select);
                if (action) {
                    $action_select.val(action).trigger('change');
                } else {
                    $action_select.val('').trigger('change');
                }
                if ($action_select.children().length) {
                    return $action;
                }
                return false;
            }
            function create_condition(condition, obj) {
                if (['or', 'and'].indexOf(condition) >= 0) {
                    var $group = create_group(condition);
                    var $list = $group.children('.azr-list');
                    $(obj.conditions).each(function () {
                        var $condition = create_condition(this.type, this);
                        $list.append($condition);
                    });
                    return $group;
                } else {
                    var $condition = $('<div class="azr-condition"></div>').data('obj', obj);
                    $condition.data('obj', obj);
                    var $condition_type = $('<div class="azr-type"></div>').appendTo($condition);
                    var $condition_parameters = $('<div class="azr-parameters"></div>').appendTo($condition);
                    $('<div class="azr-remove">' + azr.i18n.remove + '</div>').appendTo($condition).on('click', function () {
                        var i = rule.conditions.indexOf(obj);
                        delete rule.conditions[i];
                        rule.conditions = rule.conditions.filter(function (obj) {
                            return obj ? true : false;
                        });
                        $condition.remove();
                        refresh_rule();
                    });
                    var $negate = $('<div class="azr-negate"><span>' + azr.i18n.negate + '</span></div>').appendTo($condition_type);
                    $('<input type="checkbox" ' + (obj.negate ? 'checked' : '') + '>').appendTo($negate).on('change', function () {
                        if ($(this).prop('checked')) {
                            obj.negate = true;
                        } else {
                            obj.negate = false;
                        }
                        refresh_rule();
                    });
                    var $condition_select = $('<select><option value="" selected></option></select>').appendTo($condition_type).on('change', function (event) {
                        $condition_parameters.empty();
                        var condition = $condition_select.val();
                        if (condition) {
                            obj.type = condition;
                            if (obj.blocked) {
                                $condition.addClass('azr-blocked');
                            } else {
                                $condition.removeClass('azr-blocked');
                            }
                            $condition_type.find('.azr-description').remove();
                            if (azr.settings.conditions[condition].description) {
                                $('<div class="azr-description">' + azr.settings.conditions[condition].description + '</div>').appendTo($condition_type);
                            }
                            $condition_type.find('.azr-helpers').remove();
                            if (azr.settings.conditions[condition].helpers) {
                                $('<div class="azr-helpers">' + azr.settings.conditions[condition].helpers + '</div>').appendTo($condition_type).find('.azr-tokens input').each(function () {
                                    $(this).attr('size', $(this).val().length);
                                });
                            }

                            var event_dependency = [];
                            if (azr.settings.conditions[condition].event_dependency && $events_list) {
                                $(azr.settings.conditions[condition].event_dependency).each(function () {
                                    if ($events_list.find('.azr-event > .azr-type > select').val() == this) {
                                        event_dependency = [];
                                        return false;
                                    } else {
                                        if (azr.settings.events[this].group) {
                                            event_dependency.push(azr.settings.events[this].group + ' > ' + azr.settings.events[this].name);
                                        } else {
                                            event_dependency.push(azr.settings.events[this].name);
                                        }
                                    }
                                });
                            }

                            if (event_dependency.length) {
                                $('<div class="azr-warning">' + azr.i18n.required_event + ' ' + event_dependency.join(', ') + '</div>').appendTo($condition_parameters);
                            } else {
                                for (var parameter in azr.settings.conditions[condition].parameters) {
                                    var settings = azr.settings.conditions[condition].parameters[parameter];
                                    var $parameter = create_parameter(parameter, settings, obj);
                                    $condition_parameters.append($parameter);
                                    $parameter.on('change', function () {
                                        refresh_dependencies($condition_parameters, azr.settings.conditions[condition].parameters, obj);
                                    });
                                }
                                refresh_dependencies($condition_parameters, azr.settings.conditions[condition].parameters, obj);
                                if ($actions) {
                                    $actions.find('.azr-action > .azr-type > select').trigger('change');
                                }
                                var event_dependency = [];
                                if (azr.settings.conditions[condition].event_dependency && $events_list) {
                                    $(azr.settings.conditions[condition].event_dependency).each(function () {
                                        if (azr.settings.events[this].group) {
                                            event_dependency.push(azr.settings.events[this].group + ' > ' + azr.settings.events[this].name);
                                        } else {
                                            event_dependency.push(azr.settings.events[this].name);
                                        }
                                    });
                                }
                                if (event_dependency.length) {
                                    $('<div class="azr-description">' + azr.i18n.required_event + ' ' + event_dependency.join(', ') + '</div>').appendTo($condition_parameters);
                                }
                            }
                        }
                    });


                    var groups = {};
                    var groups_items = {};
                    for (var c in azr.settings.conditions) {
                        if ($events_list || !azr.settings.conditions[c].event_dependency) {
                            if (!context || azr.settings.conditions[c].required_context && azr.settings.conditions[c].required_context.indexOf(context) >= 0) {
                                if (azr.settings.conditions[c].group) {
                                    if (!groups[azr.settings.conditions[c].group]) {
                                        groups[azr.settings.conditions[c].group] = [];
                                    }
                                    groups[azr.settings.conditions[c].group].push(c);
                                    groups_items[c] = azr.settings.conditions[c].group;
                                }
                            }
                        }
                    }
                    for (var c in azr.settings.conditions) {
                        if (!groups_items[c]) {
                            if ($conditions_list.find('.azr-condition.azr-blocked > .azr-type > select').val() == c) {
                                continue;
                            }
                            if ($events_list || !azr.settings.conditions[c].event_dependency) {
                                if (!context || azr.settings.conditions[c].required_context && azr.settings.conditions[c].required_context.indexOf(context) >= 0) {
                                    $('<option value="' + c + '">' + azr.settings.conditions[c].name + '</option>').appendTo($condition_select);
                                }
                            }
                        }
                    }
                    for (var group in groups) {
                        var $group = $('<optgroup label="' + group + '"></optgroup>').appendTo($condition_select);
                        $(groups[group]).each(function () {
                            if ($conditions_list.find('.azr-condition.azr-blocked > .azr-type > select').val() == this) {
                                return;
                            }
                            if ($events_list || !azr.settings.conditions[this].event_dependency) {
                                if (!context || azr.settings.conditions[this].required_context && azr.settings.conditions[this].required_context.indexOf(context) >= 0) {
                                    $('<option value="' + this + '">' + azr.settings.conditions[this].name + '</option>').appendTo($group);
                                }
                            }
                        });
                    }
                    grouped_select2($condition_select);
                    if (condition) {
                        $condition_select.val(condition).trigger('change');
                    } else {
                        $condition_select.val('').trigger('change');
                    }
                    return $condition;
                }
            }
            function create_group(type) {
                var $group = $('<div class="azr-' + type + '"></div>');
                $('<div class="azr-title">' + azr.i18n[type] + '</div>').appendTo($group);
                $('<div class="azr-remove">' + azr.i18n.remove + '</div>').appendTo($group).on('click', function () {
                    $group.remove();
                    refresh_rule();
                });
                var $group_list = $('<div class="azr-list"></div>').appendTo($group);
                $group_list.sortable({
                    items: "> :not(.azr-blocked)",
                    update: function (event, ui) {
                        refresh_rule();
                    },
                    connectWith: ".azr-list"
                });
                return $group;
            }
            function makeid() {
                var text = "";
                var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
                for (var i = 0; i < 5; i++)
                    text += possible.charAt(Math.floor(Math.random() * possible.length));
                return text;
            }
            function rich_text_editor($textarea) {
                function init_textarea_html($element) {
                    var $wp_link = $("#wp-link");
                    $wp_link.parent().hasClass("wp-dialog") && $wp_link.wpdialog("destroy");
                    $element.val($textarea.val());
                    try {
                        window._.isUndefined(window.tinyMCEPreInit.qtInit[textfield_id]) && (window.tinyMCEPreInit.qtInit[textfield_id] = window._.extend({}, window.tinyMCEPreInit.qtInit[window.wpActiveEditor], {
                            auto_focus: false,
                            id: textfield_id
                        }));
                        window.tinyMCEPreInit && window.tinyMCEPreInit.mceInit[window.wpActiveEditor] && (window.tinyMCEPreInit.mceInit[textfield_id] = window._.extend({}, window.tinyMCEPreInit.mceInit[window.wpActiveEditor], {
                            resize: "vertical",
                            height: 200,
                            auto_focus: false,
                            id: textfield_id,
                            setup: function (ed) {
                                "undefined" != typeof ed.on ? ed.on("init", function (ed) {
//                                    ed.target.focus();
                                    window.wpActiveEditor = textfield_id;
                                }) : ed.onInit.add(function (ed) {
//                                    ed.focus();
                                    window.wpActiveEditor = textfield_id;
                                });
                                ed.on('change', function (e) {
                                    $textarea.val(ed.getContent());
                                    $textarea.trigger('change');
                                });
                            }
                        }), window.tinyMCEPreInit.mceInit[textfield_id].plugins = window.tinyMCEPreInit.mceInit[textfield_id].plugins.replace(/,?wpfullscreen/, ""), window.tinyMCEPreInit.mceInit[textfield_id].wp_autoresize_on = !1);
                        window.quicktags(window.tinyMCEPreInit.qtInit[textfield_id]);
                        window.QTags._buttonsInit();
                        window.tinymce && (window.switchEditors && window.switchEditors.go(textfield_id, "tmce"), "4" === window.tinymce.majorVersion && window.tinymce.execCommand("mceAddEditor", !0, textfield_id));
                        window.wpActiveEditor = textfield_id;
                        window.setUserSetting('editor', 'html');
                    } catch (e) {
                    }
                }
                var textfield_id = makeid();
                $.ajax({
                    type: 'POST',
                    url: window.ajaxurl,
                    data: {
                        action: 'azm_get_wp_editor',
                        id: textfield_id
                    },
                    cache: false
                }).done(function (data) {
                    $textarea.hide();
                    $textarea.after(data);
                    init_textarea_html($('#' + textfield_id));
                    $('#' + textfield_id).on('change', function () {
                        $textarea.val($(this).val());
                        $textarea.trigger('change');
                    });
                });
            }
            function get_nested_conditions($list) {
                var conditions = [];
                $list.children().each(function () {
                    var $this = $(this);
                    if ($this.is('.azr-or')) {
                        conditions.push({
                            type: 'or',
                            conditions: get_nested_conditions($this.children('.azr-list'))
                        });
                    } else if ($this.is('.azr-and')) {
                        conditions.push({
                            type: 'and',
                            conditions: get_nested_conditions($this.children('.azr-list'))
                        });
                    } else {
                        conditions.push($this.data('obj'));
                    }
                });
                return conditions;
            }
            function refresh_rule() {
                var valid = true;
                $rule.removeClass('azr-valid');
                $rule.find('[required]:visible').each(function () {
                    if ('reportValidity' in this) {
                        valid = this.reportValidity();
                    } else {
                        var $this = $(this);
                        $this.off('change.az-report-validity').on('change.az-report-validity', function () {
                            $(this).removeClass('azr-not-valid');
                        });
                        $this.removeClass('azr-not-valid');
                        if (!this.checkValidity()) {
                            valid = false;
                            $this.addClass('azr-not-valid');
                        }
                    }
                });
                if (valid) {
                    $rule.addClass('azr-valid');
                    if ($conditions_list) {
                        var rule_copy = $.extend({}, rule);
                        rule_copy.conditions = get_nested_conditions($conditions_list);
                        $textarea.val(JSON.stringify(rule_copy));
                    }
                }
                setTimeout(function () {
                    $window.trigger('azr-refresh');
                });
            }
            var $textarea = $(this);
            var context = $textarea.data('context');
            var rule = $textarea.val();
            if (rule) {
                rule = JSON.parse(rule);
            } else {
                rule = {};
            }
            var $rule = $('<form class="azr-rule"></form>').insertAfter($textarea);
            if (rule.event) {
                var $events = $('<div class="azr-events"></div>').appendTo($rule).hide();
                $('<div class="azr-title">' + azr.i18n.event + '</div>').appendTo($events);
                var $events_list = $('<div class="azr-list"></div>').appendTo($events);
                var $event = $('<div class="azr-event"></div>').appendTo($events_list).data('obj', rule.event);
                var $event_type = $('<div class="azr-type"></div>').appendTo($event);
                var $event_parameters = $('<div class="azr-parameters"></div>').appendTo($event);
                var $event_select = $('<select><option value="" selected></option></select>').appendTo($event_type).on('change', function (event) {
                    $event_parameters.empty();
                    var event = $event_select.val();
                    if (event) {
                        rule.event.type = event;
                        $event_type.find('.azr-description').remove();
                        if (azr.settings.events[event].description) {
                            $('<div class="azr-description">' + azr.settings.events[event].description + '</div>').appendTo($event_type);
                        }
                        $event_type.find('.azr-helpers').remove();
                        if (azr.settings.events[event].helpers) {
                            $('<div class="azr-helpers">' + azr.settings.events[event].helpers + '</div>').appendTo($event_type).find('.azr-tokens input').each(function () {
                                $(this).attr('size', $(this).val().length);
                            });
                        }
                        for (var parameter in azr.settings.events[event].parameters) {
                            var settings = azr.settings.events[event].parameters[parameter];
                            var $parameter = create_parameter(parameter, settings, rule.event);
                            $event_parameters.append($parameter);
                            $parameter.on('change', function () {
                                refresh_dependencies($event_parameters, azr.settings.events[event].parameters, rule.event);
                            });
                        }
                        refresh_dependencies($event_parameters, azr.settings.events[event].parameters, rule.event);
                        if ($conditions) {
                            $conditions.find('.azr-condition > .azr-type > select').trigger('change');
                        }
                        if ($actions) {
                            $actions.find('.azr-action > .azr-type > select').trigger('change');
                        }
                    }
                });
                var groups = {};
                var groups_items = {};
                for (var event in azr.settings.events) {
                    if (azr.settings.events[event].group) {
                        if (!groups[azr.settings.events[event].group]) {
                            groups[azr.settings.events[event].group] = [];
                        }
                        groups[azr.settings.events[event].group].push(event);
                        groups_items[event] = azr.settings.events[event].group;
                    }
                }
                for (var event in azr.settings.events) {
                    if (!groups_items[event]) {
                        $('<option value="' + event + '">' + azr.settings.events[event].name + '</option>').appendTo($event_select);
                    }
                }
                for (var group in groups) {
                    var $group = $('<optgroup label="' + group + '"></optgroup>').appendTo($event_select);
                    $(groups[group]).each(function () {
                        $('<option value="' + this + '">' + azr.settings.events[this].name + '</option>').appendTo($group);
                    });
                }
                grouped_select2($event_select);
                if (rule.event.type) {
                    $event_select.val(rule.event.type).trigger('change');
                } else {
                    $event_select.val('').trigger('change');
                }
            }


            if (rule.conditions) {
                var $conditions = $('<div class="azr-conditions"></div>').appendTo($rule).hide();
                $('<div class="azr-title">' + azr.i18n.conditions + '</div>').appendTo($conditions);
                var $conditions_list = $('<div class="azr-list"></div>').appendTo($conditions);
                $(rule.conditions).each(function () {
                    var $condition = create_condition(this.type, this);
                    $conditions_list.append($condition);
                });
                $('<div class="azr-add azr-add-condition">' + azr.i18n.add_condition + '</div>').appendTo($conditions).on('click', function () {
                    var type = false;
                    var condition = {
                        type: type
                    };
                    rule.conditions.push(condition);
                    var $condition = create_condition(type, condition);
                    $conditions_list.append($condition);
                    refresh_rule();
                    $conditions_list.sortable({
                        items: "> :not(.azr-blocked)",
                        update: function (event, ui) {
                            refresh_rule();
                        },
                        connectWith: ".azr-list"
                    });
                });
                $('<div class="azr-add">' + azr.i18n.add_or + '</div>').appendTo($conditions).on('click', function () {
                    var $group = create_group('or');
                    $conditions_list.append($group);
                    refresh_rule();
                    $conditions_list.sortable({
                        items: "> :not(.azr-blocked)",
                        update: function (event, ui) {
                            refresh_rule();
                        },
                        connectWith: ".azr-list"
                    });
                });
                $('<div class="azr-add">' + azr.i18n.add_and + '</div>').appendTo($conditions).on('click', function () {
                    var $group = create_group('and');
                    $conditions_list.append($group);
                    refresh_rule();
                    $conditions_list.sortable({
                        items: "> :not(.azr-blocked)",
                        update: function (event, ui) {
                            refresh_rule();
                        },
                        connectWith: ".azr-list"
                    });
                });
            }

            if (rule.actions) {
                var $actions = $('<div class="azr-actions"></div>').appendTo($rule);
                $('<div class="azr-title">' + azr.i18n.actions + '</div>').appendTo($actions);
                var $actions_list = $('<div class="azr-list"></div>').appendTo($actions);
                $(rule.actions).each(function () {
                    var $action = create_action(this.type, this);
                    if ($action) {
                        $actions_list.append($action);
                    }
                });
                if ($actions_list.children().length) {
                    if ($events && $events.is(':hidden')) {
                        $events.show();
                    }
                    if ($conditions && $conditions.is(':hidden')) {
                        $conditions.show();
                    }
                }
                $('<div class="azr-add">' + azr.i18n.add_action + '</div>').appendTo($actions).on('click', function () {
                    var type = false;
                    var action = {
                        type: type
                    };
                    rule.actions.push(action);
                    var $action = create_action(type, action);
                    if ($action) {
                        $actions_list.append($action);
                    }
                });
            } else {
                if ($events && $events.is(':hidden')) {
                    $events.show();
                }
                if ($conditions && $conditions.is(':hidden')) {
                    $conditions.show();
                }
            }
        });
    });
})(window.jQuery);