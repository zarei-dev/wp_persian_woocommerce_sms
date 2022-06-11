(function () {
    jQuery(document).ready(function (a) {
        "use strict";
        var b;
        return b = a("form[name='checkout']"), a("form[name='checkout']").attr("id", "checkout").addClass("dropzone"), b.find(":input.select").each(function () {
            var b, c, d;
            return b = a(this).closest(".form-row").hasClass("validate-required"), "function" == typeof(c = a(this)).select2 && c.select2({
                allowClear: !b,
                placeholder: a(this).attr("multiple") ? pwoosms.chosen_placeholder_multi : pwoosms.chosen_placeholder_single
            }), a().select2 ? void 0 : "function" == typeof(d = a(this)).chosen ? d.chosen({
                allow_single_deselect: !b,
                placeholder_text_single: pwoosms.chosen_placeholder_single,
                placeholder_text_multiple: pwoosms.chosen_placeholder_multi,
                no_results_text: pwoosms.chosen_no_results_text
            }) : void 0
        })
    })
}).call(this);