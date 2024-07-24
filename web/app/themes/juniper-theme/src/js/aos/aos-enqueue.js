// noinspection JSUnresolvedReference

const aosInit = () => {
    document.getElementsByClassName('aos').toConnectedArray().forEach((element) => {
        console.log(element)

        Array.from(element.classList)
            .filter((className) => className.includes('aos') && className !== 'aos')
            .map((className) => className.replace('aos-', ''))
            .forEach((aosData) => element.dataset.aos = aosData)
    })

    AOS.init({once: true});
}

addEventListener('DOMContentLoaded', aosInit)

jQuery(document).on('filterRefreshRenderedElements', function() {
    AOS.refresh();
})