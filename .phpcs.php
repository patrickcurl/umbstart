<?php


return PhpCsFixer\Config::Create()
    ->setUsingCache(false)
    ->setRules([
        '@PSR2'                                    => true,
        'array_indentation'                        => true,
        'array_syntax'                             => ['syntax' => 'short'],
        'binary_operator_spaces'                   => [
            // 'align_double_arrow' => true,
            // 'align_equals' => true,
            'default'   => 'align',
            'operators' => [
                '=>' => 'align',
                '='  => 'single_space'
            ]
        ],
        'blank_line_after_opening_tag'           => true,
        'blank_line_before_statement'            => [
            'statements' => [
                'break',
                'continue',
                'declare',
                'for',
                'foreach',
                'if',
                'return',
                'switch',
                'throw',
                'try',
                'while',
            ],
        ],
        'cast_spaces'                            => true,
        'class_attributes_separation'            => true,
        'combine_consecutive_unsets'             => true,
        'concat_space'                           => ['spacing' => 'one'],
        'linebreak_after_opening_tag'            => true,
        'normalize_index_brace'                  => true,
        'lowercase_static_reference'             => true,
        'no_blank_lines_after_class_opening'     => true,
        'no_blank_lines_after_phpdoc'            => true,
        'no_empty_phpdoc'                        => true,
        'no_empty_statement'                     => true,
        'no_extra_consecutive_blank_lines'       => true,
        'no_trailing_comma_in_singleline_array'  => true,
        'no_spaces_around_offset'                => true,
        'no_unused_imports'                      => true,
        'no_useless_return'                      => true,
        'no_whitespace_before_comma_in_array'    => true,
        'no_whitespace_in_blank_line'            => true,
        'ordered_class_elements'                 => [
            'order' => [
                'use_trait',
                'constant',
                'property',
            ]
        ],
        'ordered_imports'                        => ['sortAlgorithm' => 'alpha'],
        'phpdoc_order'                           => true,
        // 'psr4'                                   => true,
        'single_import_per_statement'            => false,
        'single_quote'                           => true,
        'unary_operator_spaces'                  => true,
        'switch_case_semicolon_to_colon'         => true,
        'ternary_operator_spaces'                => true,
        'ternary_to_null_coalescing'             => true,
        'trailing_comma_in_multiline_array'      => false,
        'trim_array_spaces'                      => true,
        'whitespace_after_comma_in_array'        => true,
    ]);
