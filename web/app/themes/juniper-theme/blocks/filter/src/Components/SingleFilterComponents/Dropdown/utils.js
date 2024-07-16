import translationObject from "../../../TranslationObject";
// import colorCodes from "../../../ColorCodes";
import {clone, getUrlParamValue} from "../../../utils";
import convert from "color-convert";
import {FilterEnvStorage} from "../../../FilterEntry";

/**
 * @param rawOptions {FilterOption[]}
 * @param label {string}
 * @param optionAvailaleHandler { OptionAvailableHandler }
 * @return {*[]}
 */
export default function prepareDropdownOptions(rawOptions, label, optionAvailaleHandler) {
    const cateogoryOptions = []

    const parents = rawOptions
        .filter((tax) => tax.parent)
        .map((tax) => tax.parent)
        .filter((tax, index, self) => self.indexOf(tax) === index)
        .sort(sortByTermOrder)

    const parentTaxms = rawOptions.filter((tax) => parents.includes(tax.termId))

    parentTaxms.forEach((parent) => {
        const category = generateCategoryOfParent(parent, rawOptions, optionAvailaleHandler);

        cateogoryOptions.push(category);
    })

    const othersLabel = parentTaxms.length > 0
        ? `${translationObject.others_label} ${label}`
        : ''

    const others = generateCategoryBaseConstruct(othersLabel);

    others.options = rawOptions
        .filter(tax => !tax.parent && !parents.includes(tax.termId))
        .map(mapToOptionObject)
        .sort(sortByTermOrder)
        .filter((option) => optionAvailaleHandler.optionIsAvailable(option))

    cateogoryOptions.push(others);

    return cateogoryOptions
}

/**
 * @param parent { FilterOption }
 * @param rawOptions { FilterOption[] }
 * @param optionAvailableHandler { OptionAvailableHandler }
 * @return {{options: *[], label: string}}
 */
function generateCategoryOfParent(parent, rawOptions, optionAvailableHandler) {
    const newCategory = generateCategoryBaseConstruct(parent.name);

    parent.name = `${translationObject.all_label} ${parent.name}`

    const clean_category_options = rawOptions
        .filter((tax) => tax.parent === parent.termId)
        .sort(sortByTermOrder)
        .map(mapToOptionObject)

    const parent_category_option = rawOptions
        .filter((tax) => tax.termId === parent.termId)
        .map(mapToOptionObject)

    newCategory.options = parent_category_option.concat(clean_category_options)

    newCategory.options = newCategory.options.filter((option) => optionAvailableHandler.optionIsAvailable(option))

    return newCategory;
}


function generateCategoryBaseConstruct(name) {
    return {
        label: `${name}`,
        options: []
    };
}

function mapToOptionObject(tax) {
    const colorCode = tax.backgroundColor
    const colorStyle = generateGradientCssTagForColor(colorCode)

    const optionLabel = tax.renderOptionText ? tax.name : '  '

    return new DropdownOption(optionLabel, tax.termId, colorStyle, tax.slug, colorCode, tax.parent, tax.renderOptionText, tax.filterChoice)
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

/**
 * @param termA
 * @param termB
 * @return {number}
 */
function sortByTermOrder(termA, termB) {
    const a_bigger_b = parseInt(termA.termOrder) > parseInt(termB.termOrder)

    return a_bigger_b ? 1 : -1;
}

class DropdownOption {
    label = ''
    value = ''
    colorStyle = ''
    slug = ''
    color = ''
    parent = 0
    renderText = true
    filterChoice = ''

    constructor(label, value, colorStyle, slug, color, parent, renderText, filterChoice) {
        this.label = label;
        this.value = value;
        this.colorStyle = colorStyle;
        this.slug = slug;
        this.color = color;
        this.parent = parent;
        this.renderText = renderText
        this.filterChoice = filterChoice
    }
}

export class OptionAvailableHandler {
    filterSelected
    filterPosts
    filterFunction

    constructor(filterSelected, filterPosts, filterFunction) {
        this.filterSelected = filterSelected;
        this.filterPosts = filterPosts;
        this.filterFunction = filterFunction;
    }

    optionIsAvailable(option) {
        // ignore filter for own filterChoice
        if(this.filterSelected[option.filterChoice]?.length > 0) {
            return true
        }

        return this.filterFunction(option, this.filterSelected, this.filterPosts)
    }
}