jQuery(function ($) {
    if ($('[name="regp_custom_field_json"]').size() > 0) {
        var json = JSON.parse($('[name="regp_custom_field_json"]').val());

        $.each(json, function (i, field) {
            addField(field.type, field.id, field.labelValue, true);
        });


        $('.ws-custom-field-container').sortable({
            stop: function () {
                var json = [];

                $('.ws-custom-field-container li').each(function (i, el) {
                    var id = $(el).attr('data-id');
                    json.push({
                        id: id,
                        type: $(el).attr('data-type'),
                        labelValue: $('input[data-id="' + id + '"]').val() || ""
                    });
                });
                $('[name="regp_custom_field_json"]').val(JSON.stringify(json));
            }
        });


        $('.ws-custom-field.button').click(function () {
            addField($(this).attr('data-type'));
        });
        $(document).on('keyup', '.ws-custom-input-field-label', function () {

            var json = JSON.parse($('[name="regp_custom_field_json"]').attr('value')), thisEl = $(this);

            $.each(json, function (i, field) {
                
                if (field.id == $(thisEl).attr('data-id')){
                    field.labelValue = $(thisEl).val();
                }
            });
            console.log('end keyup : '+JSON.stringify(json));
            $('[name="regp_custom_field_json"]').val(JSON.stringify(json));
            $('.ws-custom-input-field-preview-input[data-id="' + $(thisEl).attr('data-id') + '"]').val($(thisEl).val());
        });

        function addField(type, id, value, isOld) {
            isOld = (typeof isOld == 'undefined' ? false : isOld);
            value = value ? value : '';
            
            var id = id || makeId();
            var pHolder = (isOld ? value : "Preview " + type + " box");            
            var previewInput = '<input class="ws-custom-input-field-preview-input" type="' + type + '" data-id="' + id + '" placeholder="'+pHolder+'" disabled checked>';
            var label = '<input class="ws-custom-input-field-label" data-id="' + id + '" value="' + value + '" placeholder="Field label">';
            var field = type === 'checkbox' ? '<label class="radio">' + previewInput + '</label>' + label : '<div class="form-field">' + label + previewInput;
            var fieldSet = '<li class="ui-state-default" data-id="' + id + '" data-type="' + type + '">' +
                    field +
                    '<i class="fa fa-reorder"></i> <i class="fa fa-close remove-custom-regfield" data-id="' + id + '" ></i></div>' +
                    '<div style="clear: both;"></div>' +
                    '</li>';
            $('.ws-custom-field-container').append(fieldSet);
            
            console.log("is old "+isOld+" val : "+value+" type : "+type);
            if (!isOld) {
                json.push({
                    id: id,
                    type: type,
                    labelValue: value
                });
                $('[name="regp_custom_field_json"]').val(JSON.stringify(json));
            }
            $('.ws-custom-input-field-label').trigger('keyup');
        }

        function makeId() {
            var text = "";
            var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
            for (var i = 0; i < 5; i++)
                text += possible.charAt(Math.floor(Math.random() * possible.length));
            return text;
        }
    }
});

jQuery(document).on('click', '.remove-custom-regfield', function (e) {
    e.preventDefault();
    if (confirm('Are you sure you want to delete this field?')) {
        var formId = jQuery(this).attr('data-id');
        jQuery('.ui-state-default[data-id="' + formId + '"]').fadeOut();
        var json = JSON.parse(jQuery('[name="regp_custom_field_json"]').val());

        jQuery(json).each(function (count) {
            if (json[count].id == formId) {
                json.splice(count, 1);
                jQuery('[name="regp_custom_field_json"]').val(JSON.stringify(json));
                return false;
            }
        });
    }
});