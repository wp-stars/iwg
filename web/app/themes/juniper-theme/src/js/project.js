
const toggleNavbar = (event) => {
    if (document.body.scrollTop > 50 || document.documentElement.scrollTop > 50) {
        document.querySelector(".navbar").classList.add("navbar-scrolled")
    } else {
        document.querySelector(".navbar").classList.remove("navbar-scrolled")
    }
} 

document.addEventListener("scroll", toggleNavbar)


const toggleMenu = () => {
    document.querySelector(".navbar").classList.toggle("navbar-open")
}

let navbarToggler = document.querySelector('#navbarToggler')
navbarToggler.addEventListener('click', () => {
    toggleMenu()
})
// Event listener for new navbar toggler with id "musterbestellung-btn-mobile"
let musterbestellungBtnMobile = document.querySelector('#musterbestellung-btn-mobile');
if (musterbestellungBtnMobile) {
    musterbestellungBtnMobile.addEventListener('click', () => {
        document.querySelector("#musterbestellung-mobile").classList.toggle("hidden");
    });
}
let toggleMusterbestellungMobile = document.querySelector('#musterbestellung-floating');
if (toggleMusterbestellungMobile) {
    toggleMusterbestellungMobile.addEventListener('click', () => {
        document.querySelector("#musterbestellung-mobile").classList.toggle("hidden");
    });
}

// Close button for musterbestellung-mobile
let musterbestellungClose = document.querySelector('#musterbestellung-close');
if (musterbestellungClose) {
    musterbestellungClose.addEventListener('click', () => {
        document.querySelector("#musterbestellung-mobile").classList.add("hidden");
    });
}

// open and close the dialog modal function
const openModal = (id) => {
    const dialog = document.getElementById("modal-" + id);
    dialog.showModal();
}

const closeModal = (id) => {
    const dialog = document.getElementById("modal-" + id);
    dialog.close();
}

jQuery(document).ready(function() {
    const initSlider = ($element) => {
        // Initialize the slider first with default settings
        $element.each(function() {
            const $slider = jQuery(this);

            if ($slider.hasClass('slick-initialized')) {
                return
            }

            $slider.slick({
                slidesToShow: 1,
                slidesToScroll: 1,
                arrows: false,
                fade: true,
                dots: false // Initialize without dots
            });

            // Check the number of slides after initialization
            const slideCount = $slider.slick('getSlick').slideCount;
            if (slideCount > 1) {
                // Update the slider options to show dots if there are more than one slide
                $slider.slick('slickSetOption', 'dots', true, true);
            }
        });
    };

    const refreshSlider = ($element) => {
        // Initialize the slider first with default settings
        $element.each(function() {
            const $slider = jQuery(this);

            if (!$slider.hasClass('slick-initialized')) {
                return
            }

            $slider.slick('resize')
        });
    };

    // Initialize slider on page load
    initSlider(jQuery('.product-card-slider'));

    // Event delegation for dynamically added elements
    jQuery(document).on('click', '.product-card-slider .slick-dots li', function(e) {
        e.preventDefault();
        e.stopPropagation(); // To stop the event from bubbling up to the parent link
    });

    // Check and initialize slider for .woocommerce-product-gallery__wrapper
    if (jQuery('.woocommerce-product-gallery__wrapper').length > 0) {
        initSlider(jQuery('.woocommerce-product-gallery__wrapper'));
    } else {
        console.log('WooCommerce product gallery wrapper element not found');
    }

    // Event listener for filter rendering done
    jQuery(document).on('filterRenderingDone', function () {
        initSlider(jQuery('.product-card-slider')); // Reinitialize the slider after filter rendering is done
    });

    jQuery(document).on('filterRefreshRenderedElements', function() {
        refreshSlider(jQuery('.product-card-slider'));
    })
});

function isDefined(variable) {
    return typeof variable !== 'undefined';
}

function getNextOuterClass(current, className) {
    const currentContainsClass = current.classList.contains(className)

    return currentContainsClass
        ? current
        : getNextOuterClass(current.parentNode, className)
}

function getNextInnerClass(current, className) {
    const currentContainsClass = current.classList.contains(className)

    if(currentContainsClass) {
        return current
    }

    const nextInnerClass = current.children.toConnectedArray().map((element) => {
        return getNextInnerClass(element, className)
    })

    return nextInnerClass?.first()
}

function getNearestClass(current, className) {
    const currentContainsClass = current.classList.contains(className)

    if(currentContainsClass) {
        return current
    }

    const nextInnerClass = getNextInnerClass(current, className)

    if(!nextInnerClass) {
        const parent = current.parentNode

        return getNearestClass(parent, className)
    } else {
        return nextInnerClass
    }

}

function elementContainsImg(elementBase) {
    return elementBase.getElementsByTagName("img")
}

function findNextOuterImage(elementBase) {
    const children = elementBase.children

    for (const child of children) {
        const images = elementContainsImg(child)

        if (images.length > 0) {
            return images[0]
        }
    }


    const parent = elementBase.parentNode
    return findNextOuterImage(parent)
}

function spliceCollection(collection, chunkSize) {
    const chunks = [[]]
    for (let i = 0; i < collection.length; i++) {
        try {
            chunks[Math.floor(i / chunkSize)].push(collection.item(i))
        } catch (e) {
            chunks[Math.floor(i / chunkSize)] = []
            chunks[Math.floor(i / chunkSize)].push(collection.item(i))
        }
    }
    return chunks
}

HTMLCollection.prototype.toConnectedArray = function () {
    const connectedArray = []
    for (const block of this) {
        connectedArray.push(block)
    }
    return connectedArray
}

Array.prototype.last = function () {
    return this[this.length - 1]
}

Array.prototype.first = function () {
    return this[0] ?? null
}

Array.prototype.removeUndefined = function () {
    return this.filter(isDefined)
}

Element.prototype.appendBefore = function (element, parentNode = null) {
    element.parentNode.insertBefore(this, element);
};

// highlight classes
function addClassToPlusCellsInFigures() {
    document.querySelectorAll('figure.wp-block-table').forEach(function(figure) {
        let tables = figure.getElementsByTagName('table');
        for (let i = 0; i < tables.length; i++){
            handleTable(tables[i]);
        }
    });
}

const handleTable = (table) => {
    let cells = table.getElementsByTagName('td');
    for (var j = 0; j < cells.length; j++) {
        handleTableCell(cells[j]);
    }
}

const handleTableCell = (cell) => {
    if (containsPlus(cell.textContent)) {
        cell.classList.add('table-cell-plus');
        console.log('works fine')
    }
}

const containsPlus = (text) => /\+/.test(text);

addClassToPlusCellsInFigures();