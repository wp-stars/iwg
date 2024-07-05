import translationObject from "../../../TranslationObject";
import colorCodes from "../../../ColorCodes";
import {getUrlParamValue, hideOptionName} from "../../../utils";
import convert from "color-convert";


export default function prepareDropdownOptions(rawOptions, label) {
    const cateogoryOptions = []

    const parents = rawOptions
        .filter((tax) => tax.parent)
        .map((tax) => tax.parent)
        .filter((tax, index, self) => self.indexOf(tax) === index)
        .sort(sortByTermOrder)

    const parentTaxms = rawOptions.filter((tax) => parents.includes(tax.term_id))

    parentTaxms.forEach((parent) => {
        const category = generateCategoryOfParent(parent, rawOptions);

        cateogoryOptions.push(category);
    })

    const othersLabel = parentTaxms.length > 0
        ? `${translationObject.others_label} ${label}`
        : ''

    const others = generateCategoryBaseConstruct(othersLabel);

    others.options = rawOptions
        .filter(tax => !tax.parent && !parents.includes(tax.term_id))
        .map(mapToOptionObject)
        .sort(sortByTermOrder)

    cateogoryOptions.push(others);

    return cateogoryOptions
}

function generateCategoryOfParent(parent, rawOptions) {
    const newCategory = generateCategoryBaseConstruct(parent.name);

    parent.name = `${translationObject.all_label} ${parent.name}`

    const clean_category_options = rawOptions
        .filter((tax) => tax.parent === parent.term_id)
        .sort(sortByTermOrder)
        .map(tax => mapToOptionObject(tax, parent))

    const parent_category_option = rawOptions
        .filter((tax) => tax.term_id === parent.term_id)
        .map(tax => mapToOptionObject(tax, parent))

    newCategory.options = parent_category_option.concat(clean_category_options)

    return newCategory;
}


function generateCategoryBaseConstruct(name) {
    return {
        label: `${name}`,
        options: []
    };
}

function mapToOptionObject(tax, parent) {
    const colorCode = colorCodes.getEntryWithSlugLike(tax.slug)
    const colorStyle = generateGradientCssTagForColor(colorCode)

    const optionLabel = !hideOptionName(tax, parent) ? tax.name : '  '

    return {label: optionLabel, value: tax.term_id, colorStyle: colorStyle, slug: tax.slug, color: colorCode, parent: parent};
}

function generateGradientCssTagForColor(baseColor) {
    const colorCodes = convert.hex.rgb(baseColor)

    const colorCodesJoin = colorCodes.join(',')

    return `linear-gradient(90deg, rgba(${colorCodesJoin},0) 0%, rgba(${colorCodesJoin},0.7581232322030375) 35%, rgba(${colorCodesJoin},1) 59%)`
}

export function getDefaultSelectionFromUrl(urlParam, preparedOptions) {
    const urlParamValueRaw = getUrlParamValue(urlParam)

    const urlParamValues = urlParamValueRaw.split(',')

    const preSelectedOptions = preparedOptions.map((optionCategory) => {
        const filterCategoryOptions = optionCategory.options

        return filterCategoryOptions.filter(filterCategoryOption => {
            return urlParamValues.includes(filterCategoryOption.slug)
        })
    })

    const cleanedPreSelectedOptions = preSelectedOptions.filter(options => options.length > 0)

    const preselectedTermIds = []

    cleanedPreSelectedOptions.forEach((options) => {
        options.forEach(option => {
            preselectedTermIds.push(option)
        })
    })

    return preselectedTermIds
}

/**
 * @param label {string}
 * @param selectLabel {string}
 * @return {string}
 */
export function preparePlaceholder(label, selectLabel) {
    return selectLabel.includes('%s') ? selectLabel.replaceAll('%s', label) : `${label} ${selectLabel}`
}

function sortByTermOrder(termA, termB) {
    const a_bigger_b = parseInt(termA.term_order) > parseInt(termB.term_order)

    return a_bigger_b ? 1 : -1;
}
