(function (blocks, element, editor, components, i18n) {
    'use strict';

    const { registerBlockType } = blocks;
    const { createElement: el } = element;
    const { InspectorControls } = editor;
    const { PanelBody, SelectControl } = components;
    const { __ } = i18n;

    registerBlockType('msaas-faq/faq-block', {
        title: __('⚡ Modern FAQ', 'modern-saas-faq'),
        icon: 'editor-help',
        category: 'widgets',
        attributes: {
            faqId: {
                type: 'number',
                default: 0
            }
        },

        edit: function (props) {
            const { attributes, setAttributes } = props;
            const { faqId } = attributes;

            const faqOptions = [
                { value: 0, label: __('انتخاب کنید...', 'modern-saas-faq') }
            ];

            if (window.msaasFaqData && window.msaasFaqData.faqSets) {
                window.msaasFaqData.faqSets.forEach(function (set) {
                    faqOptions.push({ value: set.value, label: set.label });
                });
            }

            return el(
                'div',
                { className: 'msaas-faq-block-wrapper' },
                el(
                    InspectorControls,
                    {},
                    el(
                        PanelBody,
                        { title: __('تنظیمات FAQ', 'modern-saas-faq'), initialOpen: true },
                        el(SelectControl, {
                            label: __('انتخاب مجموعه FAQ', 'modern-saas-faq'),
                            value: faqId,
                            options: faqOptions,
                            onChange: function (value) {
                                setAttributes({ faqId: parseInt(value) });
                            }
                        })
                    )
                ),
                el(
                    'div',
                    {
                        className: 'msaas-faq-block-preview',
                        style: {
                            padding: '40px 20px',
                            background: faqId > 0 ? '#e7f3ff' : '#fff3cd',
                            border: faqId > 0 ? '2px solid #0073aa' : '2px dashed #ffc107',
                            borderRadius: '8px',
                            textAlign: 'center'
                        }
                    },
                    el('div', { style: { fontSize: '18px', fontWeight: 'bold', marginBottom: '8px' } },
                        '⚡ Modern FAQ'
                    ),
                    faqId > 0
                        ? el('div', { style: { fontSize: '14px', color: '#555' } },
                            __('مجموعه انتخاب شده: ', 'modern-saas-faq') + 'ID ' + faqId
                        )
                        : el('div', { style: { fontSize: '14px', color: '#856404' } },
                            __('⚠️ لطفاً یک مجموعه FAQ انتخاب کنید', 'modern-saas-faq')
                        )
                )
            );
        },

        save: function () {
            return null; // رندر سمت سرور
        }
    });

})(
    window.wp.blocks,
    window.wp.element,
    window.wp.blockEditor,
    window.wp.components,
    window.wp.i18n
);
