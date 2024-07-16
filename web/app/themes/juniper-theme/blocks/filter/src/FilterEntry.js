import React, {useState} from "react";

export class FilterEntry {
    filterType = ''
    filterChoice = ''
    label = ''
    name = ''
    onChange = ((selected) => {})
    options = []
    url = ''
    optionAvailable = (() => true)

    static makeFromRaw(type, requestObj) {
        const instance = new FilterEntry();

        instance.filterType = FilterTypes.toFilterType(type)

        // noinspection JSUnresolvedReference
        instance.filterChoice = requestObj.filter_choices
        instance.label = requestObj.label
        instance.name = requestObj.name
        instance.onChange = requestObj.onChange

        // append filterChoice to FilterOption
        requestObj.tax_options = requestObj.tax_options.map((option) => {
            option.filter_choice = instance.filterChoice
            return option
        })

        // noinspection JSUnresolvedReference
        instance.options = requestObj.tax_options.map(FilterOption.makeFromRaw)

        instance.url = requestObj.url

        return instance
    }
}

export class FilterOption {
    key = ''
    count = 0
    description = ''
    filter = ''
    name = ''
    order = ''
    parent = 0
    slug = ''
    taxonomy = ''
    termGroup = 0
    termId = 0
    termOrder = ''
    termTaxonomyId = 0
    backgroundColor = ''
    renderOptionText = true
    filterChoice = ''

    static makeFromRaw(requestObj) {
        const instance = new FilterOption()

        instance.key = requestObj.key
        instance.count = requestObj.count
        instance.description = requestObj.description
        instance.filter = requestObj.filter
        instance.name = requestObj.name
        instance.order = requestObj.order
        instance.parent = requestObj.parent
        instance.slug = requestObj.slug
        instance.taxonomy = requestObj.taxonomy
        // noinspection JSUnresolvedReference
        instance.termGroup = requestObj.term_group
        instance.termId = requestObj.term_id
        // noinspection JSUnresolvedReference
        instance.termOrder = requestObj.term_order
        instance.termTaxonomyId = requestObj.term_taxonomy_id

        // noinspection JSUnresolvedReference
        instance.backgroundColor = requestObj.background_color
        // noinspection JSUnresolvedReference
        instance.renderOptionText = requestObj.render_option_text
        // noinspection JSUnresolvedReference
        instance.filterChoice = requestObj.filter_choice

        return instance
    }
}

export const FilterTypes = {
    CHECKBOX: 'checkbox',
    DROPDOWN: 'dropdown',
    TEXT: 'text',
    toFilterType: (string) => FilterTypes.validFilterType(string) ? FilterTypes[string] : null,
    validFilterType: (string) => !!FilterTypes.getFilterTypeIndex(string),
    getFilterTypeIndex: (string) => !!Object.values(FilterTypes).indexOf(string),
}
