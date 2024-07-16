import React from "react";

import ReactDOM from 'react-dom';
import FilterComponent from "./Components/FilterComponent";

const setupFilters = () => {
    const filterNew = document.querySelectorAll('.filter-block')
    filterNew.forEach(div => {
        let data = JSON.parse(div.dataset.initialData);
        const root = ReactDOM.createRoot(div)
        root.render(<FilterComponent {...data} />);
        div.classList.remove('filter-block')
        div.classList.remove('hidden')
    })
};

document.addEventListener('DOMContentLoaded', setupFilters);