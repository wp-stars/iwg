const scrollstyle = () => {
    const scrollstyleClass = 'scrollstyle'
    const scrollstyleTransitionStyle = 'all ease-out 800ms 200ms'

    const isScrollstyleClassName = (className) => className.includes(scrollstyleClass) && className !== scrollstyleClass
    const removeBaseScrollstyleClassFromClassName = (className) => className.replace(scrollstyleClass + "-", '')

    const containers = document.getElementsByClassName(scrollstyleClass).toConnectedArray()

    class SavedComputedStyle {
        name
        value
        className

        constructor(name, value, className = '') {
            this.name = name;
            this.value = value;
            this.className = className;
        }
    }

    function generateRandomScrollstyleId() {
        return "scrollstyle-" + Math.floor(Math.random() * 1000 - 1)
    }

    function isScrolledIntoView(element) {
        const boundingClientRect = element.getBoundingClientRect();
        const top = boundingClientRect.top;
        const bottom = boundingClientRect.bottom;

        return top < window.innerHeight && bottom >= 0;
    }

    let lastDebounce = Date.now()
    const lastCheckCache = {}
    /**
     * @param element { Node|HTMLElement }
     * @param debounceTimer
     * @return {*}
     */
    function isScrolledIntoViewDebounce(element, debounceTimer = 1000)  {
        lastDebounce = isDefined(lastDebounce) ? lastDebounce : Date.now()
        const checkAllowed = lastDebounce + debounceTimer < Date.now()

        if(!isDefined(element.dataset.scrollIdentifier)) {
            element.dataset.scrollIdentifier = element.id ?? uuidv4()
        }

        const key = element.dataset.scrollIdentifier

        lastCheckCache[key] = checkAllowed
            ? isScrolledIntoView(element)
            : lastCheckCache[key]

        return lastCheckCache[key]
    }

    /** @param element {HTMLElement} */
    function applyScrollstyle(element) {
        const computedStyles = getComputedStyle(element)

        const computedStylesBefore = getComputedStyle(element, '::before')
        const computedStylesAfter = getComputedStyle(element, '::after')

        // add general transition styling
        element.style.transition = scrollstyleTransitionStyle

        // apply transition styling
        const classList = Array.from(element.classList)

        // apply to :before pseudo element
        const beforeClasses = classList
            .filter(isScrollstyleClassName)
            .filter((className) => className.includes('before'))

        beforeClasses
            .map(removeBaseScrollstyleClassFromClassName)
            .map((className) => className.replace('before-', ''))
            .map((styleName) => new SavedComputedStyle(styleName, computedStylesBefore[styleName], `${scrollstyleClass}-before-${styleName}`))
            .forEach((savedStyle) => applyVisibilityReactionToSavedClass(element, savedStyle, 'before'))

        // apply to :after pseudo element
        const afterClasses = classList
            .filter(isScrollstyleClassName)
            .filter((classList) => classList.includes('after'))

        afterClasses
            .map(removeBaseScrollstyleClassFromClassName)
            .map((className) => className.replace('after-', ''))
            .map((styleName) => new SavedComputedStyle(styleName, computedStylesAfter[styleName], `${scrollstyleClass}-after-${styleName}`))
            .forEach((savedStyle) => applyVisibilityReactionToSavedClass(element, savedStyle, 'after'))

        // apply to base element
        classList
            .filter((className) => afterClasses.includes(className) || beforeClasses.includes(className))
            .filter(isScrollstyleClassName)
            .map(removeBaseScrollstyleClassFromClassName)
            .map((styleName) => new SavedComputedStyle(styleName, computedStyles[styleName]))
            .forEach((savedStyle) => applyVisibilityReaction(element, savedStyle))
    }

    // scrollstyle-<style>
    containers.forEach(applyScrollstyle)

    function applyVisibilityReactionToSavedClass(element, savedStyle, pseudo) {
        const elementId = element.id === '' ? 'autoscroll-' + generateRandomScrollstyleId() : element.id
        console.log(elementId)
        element.id = elementId

        const transitionRule = `#${elementId}::${pseudo} { transition: ${scrollstyleTransitionStyle}; }`
        const initialRule = `#${elementId}::${pseudo} { ${savedStyle.name}: 0px !important; }`

        let wasAlreadyVisible = false

        const styleSheet = document.styleSheets[0]

        styleSheet.insertRule(transitionRule, 0)
        document.styleSheets[0].insertRule(initialRule, 0)

        const reaction = () => {
            // seperated due to expensive scrollIntoViewFunction
            if (wasAlreadyVisible) {
                return
            }

            if (!isScrolledIntoViewDebounce(element)) {
                return
            }

            wasAlreadyVisible = true

            const removalRuleId = Array.from(styleSheet.cssRules).findIndex((rule) => {
                return rule.cssText === initialRule
            })

            console.log(styleSheet.cssRules[removalRuleId])

            styleSheet.deleteRule(removalRuleId)
            document.removeEventListener('scroll', reaction)
        }

        document.addEventListener('scroll', reaction)
        reaction()
    }

    function applyVisibilityReaction(element, savedStyle) {
        element.style[savedStyle.name] = 'initial'
        let wasAlreadyVisible = false

        const reaction = () => {
            // seperated due to expensive scrollIntoViewFunction
            if (wasAlreadyVisible) {
                return
            }

            if (!isScrolledIntoViewDebounce(element)) {
                return
            }

            wasAlreadyVisible = true
            element.style[savedStyle.name] = savedStyle.value
        }

        document.addEventListener('scroll', reaction)
        reaction()
    }
}

addEventListener('DOMContentLoaded', scrollstyle)