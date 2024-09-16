import React, {useEffect} from "react";

import chroma from 'chroma-js';

import Select, {components} from 'react-select';

import translationObject from "../../../TranslationObject";
import prepareDropdownOptions, {getDefaultSelectionFromUrl, OptionAvailableHandler, preparePlaceholder} from "./utils";
import {clone} from "../../../utils";

const FilterDropdown = (filterEntry) => {
    /** @type { FilterEntry } */
    const filterEntryData = filterEntry.data ? filterEntry.data : filterEntry

    const key = filterEntryData.key
    const label = filterEntryData.label
    const placeholder = preparePlaceholder(filterEntryData.label, translationObject.select_label)
    const urlParam = filterEntryData.url ?? ''
    const onChange = filterEntryData.onChange

    const multiSelection = filterEntryData.multiSelect ?? true

    const taxOptionsRaw = clone(filterEntryData.options) ?? []

    const filterSelected = filterEntry.filterSelected
    const filterPosts = filterEntry.filterPosts

    // noinspection JSUnresolvedReference
    const optionAvailable = filterEntryData.optionAvailable ?? ((option) => {return option})

    const optionAvailableHandler = new OptionAvailableHandler(filterSelected, filterPosts, optionAvailable);

    const _options = prepareDropdownOptions(taxOptionsRaw, label, optionAvailableHandler)
    const _preselectedValue = getDefaultSelectionFromUrl(urlParam, _options)

    const colourStyles = {
        control: (styles) => ({...styles, backgroundColor: 'white'}),
        option: (styles, {data, isDisabled, isFocused, isSelected}) => {
            const color = chroma(data.color);

            return {
                ...styles,
                backgroundColor: isDisabled
                    ? undefined
                    : isSelected
                        ? data.color
                        : isFocused
                            ? color.alpha(0.1).darken(5).css()
                            : undefined,

                color: isDisabled
                    ? '#ccc'
                    : chroma.contrast(color, 'white') > 2
                        ? 'white'
                        : 'black',

                cursor: isDisabled ? 'not-allowed' : 'default',

                ':active': {
                    ...styles[':active'],
                    backgroundColor: !isDisabled
                        ? isSelected
                            ? data.color
                            : color.alpha(0.5).css()
                        : undefined,
                },
            };
        },
        placeholder: (styles)=> ({
            ...styles,
            transition: 'color 100ms ease-in-out'
        }),
        container: (styles) => ({
            ...styles,
            ':hover div[class$="-placeholder"]': {
                color: 'black',
                fontWeight: '700',
            }
        }),
        multiValue: (styles, {data}) => {
            const color = chroma(data.color);

            return {
                ...styles,
                color: '#000',
                backgroundColor: color.alpha(0.1).css(),
                borderRadius: '3px',
            };
        },
        multiValueLabel: (styles, {data}) => {
            const color = chroma(data.color)

            const optionNameHidden = data.renderOptionText

            const backgroundIsWhite = chroma.contrast(color, 'white') < 1.1

            const needsBorder = backgroundIsWhite && optionNameHidden

            const makeBackgroundBlandGray = backgroundIsWhite && !needsBorder

            return {
                ...styles,
                color: 'black',
                backgroundColor: makeBackgroundBlandGray ? '#e6e6e6' : color.css(),

                borderStyle: 'solid',
                borderWidth: needsBorder ? '1px' : '0',
                borderColor: needsBorder ? 'black' : 'none',

                paddingTop: needsBorder ? '2px' : styles.paddingTop,
                paddingBottom: needsBorder ? '2px' : styles.paddingBottom,

                paddingRight: '1rem',
                paddingLeft: '1rem',
            }
        },
        multiValueRemove: (styles, {data}) => {
            const color = chroma(data.color)

            return {
                ...styles,
                color: '#9f9f9f',
                backgroundColor: color.alpha(0.1).darken(5).css(),
                ':hover': {
                    backgroundColor: color.css(),
                    color: '#000',
                },
            }
        },
    };

    const customTheme = ((theme) => ({
        ...theme,
        colors: {
            ...theme.colors,
            primary: 'black',
        }
    }))

    useEffect(() => {
        onChange(_preselectedValue)
    }, []);

    const Option = (props) => {
        return (
            <div style={{background: props.data.colorStyle, fontWeight: '600'}}>
                <components.Option {...props} />
            </div>
        );
    };

    return <div key={key} className="relative w-full max-w-full mb-4">
        <label>{label}</label>
        <Select
            isMulti={multiSelection}
            defaultValue={_preselectedValue}
            name={label}
            options={_options}
            onChange={(newValue) => {
                onChange(newValue)
            }}
            placeholder={placeholder}
            components={{Option}}
            styles={colourStyles}
            theme={customTheme}
        />
    </div>
}

export default FilterDropdown