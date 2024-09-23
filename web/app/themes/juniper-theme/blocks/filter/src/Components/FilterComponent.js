import React, {useEffect, useState} from "react";
import {
    isArray, loadingElement, loadInPostsFromPage, postHasSampleAvailable,
    postInSelection,
    postInTextSelection,
    postIsAvailableOnline, refreshSlick, renderMock,
    renderPost,
    rerenderSlick
} from "../utils";
import FilterTextSearch from "./SingleFilterComponents/Text/FilterTextSearch";
import FilterCheckbox from "./SingleFilterComponents/Checkbox/FilterCheckbox";
import translationObject from "../TranslationObject";
import {FilterEntry, FilterTypes} from "../FilterEntry";
import FilterDropdown from "./SingleFilterComponents/Dropdown/FilterDropdown";

const FilterComponent = (data) => {
    const title = data.title ?? '';

    const postType = data.postType ?? 'product'

    // noinspection JSUnresolvedReference
    const endpoint = data.pullEndpoint

    // noinspection JSUnresolvedReference
    const nocache = data.nocache ?? false

    // noinspection JSUnresolvedReference
    const sample_available = data.sample_available
    // noinspection JSUnresolvedReference
    const online_available = data.online_available

    const show_sample_available_filter = sample_available === 'filter'
    const show_online_available_filter = online_available === 'filter'

    const always_filter_sample_available = sample_available === 'outright'
    const always_filter_online_available = online_available === 'outright'

    // mocked card render
    // noinspection JSUnresolvedReference
    const mockedCard = data.mocked ?? ''

    // filter options that get displayed
    const [filterOptions, setFilterOptions] = useState([])

    // selection of the filter (what to filter for)
    const [filterSelected, setFilterSelected] = useState({})

    // all posts that exist
    const [allPosts, setAllPosts] = useState([])

    // posts after being run through the filter
    const [filteredPosts, setFilteredPosts] = useState([])

    const [loading, isLoading] = useState(true);

    function loadPosts() {
        isLoading(true)

        if (data.posts) {
            const posts = data.posts

            setAllPosts(posts)
            isLoading(false)
        } else {
            const posts = loadInPostsFromPage(endpoint, postType, 0, nocache);

            posts.then((data) => {
                setAllPosts(data)
                isLoading(false)
            })
        }
    }

    function applyFilterReturn(filter, postsToFilter = null) {
        let filterOptions = Object.entries(filter).filter(keyValue => keyValue[1] !== "")

        let toFilterData = postsToFilter ?? allPosts

        // filter out false and empty values
        filterOptions = filterOptions.filter((filter) => filter[1] && filter[1].length !== 0)

        if (filterOptions.length === 0) {
            return toFilterData
        }

        for (const filterOption of filterOptions) {
            const filterOptionName = filterOption[0]
            const filterValue = filterOption[1]

            switch (filterOptionName) {
                case 'searchText':
                    try {
                        const textRegex = new RegExp(filterValue.toLowerCase().trim().replaceAll(' ', '.*?'))
                        toFilterData = toFilterData.filter((post) => postInTextSelection(textRegex, post))
                    } catch (e) {
                        // failsave if the regex is faulty
                        const textRegex = filterValue.toLowerCase().trim().replaceAll(/[*.?\[\]]/g, '')
                        toFilterData = toFilterData.filter((post) => postInTextSelection(textRegex, post))
                    }
                    break
                case 'sampleAvailable':
                    toFilterData = toFilterData.filter(postHasSampleAvailable)
                    break
                case 'onlineAvailable':
                    toFilterData = toFilterData.filter(postIsAvailableOnline)
                    break
                default:
                    toFilterData = isArray(filterValue)
                        ? toFilterData.filter((post) => filterValue.some((singleValue) => postInSelection(filterOptionName, singleValue.value, post)))
                        : toFilterData.filter((post) => postInSelection(filterOptionName, filterValue.value, post))
            }
        }

        return toFilterData
    }

    function setUpFilterPresets() {
        setFilterSelected(prevFilter => ({
            ...prevFilter,
            sampleAvailable: always_filter_sample_available,
            onlineAvailable: always_filter_online_available
        }))
    }

    function applyFilter(filter) {
        const filteredPosts = applyFilterReturn(filter)
        setFilteredPosts(filteredPosts)
    }

    function applyValueToFilter(filterKey, filterValue) {
        setFilterSelected((prevFilter) => {
            return {
                ...prevFilter,
                [filterKey]: filterValue
            }
        })
    }

    const directFilterRun = (filterKey, filterValue, prevFilter, toFilterValues) => {
        return applyFilterReturn({
            ...prevFilter,
            [filterKey]: filterValue
        }, toFilterValues)
    }

    function setUpFilters() {
        // noinspection JSUnresolvedReference
        let filterOptions = data.filterOptions ?? []

        const defaultType = FilterTypes.DROPDOWN

        filterOptions = filterOptions.map((raw) => FilterEntry.makeFromRaw(raw.type ?? defaultType, raw))

        const preparedOptions = filterOptions.map((filterOption) => {
            filterOption.onChange = (selected) => {
                applyValueToFilter(filterOption.filterChoice, selected)
            }

            filterOption.label = translationObject[filterOption.name]
                ? translationObject[filterOption.name]
                : filterOption.label

            filterOption.url = filterOption.filterChoice.replaceAll('_', '-')

            filterOption.optionAvailable = (option, filterSelected, filterPosts) => {
                if(filterPosts <= 0) {
                    return true
                }

                return directFilterRun(filterOption.filterChoice, option, filterSelected, filterPosts).length > 0
            }

            return filterOption
        })

        // move checkboxes to the back
        preparedOptions.sort((filterOption) => {
            return filterOption.filterType === FilterTypes.CHECKBOX ? 1 : -1
        })

        setFilterOptions(preparedOptions)
    }

    useEffect(() => {
        applyFilter(filterSelected)
    }, [filterSelected]);

    useEffect(() => {
        applyFilter(filterSelected)
    }, [allPosts]);

    useEffect(async () => {
        setUpFilters()
        setUpFilterPresets()
        loadPosts()
        rerenderSlick()
    }, []);

    useEffect(rerenderSlick, [filteredPosts]);

    return (
        <div className={'w-full container'}>
            <div className={""}>
                <h1
                    data-aos={'fade-up'}
                    className={"mb-0 sm:mb-6"}>{title}</h1>
            </div>
            <div className={"mx-auto"}>
                <div id={'filter-items'}
                     data-aos={'fade-up'}
                     data-aos-delay={'50'}
                     data-aos-offset={0}
                     className={'grid grid-cols-12 justify-start mt-6 sm:mt-0'}>
                    <FilterTextSearch
                        label={'Product Search'}
                        name={'Product Search'}
                        url={'text'}
                        placeholder={translationObject.product_search}
                        onChange={(newValue) =>
                            applyValueToFilter('searchText', newValue.trim().toLowerCase())
                        }
                    />
                </div>

                <div data-aos={'fade-up'}
                     data-aos-delay={'100'}
                     data-aos-offset={0}
                     className={'grid grid-cols-1 md:grid-cols-3 relative z-10 sm:gap-7 mt-6 sm:mt-0'}>
                    {filterOptions.map((filter) => <FilterDropdown
                        data={filter}
                        filterSelected={filterSelected}
                        filterPosts={allPosts}
                    /> )}
                </div>

                <div data-aos={'fade-up'}
                     data-aos-delay={'150'}
                     data-aos-offset={0}
                     className="flex flex-row justify-between gap-5 col-span-12">
                    <div className={'flex flex-row gap-5'}>
                        {show_sample_available_filter && <FilterCheckbox
                            key={'sampleAvailable'}
                            name={'sampleAvailable'}
                            label={translation.filter_sample_available}
                            url={'purchasability'}
                            onChange={(isChecked) => setFilterSelected(prevFilters => (
                                {
                                    ...prevFilters,
                                    sampleAvailable: isChecked
                                }
                            ))}
                        />}
                        {show_online_available_filter && <FilterCheckbox
                            key={'onlineAvailable'}
                            name={'onlineAvailable'}
                            label={translation.filter_online_available}
                            url={'online-available'}
                            isChecked={filterSelected.onlineAvailable}
                            onChange={(isChecked) => setFilterSelected(prevFilters => (
                                {
                                    ...prevFilters,
                                    onlineAvailable: isChecked
                                }
                            ))}
                        />}
                    </div>
                    <div className={'flex flex-row gap-3 text-sm font-medium text-gray-900 mr-1'}>
                        {translationObject.results_label}: {filteredPosts?.length}
                    </div>
                </div>
            </div>
            <div className={'mt-5 relative isolate'}>
                {loading && loadingElement() && !data.posts}
                <div className={"grid grid-cols-1 md:grid-cols-3 md:mb-10 md:gap-7 filter-grid flex-wrap"}>
                    {allPosts?.length === 0 && [...Array(6).keys()].map(() => renderMock(mockedCard))}
                    {filteredPosts?.length > 0 ?
                        filteredPosts.map((post, index) => renderPost(post, index, false, refreshSlick))
                        : <div className={'w-full text-center col-span-3'}> {translationObject.no_results} </div>}
                </div>
            </div>
        </div>
    )
}

export default FilterComponent;
