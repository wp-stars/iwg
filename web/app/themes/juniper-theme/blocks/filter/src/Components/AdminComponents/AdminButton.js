import React, {useEffect, useState} from "react";
import axios from "axios";

export default function AdminButton(data) {

    const onClick = data.onClick ?? (() => {})

    const [currentlyLoading, setCurrentlyLoading] = useState(false)
    const [buttonClassList, setButtonClassList] = useState(['btn', 'transition-all'])

    async function onClickContainer() {
        setCurrentlyLoading(true)
        await onClick()
        setCurrentlyLoading(false)
    }

    useEffect(() => {
        if (currentlyLoading) {
            setButtonClassList((oldClassList) => {
                const newClassList = oldClassList
                    .join(' ')
                    .replace('btn-black', '')
                    .replace('text-white', '')
                    .split(' ')

                newClassList.push('btn-accent', 'text-black')
                return newClassList
            })
        } else {
            const newClassList = buttonClassList
                .join(' ')
                .replace('btn-accent', '')
                .replace('text-black', '')
                .split(' ')

            newClassList.push('btn-black', 'text-white')

            setButtonClassList(newClassList)
        }
    }, [currentlyLoading]);

    return (
        <button className={buttonClassList.join(' ')} disabled={currentlyLoading} onClick={onClickContainer}>
            {data.children}
        </button>
    )

}