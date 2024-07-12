const customMusterbestellung = () => {
    const restUrl = customMusterbestellungParams.restUrl
    const themeUrl = customMusterbestellungParams.themeUrl
    const musterbestellungProducts = customMusterbestellungParams.musterbestellungProducts
    const sampleBoxItemName = customMusterbestellungParams.sampleBoxItemName

    const shopUrl = scriptData.shopUrl

    const cartItemsBlockClass = 'wc-block-cart-items'

    const cookieName = 'musterbestellungProducts'
    let isUpdating = false


    function fetchAndUpdateMusterbestellung(reload = false) {
        showLoading()

        const productIds = JSON.parse(getCookie(cookieName)) || []

        toggleMusterbestellungVisibility(productIds.length > 0) // Update visibility based on cookie content

        isUpdating = true

        fetch(`${restUrl}wps/v1/musterbestellung/?ids=${productIds.join(',')}`)
            .then(response => response.json())
            .then(products => {
                if (reload) {
                    window.location.reload()
                    return
                }

                isUpdating = false

                renderProducts(products, productIds)
            })
    }

    function showLoading() {
        const containers = document.querySelectorAll('#musterbestellung .products, #musterbestellung-mobile .products-mobile')
        containers.forEach(container => {
            container.innerHTML = ''

            const loadingDiv = document.createElement('div')
            loadingDiv.classList.add('products-loading')
            loadingDiv.innerHTML = `<img src="${themeUrl}/assets/img/musterbestellung-loading.svg" alt="loading" />`
            container.appendChild(loadingDiv)
        })
    }

    function initializeMusterbestellung () {
        const products = musterbestellungProducts
        const productIds = products.map(product => product.id)

        toggleMusterbestellungVisibility(productIds.length > 0)

        // Toggle visibility based on product count
        renderProducts(products, productIds)
    }

    function toggleMusterbestellungVisibility(isVisible) {
        const musterbestellungDiv = document.getElementById('musterbestellung')
        const musterbestellungMobileDiv = document.getElementById('musterbestellung-mobile')

        if (isVisible) {
            musterbestellungDiv.classList.remove('hidden')
            musterbestellungMobileDiv.classList.remove('hidden'); // Reset to default or use 'block'
        } else {
            musterbestellungDiv.classList.add('hidden'); // Hide the div
            musterbestellungMobileDiv.classList.add('hidden'); // Hide the div
        }
    }

    function renderSampleProductElement(id, name, image) {
        return `<div class="tooltip group mb-4">
                    <div class="product overflow-hidden rounded-full">
                        <img src="${image}" class="block" alt="${name}">
                    </div>
                    <span class="tooltiptext bg-accent rounded-sm px-2 py-2 text-black invisible group-hover:visible whitespace-nowrap truncate">
                        ${name}
                    </span>
                    <button class="delete-product" data-product-id="${id}" data-product-name="${name}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="11" viewBox="0 0 10 11" fill="none">
                            <path d="M9.14583 2.41667H1.35417C1.26024 2.41667 1.17015 2.45398 1.10373 2.5204C1.03731 2.58682 1 2.6769 1 2.77083C1 2.86476 1.03731 2.95485 1.10373 3.02127C1.17015 3.08769 1.26024 3.125 1.35417 3.125H1.70833V9.5C1.70833 9.68786 1.78296 9.86803 1.9158 10.0009C2.04864 10.1337 2.2288 10.2083 2.41667 10.2083H8.08333C8.27119 10.2083 8.45136 10.1337 8.5842 10.0009C8.71704 9.86803 8.79167 9.68786 8.79167 9.5V3.125H9.14583C9.23976 3.125 9.32985 3.08769 9.39627 3.02127C9.46269 2.95485 9.5 2.86476 9.5 2.77083C9.5 2.6769 9.46269 2.58682 9.39627 2.5204C9.32985 2.45398 9.23976 2.41667 9.14583 2.41667ZM8.08333 9.5H2.41667V3.125H8.08333V9.5ZM3.125 1.35417C3.125 1.26024 3.16231 1.17015 3.22873 1.10373C3.29515 1.03731 3.38524 1 3.47917 1H7.02083C7.11476 1 7.20485 1.03731 7.27127 1.10373C7.33769 1.17015 7.375 1.26024 7.375 1.35417C7.375 1.4481 7.33769 1.53818 7.27127 1.6046C7.20485 1.67102 7.11476 1.70833 7.02083 1.70833H3.47917C3.38524 1.70833 3.29515 1.67102 3.22873 1.6046C3.16231 1.53818 3.125 1.4481 3.125 1.35417Z" fill="black" stroke="black" stroke-width="0.3"/>
                        </svg>
                    </button>
                </div>`
    }

    function renderSampleProductPlaceholder(link) {
        return `<div class="placeholder mb-4 border-solid border-2 border-black rounded-full">
                    <a href="${link}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 22 22" fill="none">
                        <path d="M19.25 11C19.25 11.1823 19.1776 11.3572 19.0486 11.4861C18.9197 11.6151 18.7448 11.6875 18.5625 11.6875H11.6875V18.5625C11.6875 18.7448 11.6151 18.9197 11.4861 19.0486C11.3572 19.1776 11.1823 19.25 11 19.25C10.8177 19.25 10.6428 19.1776 10.5139 19.0486C10.3849 18.9197 10.3125 18.7448 10.3125 18.5625V11.6875H3.4375C3.25516 11.6875 3.0803 11.6151 2.95136 11.4861C2.82243 11.3572 2.75 11.1823 2.75 11C2.75 10.8177 2.82243 10.6428 2.95136 10.5139C3.0803 10.3849 3.25516 10.3125 3.4375 10.3125H10.3125V3.4375C10.3125 3.25516 10.3849 3.0803 10.5139 2.95136C10.6428 2.82243 10.8177 2.75 11 2.75C11.1823 2.75 11.3572 2.82243 11.4861 2.95136C11.6151 3.0803 11.6875 3.25516 11.6875 3.4375V10.3125H18.5625C18.7448 10.3125 18.9197 10.3849 19.0486 10.5139C19.1776 10.6428 19.25 10.8177 19.25 11Z" fill="black"/>
                    </svg>
                    </a>
                </div>`
    }

    function printProductCount(span, count, max) {
        span.textContent = `${count} / ${max}`
    }

    function generateSampleProductRepresentationElement(product) {
        const productFinderPath = '/productfinder?purchasability=true'
        const placeholderLink = shopUrl + productFinderPath

        const productDiv = document.createElement('div')
        productDiv.className = 'product-slot'

        const productId = product?.id ?? false
        const productName = product?.name ?? false
        const productImage = product?.image[0] ?? false

        productDiv.innerHTML = product && productImage
            ? renderSampleProductElement(productId, productName, productImage)
            : renderSampleProductPlaceholder(placeholderLink)

        return productDiv
    }

    /**
     * @param products {array}
     * @param productIds {array}
     */
    function renderProducts(products, productIds){
        const containers = document.querySelectorAll('#musterbestellung .products, #musterbestellung-mobile .products-mobile')
        const productNumberSpans = document.querySelectorAll('#musterbestellung .product-number, #musterbestellung-mobile .product-number')

        const numberOfProducts = products.length
        const maxProductsAllowed = 3

        // Update the product number display
        productNumberSpans.forEach((span) => printProductCount(span, numberOfProducts, maxProductsAllowed))

        // Clear existing products and fill up to 3 product slots in each container
        containers.forEach(container => {
            container.innerHTML = ''

            const fillerEntries = Array(Math.max(maxProductsAllowed - numberOfProducts, 0)).fill(null)
            const productsToShow = [
                ...products,
                ...fillerEntries
            ]

            const productContainersRendered = productsToShow.map(generateSampleProductRepresentationElement)

            productContainersRendered.forEach((productContainer) => container.appendChild(productContainer))
        })

        attachDeleteEventListeners(productIds)
    }

    function updateProductItemList(updatedProductIds) {
        setCookie(cookieName, JSON.stringify(updatedProductIds))
        fetchAndUpdateMusterbestellung(true)
    }

    function attachDeleteEventListeners(productIds) {
        const deleteButtons = document.querySelectorAll('.delete-product')

        deleteButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                const productId = e.currentTarget.getAttribute('data-product-id')
                const updatedProductIds = productIds.filter(id => id !== productId)
                updateProductItemList(updatedProductIds);
            })
        })
    }

    function setCookie(name, value) {
        const expiryDate = new Date()
        expiryDate.setTime(expiryDate.getTime() + (365 * 24 * 60 * 60 * 1000)) // Set cookie to expire in 1 year
        const expires = "expires=" + expiryDate.toUTCString()
        document.cookie = name + "=" + value + ";" + expires + ";path=/"
    }

    function getCookie(name) {
        const value = `; ${document.cookie}`
        const parts = value.split(`; ${name}=`)

        return parts.length === 2
            ? parts.pop().split(';').shift()
            : null;
    }

    function handleMusterbestellungSelectsUpdate() {
        let selects = document.querySelectorAll('.musterbestellung-custom-fields select')
        let cookieArr = getCookie(cookieName)
        let valuesToEnable = []

        if (selects.length === 0 || cookieArr.length === 0) {
            return
        }

        selects.filter((select) => !cookieArr.includes(select.value))
            .forEach((select) => valuesToEnable.push(select.value))
            .forEach((select) => select.value = '')

        selects.map((select) => select.querySelectorAll('option'))
            .filter((options) => options.find((option) => valuesToEnable.includes(option)))
            .forEach((options) => options.forEach((option) => option.disabled = false))
    }

    // add to musterbestellung
    function attachSampleAddButtonEventListeners() {
        const appendButtons = document.querySelectorAll('.add-to-musterbestellung')

        appendButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                const productId = e.currentTarget.getAttribute('data-product-id')
                let products = JSON.parse(getCookie(cookieName)) || []

                // Check if there are already 3 products in the list
                if (products.length >= 3) {
                    openModal('full-samplebox-modal')
                    return
                }

                // Check if the product is already in the list
                if (products.includes(productId)) {
                    return
                }

                // Add the new product and update the cookie
                products.push(productId)
                setCookie(cookieName, JSON.stringify(products))
                fetchAndUpdateMusterbestellung(true)
            })
        })
    }

    function listenAndHandleCartSampleRmoveButton() {
        let cartItemsListContainerHolder = undefined

        const cartListFetcherMutationObserver = new MutationObserver(() => {
            if(isDefined(cartItemsListContainerHolder)) {
                return
            }

            const cartContainer = document.getElementsByClassName(cartItemsBlockClass)

            if(cartContainer.length === 0) {
                return
            }

            console.log('caught cart container')

            cartItemsListContainerHolder = cartContainer.item(0)

            const cartItemContainers = cartItemsListContainerHolder.getElementsByClassName('wc-block-cart-items__row')

            for (const cartItemContainer of cartItemContainers) {
                applyListenerToRemovalButton(cartItemContainer)
            }
        })

        function applyListenerToRemovalButton(container) {
            const removalButton = container.getElementsByClassName('wc-block-cart-item__remove-link').item(0)
            removalButton.addEventListener('click', reactionToItemRemovalButtonPressed)
        }

        function reactionToItemRemovalButtonPressed(event) {
            const button = event.target

            const nameElement = getNearestClass(button, 'wc-block-components-product-name')

            const name = nameElement.innerText

            const sampleBoxWasRemoved = name === sampleBoxItemName

            sampleBoxWasRemoved && updateProductItemList([])
        }

        const possibleCartContainment = document.getElementsByClassName('article-body')[0] ?? null

        if(!possibleCartContainment) {
            return
        }

        cartListFetcherMutationObserver.observe(possibleCartContainment, {attributes: true, subtree: true})
    }
    
    // Set up an observer to watch for cookie changes
    // let lastCookie = document.cookie
    // setInterval(() => {
    //     const cookiesGotUpdated = document.cookie !== lastCookie
    //
    //     if (!cookiesGotUpdated || isUpdating) {
    //         return
    //     }
    //
    //     lastCookie = document.cookie
    // fetchAndUpdateMusterbestellung(false)
    // handleMusterbestellungSelectsUpdate()
    // }, 1000) // Poll every 1000ms

    initializeMusterbestellung()
    attachSampleAddButtonEventListeners()
    listenAndHandleCartSampleRmoveButton()
    fetchAndUpdateMusterbestellung(false)
}

addEventListener('DOMContentLoaded', customMusterbestellung)