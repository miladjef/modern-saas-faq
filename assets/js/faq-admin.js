jQuery(document).ready(function ($) {
    'use strict';

    /* ─── Color Picker ─── */
    if ($.fn.wpColorPicker) {
        $('.msaas-color-picker').wpColorPicker();
    }

    /* ─── Sortable (Drag & Drop) ─── */
    if ($.fn.sortable) {
        $('#msaas-faq-items-wrapper').sortable({
            handle: '.msaas-faq-drag-handle',
            placeholder: 'msaas-faq-placeholder',
            update: function () {
                updateItemNumbers();
            }
        });
    }

    /* ─── افزودن آیتم جدید ─── */
    var itemIndex = $('#msaas-faq-items-wrapper .msaas-faq-item-row').length;

    $('#msaas-add-faq-item').on('click', function () {
        var template = $('#tmpl-msaas-faq-item').html();
        if (!template) {
            console.warn('MSAAS FAQ: Item template not found.');
            return;
        }
        var html = template.replace(/\{\{INDEX\}\}/g, itemIndex);
        $('#msaas-faq-items-wrapper').append(html);
        itemIndex++;
        updateItemNumbers();

        // اسکرول به آیتم جدید
        $('html, body').animate({
            scrollTop: $('#msaas-faq-items-wrapper .msaas-faq-item-row:last').offset().top - 50
        }, 400);

        // فعال‌سازی ColorPicker برای آیتم جدید
        if ($.fn.wpColorPicker) {
            $('#msaas-faq-items-wrapper .msaas-faq-item-row:last .msaas-color-picker').wpColorPicker();
        }
    });

    /* ─── حذف آیتم ─── */
    $(document).on('click', '.msaas-faq-remove-item', function () {
        if (confirm('آیا مطمئن هستید؟')) {
            $(this).closest('.msaas-faq-item-row').fadeOut(300, function () {
                $(this).remove();
                updateItemNumbers();
            });
        }
    });

    /* ─── باز / بسته کردن آیتم ─── */
    $(document).on('click', '.msaas-faq-item-header', function () {
        $(this).closest('.msaas-faq-item-row')
               .find('.msaas-faq-item-body')
               .slideToggle(200);
        $(this).find('.msaas-faq-toggle-icon').toggleClass('open');
    });

    /* ─── به‌روزرسانی شماره و ترتیب آیتم‌ها ─── */
    function updateItemNumbers() {
        $('#msaas-faq-items-wrapper .msaas-faq-item-row').each(function (index) {
            $(this).find('.msaas-faq-item-number').text(index + 1);
            $(this).find('input, textarea, select').each(function () {
                var name = $(this).attr('name');
                if (name) {
                    $(this).attr('name', name.replace(/\[\d+\]/, '[' + index + ']'));
                }
            });
            $(this).find('.msaas-faq-order').val(index);
        });
    }

});
